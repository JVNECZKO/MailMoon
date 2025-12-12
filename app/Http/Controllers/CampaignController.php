<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Models\Campaign;
use App\Models\ContactList;
use App\Models\SendingIdentity;
use App\Models\Template;
use App\Services\CampaignSenderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(private CampaignSenderService $senderService)
    {
        $this->middleware('auth');
        $this->authorizeResource(Campaign::class, 'campaign');
    }

    public function index(Request $request): View
    {
        $campaigns = $request->user()->campaigns()
            ->with(['sendingIdentity', 'contactList'])
            ->withCount([
                'messages as sent_messages_count' => fn ($query) => $query->whereNotNull('sent_at'),
                'messages as unique_opens_count' => fn ($query) => $query->where('open_count', '>', 0),
                'messages as unique_clicks_count' => fn ($query) => $query->where('click_count', '>', 0),
                'messages as unsubscribes_count' => fn ($query) => $query->whereNotNull('unsubscribe_at'),
            ])
            ->latest()
            ->paginate(15);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        $sendingIdentities = $user->sendingIdentities()->get();
        $contactLists = $user->contactLists()->get();
        $templates = $user->templates()->get();

        $campaign = new Campaign([
            'track_opens' => true,
            'track_clicks' => true,
            'enable_unsubscribe' => true,
            'send_interval_seconds' => 1,
        ]);

        if ($request->filled('template_id')) {
            $selectedTemplate = $templates->firstWhere('id', (int) $request->integer('template_id'));
            if ($selectedTemplate) {
                $campaign->template_id = $selectedTemplate->id;
                $campaign->subject = $selectedTemplate->subject;
                $campaign->html_content = $selectedTemplate->html_content;
            }
        }

        return view('campaigns.create', compact('campaign', 'sendingIdentities', 'contactLists', 'templates'));
    }

    public function store(CampaignRequest $request): RedirectResponse
    {
        $this->ensureUserOwnsResources($request);

        $data = $this->prepareData($request);
        $campaign = $request->user()->campaigns()->create($data);

        return $this->handleAction($request, $campaign, 'zapisana');
    }

    public function show(Campaign $campaign): View
    {
        $campaign->load(['sendingIdentity', 'contactList', 'template']);
        $stats = $this->campaignStats($campaign);

        return view('campaigns.show', compact('campaign', 'stats'));
    }

    public function edit(Campaign $campaign, Request $request): View
    {
        $user = $request->user();
        $sendingIdentities = $user->sendingIdentities()->get();
        $contactLists = $user->contactLists()->get();
        $templates = $user->templates()->get();

        return view('campaigns.edit', compact('campaign', 'sendingIdentities', 'contactLists', 'templates'));
    }

    public function update(CampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $this->ensureUserOwnsResources($request);

        $campaign->update($this->prepareData($request));

        return $this->handleAction($request, $campaign, 'zaktualizowana');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('status', 'Kampania została usunięta.');
    }

    public function sendNow(Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        return $this->sendCampaign($campaign);
    }

    private function ensureUserOwnsResources(Request $request): void
    {
        $userId = $request->user()->id;

        abort_unless(
            SendingIdentity::where('user_id', $userId)->where('id', $request->input('sending_identity_id'))->exists(),
            403
        );

        abort_unless(
            ContactList::where('user_id', $userId)->where('id', $request->input('contact_list_id'))->exists(),
            403
        );

        if ($request->filled('template_id')) {
            abort_unless(
                Template::where('user_id', $userId)->where('id', $request->input('template_id'))->exists(),
                403
            );
        }
    }

    private function prepareData(CampaignRequest $request): array
    {
        $data = $request->validated();

        $extraSubjects = collect($request->input('extra_subjects', []))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
        $extraContents = collect($request->input('extra_contents', []))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        if (!empty($extraSubjects) && empty($data['subject'])) {
            $data['subject'] = array_shift($extraSubjects);
        }
        if (!empty($extraContents) && empty($data['html_content'])) {
            $data['html_content'] = array_shift($extraContents);
        }

        $data['extra_subjects'] = $extraSubjects;
        $data['extra_contents'] = $extraContents;

        $data['track_opens'] = $request->boolean('track_opens');
        $data['track_clicks'] = $request->boolean('track_clicks');
        $data['enable_unsubscribe'] = $request->boolean('enable_unsubscribe');
        $data['send_interval_seconds'] = (int) $request->input('send_interval_seconds', 1);
        $data['send_interval_max_seconds'] = (int) $request->input('send_interval_max_seconds', $data['send_interval_seconds']);
        $data['scheduled_at'] = $request->input('scheduled_at') ?: null;
        $data['template_id'] = $data['template_id'] ?? null;
        $data['status'] = 'draft';
        $schedule = $request->input('sending_window_schedule', []);
        $hasSchedule = $this->hasScheduleEnabled($schedule);
        $data['sending_window_enabled'] = $request->boolean('sending_window_enabled') || $hasSchedule;
        $data['sending_window_start'] = $request->input('sending_window_start') ?: null;
        $data['sending_window_end'] = $request->input('sending_window_end') ?: null;
        $data['sending_window_schedule'] = $this->cleanSchedule($schedule);

        return $data;
    }

    private function cleanSchedule(array $schedule): array
    {
        $clean = [];
        foreach ($schedule as $day => $config) {
            $enabled = filter_var($config['enabled'] ?? false, FILTER_VALIDATE_BOOL);
            $start = $config['start'] ?? null;
            $end = $config['end'] ?? null;

            if ($enabled && $start && $end) {
                $clean[$day] = [
                    'enabled' => true,
                    'start' => $start,
                    'end' => $end,
                ];
            } else {
                $clean[$day] = [
                    'enabled' => false,
                    'start' => null,
                    'end' => null,
                ];
            }
        }

        return $clean;
    }

    private function hasScheduleEnabled(array $schedule): bool
    {
        foreach ($schedule as $config) {
            if (filter_var($config['enabled'] ?? false, FILTER_VALIDATE_BOOL)) {
                return true;
            }
        }

        return false;
    }

    private function handleAction(Request $request, Campaign $campaign, string $fallbackMessage): RedirectResponse
    {
        $action = $request->input('action', 'draft');

        if ($action === 'schedule') {
            $campaign->update([
                'status' => 'scheduled',
                'scheduled_at' => $request->input('scheduled_at') ?: now(),
            ]);

            return redirect()->route('campaigns.index')->with('status', 'Kampania została zaplanowana.');
        }

        if ($action === 'send_now') {
            return $this->sendCampaign($campaign);
        }

        $campaign->update([
            'status' => 'draft',
            'scheduled_at' => null,
        ]);

        return redirect()->route('campaigns.index')->with('status', "Kampania {$fallbackMessage} jako szkic.");
    }

    private function sendCampaign(Campaign $campaign): RedirectResponse
    {
        if (!$campaign->sendingIdentity?->is_active) {
            return redirect()->back()->with('error', 'Aktywna tożsamość nadawcy jest wymagana do wysyłki.');
        }

        // oznacz jako scheduled; cron / artisan rozbije wysyłkę na małe batch'e, żeby nie timeoutować
        $campaign->update([
            'status' => 'scheduled',
            'scheduled_at' => now(),
        ]);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('status', 'Wysyłka ruszy w tle (cron co minutę). Odśwież za chwilę podgląd kampanii.');
    }

    private function campaignStats(Campaign $campaign): array
    {
        $messages = $campaign->messages();

        $total = $messages->count();
        $sent = (clone $messages)->whereNotNull('sent_at')->count();
        $totalOpens = (clone $messages)->sum('open_count');
        $uniqueOpens = (clone $messages)->where('open_count', '>', 0)->count();
        $totalClicks = (clone $messages)->sum('click_count');
        $uniqueClicks = (clone $messages)->where('click_count', '>', 0)->count();
        $unsubscribes = (clone $messages)->whereNotNull('unsubscribe_at')->count();

        $openRate = $sent > 0 ? round(($uniqueOpens / $sent) * 100, 1) : 0;
        $clickRate = $sent > 0 ? round(($uniqueClicks / $sent) * 100, 1) : 0;
        $unsubscribeRate = $sent > 0 ? round(($unsubscribes / $sent) * 100, 1) : 0;

        return [
            'total' => $total,
            'sent' => $sent,
            'total_opens' => $totalOpens,
            'unique_opens' => $uniqueOpens,
            'total_clicks' => $totalClicks,
            'unique_clicks' => $uniqueClicks,
            'unsubscribes' => $unsubscribes,
            'open_rate' => $openRate,
            'click_rate' => $clickRate,
            'unsubscribe_rate' => $unsubscribeRate,
        ];
    }
}

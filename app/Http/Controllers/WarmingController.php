<?php

namespace App\Http\Controllers;

use App\Models\ContactList;
use App\Models\SendingIdentity;
use App\Models\Warming;
use App\Services\WarmingSenderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WarmingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(SendingIdentity $sendingIdentity): View
    {
        $this->authorize('view', $sendingIdentity);

        $warming = $sendingIdentity->warming;
        $contactLists = $sendingIdentity->user->contactLists()->get();

        $schedule = $warming?->schedule ?? [];
        $progress = $this->progress($warming);

        return view('warming.show', compact('sendingIdentity', 'warming', 'contactLists', 'schedule', 'progress'));
    }

    public function start(Request $request, SendingIdentity $sendingIdentity): RedirectResponse
    {
        $this->authorize('update', $sendingIdentity);

        $data = $request->validate([
            'contact_list_id' => ['required', 'exists:contact_lists,id'],
            'plan' => ['required', 'in:slow,standard,fast'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'send_interval_seconds' => ['required', 'integer', 'min:5'],
        ]);

        $schedule = $this->planSchedule($data['plan']);

        // ensure contact list belongs to user
        abort_unless($sendingIdentity->user_id === ContactList::findOrFail($data['contact_list_id'])->user_id, 403);

        $warming = Warming::updateOrCreate(
            [
                'sending_identity_id' => $sendingIdentity->id,
                'user_id' => $sendingIdentity->user_id,
            ],
            [
                'contact_list_id' => $data['contact_list_id'],
                'plan' => $data['plan'],
                'status' => 'running',
                'day_current' => 1,
                'day_total' => count($schedule),
                'daily_target' => $schedule[0] ?? 0,
                'schedule' => $schedule,
                'subject' => $data['subject'],
                'body' => $data['body'],
                'send_interval_seconds' => $data['send_interval_seconds'],
                'sent_today' => 0,
                'total_sent' => 0,
                'started_at' => now(),
                'paused_at' => null,
                'finished_at' => null,
            ]
        );

        return back()->with('status', 'Warming uruchomiony (plan: ' . ucfirst($data['plan']) . ').');
    }

    public function pause(SendingIdentity $sendingIdentity): RedirectResponse
    {
        $this->authorize('update', $sendingIdentity);
        $warming = $sendingIdentity->warming;
        if ($warming) {
            $warming->update(['status' => 'paused', 'paused_at' => now()]);
        }

        return back()->with('status', 'Warming został wstrzymany.');
    }

    public function resume(SendingIdentity $sendingIdentity): RedirectResponse
    {
        $this->authorize('update', $sendingIdentity);
        $warming = $sendingIdentity->warming;
        if ($warming) {
            $warming->update(['status' => 'running', 'paused_at' => null]);
        }

        return back()->with('status', 'Warming został wznowiony.');
    }

    public function finish(SendingIdentity $sendingIdentity): RedirectResponse
    {
        $this->authorize('update', $sendingIdentity);
        $warming = $sendingIdentity->warming;
        if ($warming) {
            $warming->update(['status' => 'finished', 'finished_at' => now()]);
        }

        return back()->with('status', 'Warming zakończony.');
    }

    public function run(WarmingSenderService $service): RedirectResponse
    {
        $summary = $service->run();
        return back()->with('status', 'Uruchomiono warming: ' . json_encode($summary));
    }

    private function planSchedule(string $plan): array
    {
        return match ($plan) {
            'slow' => [5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,110,120,130,140,150,160,170,180,190,200],
            'fast' => [20,30,40,50,60,80,100,120,140,160,180,200,220,240,260,280,300,320,340,360],
            default => [10,20,30,40,50,60,70,80,90,100,110,120,130,140,150,160,170,180,190,200],
        };
    }

    private function progress(?Warming $warming): array
    {
        if (!$warming) {
            return ['percent' => 0, 'current' => 0, 'total' => 0];
        }

        $percent = min(100, round(($warming->day_current - 1) / max(1, $warming->day_total) * 100, 1));

        return [
            'percent' => $percent,
            'current' => $warming->day_current,
            'total' => $warming->day_total,
        ];
    }
}

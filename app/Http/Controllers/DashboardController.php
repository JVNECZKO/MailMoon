<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $campaignCount = $user->campaigns()->count();
        $contactsCount = $user->contacts()->count();
        $templatesCount = $user->templates()->count();
        $hasActiveIdentity = $user->sendingIdentities()->where('is_active', true)->exists();

        $lastCampaign = $user->campaigns()->latest()->first();
        $lastCampaignStats = null;

        if ($lastCampaign) {
            $lastCampaign->loadCount([
                'messages as sent_messages_count' => fn ($query) => $query->whereNotNull('sent_at'),
                'messages as unique_opens_count' => fn ($query) => $query->where('open_count', '>', 0),
                'messages as unique_clicks_count' => fn ($query) => $query->where('click_count', '>', 0),
                'messages as unsubscribes_count' => fn ($query) => $query->whereNotNull('unsubscribe_at'),
            ]);

            $lastCampaignStats = [
                'sent' => $lastCampaign->sent_messages_count,
                'unique_opens' => $lastCampaign->unique_opens_count,
                'unique_clicks' => $lastCampaign->unique_clicks_count,
                'unsubscribes' => $lastCampaign->unsubscribes_count,
            ];
        }

        return view('dashboard', compact(
            'campaignCount',
            'contactsCount',
            'templatesCount',
            'hasActiveIdentity',
            'lastCampaign',
            'lastCampaignStats'
        ));
    }
}

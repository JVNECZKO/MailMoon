<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function cron(Request $request)
    {
        $cronUrl = Cache::remember('cron_signed_url', now()->addYears(3), function () {
            return URL::temporarySignedRoute(
                'cron.send-due',
                now()->addYears(3)
            );
        });

        $curlSnippet = "curl -s \"{$cronUrl}\" > /dev/null 2>&1";
        $cronLine = "* * * * * {$curlSnippet}";

        return view('settings.cron', compact('cronUrl', 'curlSnippet', 'cronLine'));
    }

    public function regenerateCron(Request $request)
    {
        Cache::forget('cron_signed_url');

        return redirect()->route('settings.cron')->with('status', 'Wygenerowano nowy link cron.');
    }
}

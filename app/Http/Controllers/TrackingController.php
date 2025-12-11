<?php

namespace App\Http\Controllers;

use App\Models\CampaignMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackingController extends Controller
{
    public function open(Request $request, CampaignMessage $message, string $token): Response
    {
        if ($message->unsubscribe_token && $message->unsubscribe_token !== $token) {
            abort(403);
        }

        $message->increment('open_count');

        if (!$message->first_open_at) {
            $message->forceFill(['first_open_at' => now()])->save();
        }

        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==');

        return response($pixel, 200, ['Content-Type' => 'image/png']);
    }

    public function click(Request $request, CampaignMessage $message, string $token): RedirectResponse
    {
        if ($message->unsubscribe_token && $message->unsubscribe_token !== $token) {
            abort(403);
        }

        $message->update([
            'click_count' => $message->click_count + 1,
            'last_click_at' => now(),
        ]);

        $url = $request->query('url', '/');

        return redirect()->away($url);
    }

    public function unsubscribe(string $token)
    {
        $message = CampaignMessage::where('unsubscribe_token', $token)->firstOrFail();

        if (!$message->unsubscribe_at) {
            $message->update(['unsubscribe_at' => now()]);
        }

        return view('tracking.unsubscribe', compact('message'));
    }
}

<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactImportController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SendingIdentityController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WarmingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('sending-identities', SendingIdentityController::class);
    Route::post('sending-identities/{sending_identity}/test', [SendingIdentityController::class, 'test'])->name('sending-identities.test');
    Route::resource('contact-lists', ContactListController::class);
    Route::resource('contact-lists.contacts', ContactController::class)->scoped();
    Route::post('contact-lists/{contact_list}/import', [ContactImportController::class, 'store'])->name('contact-lists.import');

    Route::resource('templates', TemplateController::class);
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/send-now', [CampaignController::class, 'sendNow'])->name('campaigns.send-now');
    Route::post('campaigns/{campaign}/pause', [CampaignController::class, 'pause'])->name('campaigns.pause');
    Route::post('campaigns/{campaign}/resume', [CampaignController::class, 'resume'])->name('campaigns.resume');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings/cron', [SettingsController::class, 'cron'])->name('settings.cron');
    Route::post('/settings/cron/regenerate', [SettingsController::class, 'regenerateCron'])->name('settings.cron.regenerate');

    Route::get('/sending-identities/{sending_identity}/warming', [WarmingController::class, 'show'])->name('warming.show');
    Route::post('/sending-identities/{sending_identity}/warming/start', [WarmingController::class, 'start'])->name('warming.start');
    Route::post('/sending-identities/{sending_identity}/warming/pause', [WarmingController::class, 'pause'])->name('warming.pause');
    Route::post('/sending-identities/{sending_identity}/warming/resume', [WarmingController::class, 'resume'])->name('warming.resume');
    Route::post('/sending-identities/{sending_identity}/warming/finish', [WarmingController::class, 'finish'])->name('warming.finish');
});

Route::get('/track/open/{message}/{token}', [TrackingController::class, 'open'])->name('tracking.open');
Route::get('/c/{message}/{token}', [TrackingController::class, 'click'])->name('tracking.click');
Route::get('/u/{token}', [TrackingController::class, 'unsubscribe'])->name('tracking.unsubscribe');

Route::get('/cron/send-due', function (
    \App\Services\CampaignSenderService $senderService,
    \App\Services\WarmingSenderService $warmingSenderService
) {
    $due = \App\Models\Campaign::where('status', 'scheduled')
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '<=', now())
        ->get();

    $summary = [];

    foreach ($due as $campaign) {
        try {
            // wysyłamy po 1 wiadomości na wywołanie crona, z opóźnieniem ustawianym w scheduled_at
            $summary[$campaign->id] = $senderService->send($campaign, 1, true);
        } catch (\Throwable $e) {
            $campaign->update(['status' => 'failed']);
            \Illuminate\Support\Facades\Log::error('Cron send-due failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
            ]);
            $summary[$campaign->id] = ['error' => $e->getMessage()];
        }
    }

    $warming = $warmingSenderService->run();

    return response()->json([
        'status' => 'ok',
        'processed' => $due->count(),
        'summary' => $summary,
        'warming' => $warming,
    ]);
})->name('cron.send-due')->middleware('signed');

require __DIR__ . '/auth.php';

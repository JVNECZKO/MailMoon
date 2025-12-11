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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings/cron', [SettingsController::class, 'cron'])->name('settings.cron');
    Route::post('/settings/cron/regenerate', [SettingsController::class, 'regenerateCron'])->name('settings.cron.regenerate');
});

Route::get('/track/open/{message}/{token}', [TrackingController::class, 'open'])->name('tracking.open');
Route::get('/c/{message}/{token}', [TrackingController::class, 'click'])->name('tracking.click');
Route::get('/u/{token}', [TrackingController::class, 'unsubscribe'])->name('tracking.unsubscribe');

Route::get('/cron/send-due', function (\App\Services\CampaignSenderService $senderService) {
    $due = \App\Models\Campaign::where('status', 'scheduled')
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '<=', now())
        ->get();

    $summary = [];

    foreach ($due as $campaign) {
        try {
            $summary[$campaign->id] = $senderService->send($campaign);
        } catch (\Throwable $e) {
            $campaign->update(['status' => 'failed']);
            $summary[$campaign->id] = ['error' => $e->getMessage()];
        }
    }

    return response()->json([
        'status' => 'ok',
        'processed' => $due->count(),
        'summary' => $summary,
    ]);
})->name('cron.send-due')->middleware('signed');

require __DIR__ . '/auth.php';

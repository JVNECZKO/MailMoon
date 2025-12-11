<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\CampaignSenderService;
use Illuminate\Console\Command;

class SendCampaignNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:send-now {campaign_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ręczne uruchomienie wysyłki konkretnej kampanii';

    public function __construct(private CampaignSenderService $senderService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $campaignId = $this->argument('campaign_id');
        $campaign = Campaign::find($campaignId);

        if (! $campaign) {
            $this->error('Nie znaleziono kampanii o ID '.$campaignId);

            return self::FAILURE;
        }

        try {
            $result = $this->senderService->send($campaign);
            $this->info("Wysłano: {$result['sent']}, błędy: {$result['failed']}");
        } catch (\Throwable $e) {
            $campaign->update(['status' => 'failed']);
            $this->error('Błąd wysyłki: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}

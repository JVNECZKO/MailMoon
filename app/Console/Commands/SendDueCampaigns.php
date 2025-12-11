<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\CampaignSenderService;
use Illuminate\Console\Command;

class SendDueCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:send-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wyślij wszystkie zaplanowane kampanie z czasem <= teraz';

    public function __construct(private CampaignSenderService $senderService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dueCampaigns = Campaign::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($dueCampaigns->isEmpty()) {
            $this->info('Brak kampanii do wysłania.');

            return self::SUCCESS;
        }

        foreach ($dueCampaigns as $campaign) {
            $this->info("Wysyłam kampanię #{$campaign->id} ({$campaign->name})");

            try {
                $result = $this->senderService->send($campaign);
                $this->info("Wysłano: {$result['sent']}, błędy: {$result['failed']}");
            } catch (\Throwable $e) {
                $campaign->update(['status' => 'failed']);
                $this->error("Błąd: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}

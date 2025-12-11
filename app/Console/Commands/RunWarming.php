<?php

namespace App\Console\Commands;

use App\Services\WarmingSenderService;
use Illuminate\Console\Command;

class RunWarming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warming:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uruchamia rozgrzewanie (warming) dla aktywnych tożsamości';

    /**
     * Execute the console command.
     */
    public function handle(WarmingSenderService $service): int
    {
        $summary = $service->run();
        $this->info('Warming summary: ' . json_encode($summary));

        return self::SUCCESS;
    }
}

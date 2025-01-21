<?php

namespace App\Console\Commands;

use App\Services\BitrixService;
use App\Services\DataCollectorService;
use App\Services\OpenAIService;
use Illuminate\Console\Command;

class DailyTaskAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:daily-automation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ежедневная автоматизация задач';

    protected $dataCollector;
    protected $openAIService;
    protected $bitrixService;

    public function __construct(
        DataCollectorService $dataCollector,
        OpenAIService $openAIService,
        BitrixService $bitrixService
    )
    {
        parent::__construct();
        $this->dataCollector = $dataCollector;
        $this->openAIService = $openAIService;
        $this->bitrixService = $bitrixService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = $this->dataCollector->collectData();
        $analysis = $this->openAIService->analyzeData($data);
        $this->bitrixService->processRecommendation($analysis['recommendations']);

        $this->info('Задачи обработаны!');
    }
}

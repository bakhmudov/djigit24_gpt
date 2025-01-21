<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitrixService
{
    private $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = env('BITRIX_WEBHOOK_URL');
    }

    public function processRecommendation($recommendations): void
    {
        foreach ($recommendations as $recommendation) {
            if ($recommendation['action'] === 'create_task') {
                $this->createTask($recommendation);
            }
        }
    }

    /**
     * Создание задачи в Битриксе
     *
     * @param array $taskData
     * @return void
     */
    private function createTask(array $taskData): void
    {
        $response = Http::post($this->webhookUrl . 'tasks.task.add', [
            'fields' => [
                'TITLE' => $taskData['title'],
                'DESCRIPTION' => $taskData['description'],
                'RESPONSIBLE_ID' => $taskData['responsible_id'],
                'DEADLINE' => $taskData['deadline'],
                'PRIORITY' => $taskData['priority'],
            ],
        ]);

        // Логгирование ошибки
        Log::error('Ошибка при создании задачи в Битрикс.', [
            'response' => $response->body(),
        ]);

        $response->json();
    }
}

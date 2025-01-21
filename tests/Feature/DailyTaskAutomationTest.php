<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Services\DataCollectorService;
use App\Services\OpenAIService;

class DailyTaskAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function testDataCollectorService()
    {
        // Инициализация сервиса
        $service = app(DataCollectorService::class);

        // Вызов метода collectData
        $data = $service->collectData();

        // Проверка результата
        $this->assertIsArray($data, 'Data should be an array');
        $this->assertArrayHasKey('departments', $data, 'Data should contain departments');
        $this->assertArrayHasKey('bx_users', $data, 'Data should contain users');
        $this->assertArrayHasKey('tasks', $data, 'Data should contain tasks');
    }

    /**
     * @throws ConnectionException
     */
    public function testOpenAIService()
    {
        // Инициализация сервиса
        $service = app(OpenAIService::class);

        // Подготовка тестовых данных
        $fakeData = [
            'departments' => [
                ['id' => 1, 'name' => 'IT'],
            ],
            'bx_users' => [
                ['id' => 1, 'name' => 'John Doe'],
            ],
            'tasks' => [
                ['id' => 1, 'title' => 'Test Task', 'priority' => 1],
            ],
        ];

        // Вызов метода analyzeData
        $response = $service->analyzeData($fakeData);

        // Проверка структуры ответа
        $this->assertIsArray($response, 'Response should be an array');
        $this->assertArrayHasKey('choices', $response, 'Response should contain choices');
        $this->assertIsArray($response['choices'], 'Choices should be an array');
    }

    public function testGPTResponseSaving()
    {
        // Подготовка тестовых данных
        $fakeResponse = [
            'prompt' => 'Analyze tasks',
            'response' => 'No issues found.',
        ];

        // Сохранение в базу
        $responseId = DB::table('gpt_responses')->insertGetId([
            'input_data' => json_encode($fakeResponse['prompt']),
            'response_data' => json_encode($fakeResponse['response']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Проверка, что запись сохранилась
        $this->assertDatabaseHas('gpt_responses', [
            'id' => $responseId,
            'input_data' => json_encode($fakeResponse['prompt']),
            'response_data' => json_encode($fakeResponse['response']),
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function testTaskAutomationCycle()
    {
        // Шаг 1: Сбор данных
        $dataCollector = app(DataCollectorService::class);
        $data = $dataCollector->collectData();

        // Шаг 2: Анализ данных
        $openAI = app(OpenAIService::class);
        $response = $openAI->analyzeData($data);

        // Шаг 3: Проверка ответа
        $this->assertArrayHasKey('choices', $response);

        // Шаг 4: Сохранение ответа в базу
        DB::table('gpt_responses')->insert([
            'request' => json_encode($data),
            'response' => json_encode($response),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Шаг 5: Проверка записи в базе
        $this->assertDatabaseHas('gpt_responses', [
            'response' => json_encode($response),
        ]);
    }

}

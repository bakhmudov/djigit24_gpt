<?php

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class OpenAIService
{
    /**
     * Отправляет данные в GPT и сохранят ответ.
     *
     * @param $data
     * @return array|mixed
     * @throws GuzzleException
     */
    public function analyzeData($data): array
    {
        $client = new Client(['verify' => false]);
        $response = $client->post('https://api.aitunnel.ru/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an assistant who analyzes employee tasks at Bitrix24.'],
                    ['role' => 'user', 'content' => 'Here is the data to analyze: ' . json_encode($data)],
                ],
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);

        // Запись в таблицу запроса и ответа
        $this->saveResponse($data, $responseData);

        return $responseData;
    }

    private function saveResponse(array $inputData, array $responseData):void
    {
        DB::table('gpt_responses')->insert([
            'input_data' => json_encode($inputData),
            'response_data' => json_encode($responseData),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

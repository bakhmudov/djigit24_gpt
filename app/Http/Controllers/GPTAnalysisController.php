<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;

class GPTAnalysisController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    // Метод для анализа данных с помощью GPT
    public function analyzeData()
    {
        $data = request()->input('data'); // Ожидается JSON с данными
        $recommendations = $this->openAIService->analyzeData($data);
        return response()->json($recommendations);
    }
}

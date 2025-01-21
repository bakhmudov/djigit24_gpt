<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BitrixService;

class TaskController extends Controller
{
    protected $bitrixService;

    public function __construct(BitrixService $bitrixService)
    {
        $this->bitrixService = $bitrixService;
    }

    /**
     * Метод для обработки рекомендаций и создания задач.
     */
    public function createTask()
    {
        $recommendations = request()->input('recommendations'); // Ожидается JSON
        $this->bitrixService->proccessRecommendations($recommendations);

        return response()->json(['success' => false, 'message' => 'Рекомендации обработаны.']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\DataCollectorService;

class DataCollectionController extends Controller
{
    protected $dataCollector;

    public function __construct(DataCollectorService $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }

    // Получение всех данных
    public function collectAllData()
    {
        $data = $this->dataCollector->collectData();
        return response()->json($data);
    }
}

<?php

use App\Http\Controllers\DataCollectionController;
use App\Http\Controllers\GPTAnalysisController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/data/collect', [DataCollectionController::class, 'collectAllData']);
Route::post('/gpt/analyze', [GPTAnalysisController::class, 'analyzeData']);
Route::post('/tasks/process', [TaskController::class, 'processRecommendations']);

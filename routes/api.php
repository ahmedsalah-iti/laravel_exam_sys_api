<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ExamAttemptController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth.api')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth.api')->get('/me', function (Request $request) {
    return response()->json($request->user());
});

Route::middleware('auth.api')->group(function () {
    // Exam routes (resourceful)
    Route::apiResource('exams', ExamController::class);

    // Question routes (custom)
    Route::post('exams/{exam}/questions', [QuestionController::class, 'store']);
    Route::put('questions/{question}', [QuestionController::class, 'update']);
    Route::delete('questions/{question}', [QuestionController::class, 'destroy']);

    // Exam attempt routes (custom)
    Route::post('exams/{exam}/start', [ExamAttemptController::class, 'start']);
    Route::post('attempts/{examAttempt}/submit', [ExamAttemptController::class, 'submit']);
    Route::get('results', [ExamAttemptController::class, 'results']);
    Route::get('exams/{exam}/results', [ExamAttemptController::class, 'adminResults']);
});

// Catch-all for undefined routes
Route::fallback(function () {
    return response()->json([
        'message' => 'API route not found.'
    ], 404);
});

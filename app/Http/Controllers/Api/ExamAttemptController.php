<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamAttemptController extends Controller
{
    public function start(Request $request, Exam $exam)
    {
        $this->authorizeStudent();
        $attempt = ExamAttempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'started_at' => now(),
        ]);

        return response()->json($attempt);
    }

    public function submit(Request $request, ExamAttempt $examAttempt)
    {
        $this->authorizeStudent();
        $this->authorizeAttempt($examAttempt);
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.choice_id' => 'required|exists:choices,id',
        ]);

        $score = 0;
        $totalQuestions = count($validated['answers']);
        foreach ($validated['answers'] as $answer) {
            $studentAnswer = StudentAnswer::create([
                'exam_attempt_id' => $examAttempt->id,
                'question_id' => $answer['question_id'],
                'choice_id' => $answer['choice_id'],
            ]);

            if ($studentAnswer->choice && $studentAnswer->choice->is_correct) {
                $score++;
            }
        }

        $examAttempt->update([
            'score' => $score,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'attempt' => $examAttempt,
            'score' => $score,
            'total' => $totalQuestions,
        ]);
    }

    public function results()
    {
        $this->authorizeStudent();
        $attempts = ExamAttempt::where('user_id', Auth::id())->with('exam')->get();
        return response()->json($attempts);
    }

    public function adminResults(Request $request, Exam $exam)
    {
        $this->authorizeAdmin();
        $this->authorizeCreator($exam);
        $attempts = ExamAttempt::where('exam_id', $exam->id)->with('user')->get();
        return response()->json($attempts);
    }

    private function authorizeStudent()
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Only students can perform this action');
        }
    }

    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can perform this action');
        }
    }

    private function authorizeAttempt(ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to submit this attempt');
        }
    }

    private function authorizeCreator(Exam $exam)
    {
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'You are not the creator of this exam');
        }
    }
}
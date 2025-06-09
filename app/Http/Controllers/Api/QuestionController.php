<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function store(Request $request, string $id)
    {
        $exam = Exam::findOrFail($id);
        $this->restrictToCreator($exam);
        $validated = $request->validate([
            'question_text' => 'required|string',
            'choices' => 'required|array|min:2',
            'choices.*.choice_text' => 'required|string',
            'choices.*.is_correct' => 'required|boolean',
        ]);

        $question = $exam->questions()->create([
            'question_text' => $validated['question_text'],
        ]);

        foreach ($validated['choices'] as $choice) {
            $question->choices()->create($choice);
        }

        return response()->json($question->load('choices'), 201);
    }
    public function update(Request $request, string $id)
    {
        $this->authorizeAdmin();
        $question = Question::findOrFail($id);
        $this->authorizeCreator($question->exam);
        $validated = $request->validate([
            'question_text' => 'required|string',
            'choices' => 'required|array|min:2',
            'choices.*.choice_text' => 'required|string',
            'choices.*.is_correct' => 'required|boolean',
        ]);

        $question->update(['question_text' => $validated['question_text']]);
        $question->choices()->delete();
        foreach ($validated['choices'] as $choice) {
            $question->choices()->create($choice);
        }

        return response()->json($question->load('choices'));
    }

    public function destroy(Question $question)
    {
        $this->authorizeAdmin();
        $this->authorizeCreator($question->exam);
        $question->delete();
        return response()->json(['message' => 'Question deleted']);
    }

    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can perform this action');
        }
    }

    private function authorizeCreator(Exam $exam)
    {
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'You are not the creator of this exam');
        }
    }
    private function restrictToCreator(Exam $exam)
    {
        if ($exam->created_by !== Auth::id()) {
            abort(403, 'You are not the creator of this exam');
        }
    }
}
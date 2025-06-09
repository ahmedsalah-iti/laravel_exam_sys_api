<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Auth::user()->role === 'student'
            ? Exam::all()
            : Exam::where('created_by', Auth::id())->get();
        return response()->json($exams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'nullable|string',
        ]);

        $exam = Exam::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
        ]);

        return response()->json($exam, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exam = Exam::findOrFail($id);
        if(!$exam){
            return response()->json(['message'=>'exam not found'],404);
        }
        return response()->json($exam->load('questions.choices'),200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorizeAdmin();
        $exam = Exam::findOrFail($id);
        if(!$exam){
            return response()->json(['message'=>'exam not found'],404);
        }
        if ($exam->creator() != Auth::id()){
            return response()->json(['message'=>'you dont have access to this exam'],403);
        }
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'nullable|string',
        ]);

        $exam->update($validated);
        return response()->json($exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorizeAdmin();
        $exam = Exam::findOrFail($id);
        if(!$exam){
            return response()->json(['message'=>'exam not found'],404);
        }
        if ($exam->creator() != Auth::id()){
            return response()->json(['message'=>'you dont have access to this exam'],403);
        }
        $exam->delete();
        return response()->json(['message' => 'Exam deleted']);
    }

    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {

        return response()->json(['message' => 'Only admins can perform this action'],403);
        }
    }
}
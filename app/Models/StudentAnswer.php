<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $fillable = ['exam_attempt_id', 'question_id', 'choice_id'];

    public function examAttempt()
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function choice()
    {
        return $this->belongsTo(Choice::class);
    }
}

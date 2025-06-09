<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['exam_id', 'question_text'];
    public function exam(){
        return $this->belongsTo(Exam::class);
    }
    public function choices(){
        return $this->hasMany(Choice::class);
    }
    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}

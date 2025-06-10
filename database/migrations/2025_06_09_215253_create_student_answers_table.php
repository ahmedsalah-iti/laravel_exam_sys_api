<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();

            // Use Laravel's foreignId() shortcut with onDelete
            $table->foreignId('exam_attempt_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('question_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            $table->foreignId('choice_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};

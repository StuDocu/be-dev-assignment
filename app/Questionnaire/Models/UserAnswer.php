<?php

namespace App\Questionnaire\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    protected $table = 'user_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question_id', 'answered'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

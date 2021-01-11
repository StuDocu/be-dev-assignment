<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QAndA extends Model
{
    protected $table = 'qanda';

    protected $fillable = ['question', 'answer'];

    /**
     * Get the user that owns the Q and A.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'foreign_key');
    }

    /**
     * Get the phone associated with the user.
     */
    public function progress()
    {
        return $this->hasOne(Progress::class);
    }
}
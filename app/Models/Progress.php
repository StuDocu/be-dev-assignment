<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $fillable = ['answered'];

    /**
     * Get the user that owns the phone.
     */
    public function qAndA()
    {
        return $this->belongsTo(QAndA::class);
    }
}
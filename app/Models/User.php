<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['email', 'name', 'password'];

    /**
     * Get the Q and As associated with the user.
     */
    public function qAndAs()
    {
        return $this->hasMany(QAndA::class);
    }
}
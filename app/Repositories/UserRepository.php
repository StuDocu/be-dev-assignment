<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository
{
    /**
     * Retrieves a User from its email
     *
     * @param string $email
     * @return User|null
     */
    public function getByEmail(string $email): ?User
    {
        try {
            $user = User::where('email', '=', $email)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return null;
        }

        return $user;
    }
}
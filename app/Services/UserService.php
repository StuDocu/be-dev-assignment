<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve a user by its email and verify that the password is correct
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function getByEmailAndPassword(string $email, string $password): ?User
    {
        $user = $this->userRepository->getByEmail($email);

        if (!$user || !(Hash::check($password, $user->password))) {
            return null;
        }

        return $user;
    }
}
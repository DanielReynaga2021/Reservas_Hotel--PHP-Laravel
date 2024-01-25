<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function createUser(User $user)
    {
        $user->save();
    }

    public function getUserByEmail(string $email){
        return User::Where("email", $email)->first();
    }
}
<?php

namespace App\Repositories;

use App\Exceptions\DbException;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;

class UserRepository
{
    public function createUser(User $user)
    {
        try {
            $user->save();
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUserByEmail(string $email){
        try {
        return User::Where("email", $email)->first();
        } catch (Exception $e) {
        throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
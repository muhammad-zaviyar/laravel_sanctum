<?php

namespace App\Repository;

use App\Interface\UserInterface;
use App\Models\User;

class UserRepository implements UserInterface
{
    public function findByEmail($email){
        return User::where('email', $email)->first();
    }
}

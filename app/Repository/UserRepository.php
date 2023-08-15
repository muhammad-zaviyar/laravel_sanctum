<?php

namespace App\Repository;

use App\Interface\UserInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{
    public function findByEmail($email){
        return User::where('email', $email)->first();
    }

    public function createUser($request){

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return $user;
    }
}

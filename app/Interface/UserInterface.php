<?php

namespace App\Interface;

interface UserInterface
{
    public function findByEmail($email);

    public function createUser($request);


}

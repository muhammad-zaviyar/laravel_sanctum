<?php

namespace App\Interface;

interface UserInterface
{
    public function findByEmail($email);
}

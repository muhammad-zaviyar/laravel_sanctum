<?php

namespace App\Interface;

interface TaskInterface
{
    function getUserTask();

    function createTask($request);

}

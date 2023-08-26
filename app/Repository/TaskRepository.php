<?php

namespace App\Repository;

use App\Interface\TaskInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskRepository implements TaskInterface
{
    public function getUserTask(){
        $task = Task::where('user_id', Auth::user()->id)->get();
        return $task;
    }

    public function createTask($request){

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority'=> $request->priority,
        ]);

        return $task;
    }

}

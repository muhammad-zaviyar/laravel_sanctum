<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{

    public function index()
    {
        return TaskResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );
    }

    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority'=> $request->priority,
        ]);

        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) :  new TaskResource($task);
    }

    public function update(Request $request, Task $task)
    {
        if(Auth::user()->id !== $task->user_id){
            return error('','You are not authorized to make this request', 403);
        }

        $task->update($request->all());

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();
    }

    private function isNotAuthorized($task){
        if(Auth::user()->id !== $task->user_id){
            return error('','You are not authorized to make this request', 403);
        }
    }
}

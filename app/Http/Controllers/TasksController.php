<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repository\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TasksController extends Controller
{

    public $taskRepository;

    public function __construct(TaskRepository $taskRepository) {
        $this->taskRepository = $taskRepository;
    }

    public function index()
    {
        try {

            $task = $this->taskRepository->getUserTask();
            return TaskResource::collection($task);

        } catch (\Exception $error) {
            $message  = $error->getMessage();
            return apiResponse(null, 'failed', $message, 500);
        }

    }

    public function store(StoreTaskRequest $request)
    {
        try {

            $task = $this->taskRepository->createTask($request);

            return new TaskResource($task);

        } catch (\Exception $error) {

            $message  = $error->getMessage();
            return apiResponse(null, 'failed', $message, 500);

        }


    }

    public function show(Task $task)
    {
        try {

            $is_authorized = $this->isNotAuthorized($task);
            return $is_authorized ? $is_authorized :  new TaskResource($task);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $notFoundException) {

            $message = 'The requested task was not found.';
            return apiResponse(null, 'failed', $message, 404);

        } catch (\Exception $error) {

            $message  = $error->getMessage();
            return apiResponse(null, 'failed', $message, 500);

        }

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
            $message = 'You are not authorized to make this request';
            return apiResponse(null, 'failed', $message, 403);
        }
    }
}

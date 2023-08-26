<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repository\TaskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TasksController extends Controller
{

    public $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function index()
    {
        try {

            $task = $this->taskRepository->getUserTask();
            $data =  TaskResource::collection($task);

            return apiResponse($data, 'success', 'All task');

        } catch (\Exception $error) {

            $message  = $error->getMessage();
            return apiResponse(null, 'failed', $message, 500);

        }
    }

    public function store(StoreTaskRequest $request)
    {
        try {

            $task = $this->taskRepository->createTask($request);

            $data =  new TaskResource($task);

            return apiResponse($data, 'success', 'Task created Successfully');

        } catch (\Exception $error) {

            $message  = $error->getMessage();
            return apiResponse(null, 'failed', $message, 500);

        }
    }

    public function show(Task $task)
    {
        $is_authorized = $this->isNotAuthorized($task);

        if(!$is_authorized){
            $data = new TaskResource($task);
            return apiResponse($data, 'success', 'One Task');
        }

        return $is_authorized;
    }

    public function update(Request $request, Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {

            return apiResponse(null, 'failed', 'You are not authorized to make this request', 403);

        }

        $task->update($request->all());

        $data = new TaskResource($task);

        return apiResponse($data, 'success', 'Updated Successfully');

    }

    public function destroy(Task $task)
    {
        $is_authorized = $this->isNotAuthorized($task);

        if(!$is_authorized){
            $data = $task->delete();
            return apiResponse($data, 'success', 'Deleted Successfully');
        }
        
        return $this->isNotAuthorized($task);
    }

    private function isNotAuthorized($task)
    {
        if (Auth::user()->id !== $task->user_id) {
            $message = 'You are not authorized to make this request';
            return apiResponse(null, 'failed', $message, 500);
        }
    }
}

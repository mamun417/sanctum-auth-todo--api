<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    const PAGINATION_LIMIT = 5;

    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $search = request('search');

        $tasksQuery = $this->taskService->getTasksQuery($search);

        $tasks = $tasksQuery->paginate(self::PAGINATION_LIMIT);

        $tasks = TaskResource::collection($tasks);

        return $this->success('Tasks retrieved successfully.', $tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask($request->only(['title', 'body']));
        $task = new TaskResource($task);

        return $this->success('Task created successfully.', ['data' => $task], HttpStatusCodes::CREATED);
    }

    public function show($id)
    {
        $task = Task::query()->forUser()->find($id);
        if (!$task) {
            return $this->error('Task not found.', HttpStatusCodes::NOT_FOUND);
        }

        return $this->success('Task retrieved successfully.', ['data' => $task]);
    }

    public function update(StoreTaskRequest $request, $id)
    {
        $task = $this->taskService->updateTask($id, $request->only(['title', 'body']));

        if (!$task) {
            return $this->error('Task not found.', HttpStatusCodes::NOT_FOUND);
        }

        $task = new TaskResource($task);

        return $this->success('Task updated successfully.', ['data' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::query()->forUser()->find($id);

        if (!$task) {
            return $this->error('Task not found.', HttpStatusCodes::NOT_FOUND);
        }

        $task->delete();

        return $this->success('Task deleted successfully.');
    }

    public function complete(Request $request, $id)
    {
        $task = Task::query()->forUser()->find($id);

        if (!$task) {
            return $this->error('Task not found.', HttpStatusCodes::NOT_FOUND);
        }

        $task->update(['is_completed' => $request->is_completed ?? 0]);

        $task = new TaskResource($task);

        return $this->success('Task marked as completed.', ['data' => $task]);
    }
}

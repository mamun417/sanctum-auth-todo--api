<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    const PAGINATION_LIMIT = 5;

    public function index()
    {
        $search = request('search');

        $tasksQuery = Task::query()->forUser()->latest();

        if ($search) {
            $tasksQuery->where('title', 'LIKE', "%{$search}%");
        }

        $tasks = $tasksQuery->paginate(self::PAGINATION_LIMIT);

        $tasks = TaskResource::collection($tasks);

        return $this->success('Tasks retrieved successfully.', $tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->only(['title', 'body']));
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
        $task = Task::query()->forUser()->find($id);

        if (!$task) {
            return $this->error('Task not found.', HttpStatusCodes::NOT_FOUND);
        }

        $task->update($request->only(['title', 'body']));

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

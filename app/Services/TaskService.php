<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class TaskService
{
    /**
     * Get the query for retrieving tasks with optional search.
     *
     * @param string|null $search
     * @return Builder
     */
    public function getTasksQuery(?string $search): Builder
    {
        $tasksQuery = Task::query()->forUser()->latest();

        if ($search) {
            $tasksQuery->where('title', 'LIKE', "%{$search}%");
        }

        return $tasksQuery;
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        return Task::create([
            'title' => $data['title'],
            'body' => $data['body'],
        ]);
    }

    /**
     * Update a task for the authenticated user.
     *
     * @param int $id
     * @param array $data
     * @return Task|null
     */
    public function updateTask(int $id, array $data): ?Task
    {
        $task = Task::query()->forUser()->find($id);

        if (!$task) {
            return null;
        }

        $task->update([
            'title' => $data['title'],
            'body' => $data['body'],
        ]);

        return $task;
    }
}

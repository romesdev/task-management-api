<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::all();
            return response()->json($tasks);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching tasks',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|min:3|max:255',
                'description' => 'required|string|min:3|max:500',
                'due_date' => 'required|date',
                'status' => 'required|in:pending,in_progress,completed',
                'user_id' => 'required|exists:users,id',
            ]);

            $task = Task::create($validated);
            return response()->json($task, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the task',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::findOrFail($id);
            return response()->json($task);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the task',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found',
            ], 404);
        }

        try {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|min:3|max:255',
                'description' => 'sometimes|required|string|min:3|max:500',
                'due_date' => 'sometimes|required|date',
                'status' => 'sometimes|required|in:pending,in_progress,completed',
                'user_id' => 'sometimes|required|exists:users,id',
            ]);

            $task->update($validated);
            return response()->json($task);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the task',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found',
            ], 404);
        }

        try {
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the task',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}

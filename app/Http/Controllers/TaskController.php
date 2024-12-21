<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;
    public function index(): JsonResponse
    {
        try {
            $tasks = Task::all();
            return $this->responseSuccess($tasks, 'Task list successfully');
        } catch (\Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|min:3|max:255',
                'description' => 'required|string|min:3|max:500',
                'due_date' => 'required|date',
                'status' => 'required|in:pending,in_progress,completed',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return $this->responseError($validator->errors(), 'Validation failed', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $task = Task::create($validator->validated());
            return $this->responseSuccess($task, 'New task created successfully', Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            return $this->responseSuccess($task, 'Task found successfully');
        } catch (ModelNotFoundException $e) {
            return $this->responseError($e, 'Task not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->responseError($e, 'Task not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|min:3|max:255',
                'description' => 'sometimes|required|string|min:3|max:500',
                'due_date' => 'sometimes|required|date',
                'status' => 'sometimes|required|in:pending,in_progress,completed',
                'user_id' => 'sometimes|required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return $this->responseError($validator->errors(), 'Validation failed', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $task->update($validator->validated());
            return $this->responseSuccess($task, 'Task updated successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->responseError($e, 'Task not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $task->delete();
            return $this->responseSuccess(null, 'Task deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

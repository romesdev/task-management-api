<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

const DEFAULT_PAGE = 1;
const DEFAULT_PER_PAGE = 10;
class TaskController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * Lista todas as tarefas.
     *
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Obter lista de tarefas",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número da página para paginação",
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Número de tarefas por página",
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarefas retornada com sucesso.",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no servidor."
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return $this->responseError($validator->errors(), 'Invalid pagination parameters', Response::HTTP_BAD_REQUEST);
            }

            $page = $request->input('page', DEFAULT_PAGE);
            $perPage = $request->input('per_page', DEFAULT_PER_PAGE);

            $tasks = Task::paginate($perPage, ['*'], 'page', $page);

            return $this->responseSuccess($tasks, 'Task list successfully');
        } catch (\Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cria uma nova tarefa.
     *
     * @OA\Post(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Criar nova tarefa",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="title", type="string", example="Finalizar relatório", description="Título da tarefa."),
     *              @OA\Property(property="description", type="string", example="Relatório deve ser entregue até sexta-feira", description="Descrição da tarefa."),
     *              @OA\Property(property="due_date", type="string", format="date", example="2024-12-31", description="Data de vencimento da tarefa."),
     *              @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="pending", description="Status da tarefa (pending, in_progress, completed)."),
     *              @OA\Property(property="user_id", type="integer", example=10, description="ID do usuário associado à tarefa.")
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Tarefa criada com sucesso.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no servidor."
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request['created_by'] = Auth::id();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|min:3|max:255',
                'description' => 'required|string|min:3|max:500',
                'due_date' => 'required|date|after_or_equal:today',
                'status' => 'required|in:pending,in_progress,completed',
                'user_id' => 'required|exists:users,id',
                'created_by' => 'required|exists:users,id',
            ], [
                'due_date.after_or_equal' => 'A data de vencimento deve ser uma data futura ou igual a hoje.',
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

    /**
     * Exibe uma tarefa específica.
     *
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Obter detalhes de uma tarefa",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa encontrada.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no servidor."
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $task = Task::where('id', '=', $id)->first();

            if (! $task) {
                return $this->responseError(null, 'Task not found or access denied', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($task, 'Task found successfully');
        } catch (ModelNotFoundException $e) {
            return $this->responseError($e, 'Task not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Atualiza uma tarefa existente.
     *
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Atualizar uma tarefa",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="title", type="string", example="Finalizar relatório", description="Título da tarefa."),
     *              @OA\Property(property="description", type="string", example="Relatório deve ser entregue até sexta-feira", description="Descrição da tarefa."),
     *              @OA\Property(property="due_date", type="string", format="date", example="2024-12-31", description="Data de vencimento da tarefa. Use a data atual ou futura."),
     *              @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="pending", description="Status da tarefa (pending, in_progress, completed)."),
     *              @OA\Property(property="user_id", type="integer", example=10, description="ID do usuário associado à tarefa.")
     *         ),
     *     ),
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa atualizada com sucesso.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no servidor."
     *     )
     * )
     */
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
                'due_date' => 'sometimes|required|date|after_or_equal:today',
                'status' => 'sometimes|required|in:pending,in_progress,completed',
                'user_id' => 'sometimes|required|exists:users,id',
            ], [
                'due_date.after_or_equal' => 'A data de vencimento deve ser uma data futura ou igual a hoje.',
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

    /**
     * Exclui uma tarefa.
     *
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Deletar uma tarefa",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Tarefa deletada com sucesso."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa não encontrada."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno no servidor."
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {

            $userId = Auth::id();

            $task = Task::where('id', '=', $id)->where('created_by', '=', $userId)->first();

            if (!$task) {
                return $this->responseError(null, 'Task not found or access denied (just could can deleted by creator)', Response::HTTP_NOT_FOUND);
            }

            $task->delete();

            return $this->responseSuccess(null, 'Task deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->responseError($e, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o endpoint index para listar tarefas do usuário logado.
     */
    public function test_index_returns_tasks_for_logged_in_user()
    {
        $user = User::factory()->create();
        $tasks = Task::factory(3)->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->getJson('/api/tasks');

        $this->assertCount(3, $response->json('data'));

        $response->assertStatus(200)
            ->assertJson([
                'data' => $tasks->toArray(),
            ]);
    }

    /**
     * Testa o endpoint show para obter uma tarefa específica.
     */
    public function test_show_returns_task_for_logged_in_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => $task->toArray(),
            ]);
    }

    /**
     * Testa a criação de uma nova tarefa.
     */
    public function test_store_creates_new_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'title' => 'New Task',
            'description' => 'Task description',
            'due_date' => '2024-12-31',
            'status' => 'pending',
            'user_id' => $user->id,
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'New Task',
                    'description' => 'Task description',
                    'status' => 'pending',
                ],
            ]);

        // Verificar se a tarefa foi criada no banco
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'user_id' => $user->id,
        ]);
    }

    public function test_store_returns_validation_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $invalidData = [
            'title' => '',
            'description' => '',
            'due_date' => 'invalid-date',
            'status' => 'unknown_status',
        ];

        // Faz a requisição POST para criar uma tarefa
        $response = $this->postJson('/api/tasks', $invalidData);

        // Verifica o status HTTP retornado
        $response->assertStatus(422);

        // Verifica a estrutura do JSON de erro retornado
        $response->assertJsonStructure([
            'status',
            "message",
            'errors' => [
                'title',
                'description',
                'due_date',
                'status',
            ],
            "data"
        ]);

        // Verifica mensagens específicas de erro
        $response->assertJsonFragment([
            'message' => 'Validation failed',
        ]);

        // Verifica que não foram criadas novas tarefas no banco
        $this->assertDatabaseCount('tasks', 0);
    }


    /**
     * Testa a atualização de uma tarefa existente.
     */
    public function test_update_updates_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $payload = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Title',
                    'description' => 'Updated description',
                ],
            ]);

        // Verificar se a tarefa foi atualizada no banco
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
        ]);
    }

    /**
     * Testa a exclusão de uma tarefa.
     */
    public function test_destroy_deletes_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        // Verificar se a tarefa foi excluída do banco
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}

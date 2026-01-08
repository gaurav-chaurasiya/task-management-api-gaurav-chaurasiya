<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('auth_token')->plainTextToken;
    $this->headers = ['Authorization' => 'Bearer ' . $this->token];
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
});

test('user can list tasks in their project', function () {
    Task::factory()->count(5)->create(['project_id' => $this->project->id]);

    $response = $this->withHeaders($this->headers)->getJson("/api/projects/{$this->project->id}/tasks");

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

test('user can create a task in their project', function () {
    $data = [
        'title' => 'New Task',
        'description' => 'Description',
        'status' => 'todo',
        'priority' => 'high'
    ];

    $response = $this->withHeaders($this->headers)->postJson("/api/projects/{$this->project->id}/tasks", $data);

    $response->assertStatus(201)
        ->assertJsonFragment(['title' => 'New Task']);

    $this->assertDatabaseHas('tasks', ['title' => 'New Task', 'project_id' => $this->project->id]);
});

test('user can filter tasks by status', function () {
    Task::factory()->create(['project_id' => $this->project->id, 'status' => 'todo']);
    Task::factory()->create(['project_id' => $this->project->id, 'status' => 'done']);

    $response = $this->withHeaders($this->headers)->getJson("/api/projects/{$this->project->id}/tasks?status=todo");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['status' => 'todo']);
});

test('user can sort tasks by due_date', function () {
    Task::factory()->create(['project_id' => $this->project->id, 'due_date' => '2026-01-10']);
    Task::factory()->create(['project_id' => $this->project->id, 'due_date' => '2026-01-01']);

    $response = $this->withHeaders($this->headers)->getJson("/api/projects/{$this->project->id}/tasks?sort_by=due_date&sort_order=asc");

    $response->assertStatus(200);
    $data = $response->json('data');
    expect($data[0]['due_date'])->toBe('2026-01-01');
    expect($data[1]['due_date'])->toBe('2026-01-10');
});

test('user can update their task', function () {
    $task = Task::factory()->create(['project_id' => $this->project->id]);
    $data = ['title' => 'Updated Task'];

    $response = $this->withHeaders($this->headers)->putJson("/api/tasks/{$task->id}", $data);

    $response->assertStatus(200)
        ->assertJsonFragment(['title' => 'Updated Task']);
});

test('user can delete their task', function () {
    $task = Task::factory()->create(['project_id' => $this->project->id]);

    $response = $this->withHeaders($this->headers)->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

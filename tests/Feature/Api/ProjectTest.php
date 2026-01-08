<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('auth_token')->plainTextToken;
    $this->headers = ['Authorization' => 'Bearer ' . $this->token];
});

test('user can list their projects', function () {
    Project::factory()->count(3)->create(['user_id' => $this->user->id]);
    Project::factory()->count(2)->create(); // Other user's projects

    $response = $this->withHeaders($this->headers)->getJson('/api/projects');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('user can create a project', function () {
    $data = [
        'name' => 'New Project',
        'description' => 'Description',
        'status' => 'pending'
    ];

    $response = $this->withHeaders($this->headers)->postJson('/api/projects', $data);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'New Project']);

    $this->assertDatabaseHas('projects', ['name' => 'New Project', 'user_id' => $this->user->id]);
});

test('user can view their project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders($this->headers)->getJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => $project->name]);
});

test('user cannot view others project', function () {
    $project = Project::factory()->create(); // Different user

    $response = $this->withHeaders($this->headers)->getJson("/api/projects/{$project->id}");

    $response->assertStatus(403);
});

test('user can update their project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $data = ['name' => 'Updated Name'];

    $response = $this->withHeaders($this->headers)->putJson("/api/projects/{$project->id}", $data);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Updated Name']);
});

test('user can delete their project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders($this->headers)->deleteJson("/api/projects/{$project->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});

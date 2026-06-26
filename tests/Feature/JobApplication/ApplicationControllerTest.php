<?php

declare(strict_types=1);

use App\Domain\Auth\Entities\User;
use App\Infrastructure\Persistence\Models\JobApplicationModel;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->application = JobApplicationModel::factory()->for($this->user)->create();

    $this->nonOwnerUser = User::factory()->create();
});

it('returns 401 for unauthenticated requests', function () {
    getJson('api/applications')->assertUnauthorized();
});

it('returns applications list for authenticated user', function () {
    actingAs($this->user)->getJson('/api/applications')->assertOk()->assertJsonStructure(['data']);
});

it('each user only sees their own applications', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    JobApplicationModel::factory()->for($user1)->create();
    JobApplicationModel::factory()->for($user2)->create();

    actingAs($user1)->getJson('/api/applications')->assertJsonCount(1, 'data');
    actingAs($user2)->getJson('/api/applications')->assertJsonCount(1, 'data');
});

it('authenticated user can create an application', function () {
    actingAs($this->user)->postJson('/api/applications', $this->application->toArray())->assertCreated();
});

it('returns 422 when creating application with invalid data', function () {
    $invalidApplication = JobApplicationModel::factory()->invalid()->make();

    actingAs($this->user)->postJson('/api/applications', $invalidApplication->toArray())->assertUnprocessable();
});

it('returns application by id for its owner', function () {
    $application = JobApplicationModel::factory()->for($this->user)->create();
    actingAs($this->user)->getJson('/api/applications/' . $application->id)->assertJsonStructure(['data']);
});

it('returns 404 when accessing another user application', function () {

    $application = JobApplicationModel::factory()->for($this->user)->create();
    actingAs($this->nonOwnerUser)->getJson('/api/applications/' . $application->id)->assertNotFound();
});

it('returns 200 when updating application by its owner', function () {
    $application = JobApplicationModel::factory()->make();
    actingAs($this->user)->putJson('/api/applications/' . $this->application->id, $application->toArray())->assertOk();
});

it('returns 404 if putting another user application', function () {
    $application = JobApplicationModel::factory()->make();
    actingAs($this->nonOwnerUser)->putJson('/api/applications/' . $this->application->id, $application->toArray())->assertNotFound();
});

it('returns 204 when deleting application by its owner', function () {
    actingAs($this->user)->deleteJson('/api/applications/' . $this->application->id)->assertNoContent();
});

it('returns 404 when deleting antoher user application', function () {
    $application = JobApplicationModel::factory()->make();
    actingAs($this->nonOwnerUser)->deleteJson('/api/applications/' . $this->application->id)->assertNotFound();
});
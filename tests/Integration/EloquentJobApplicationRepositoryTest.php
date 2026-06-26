<?php

declare(strict_types=1);

use App\Domain\Auth\Entities\User;
use App\Domain\JobApplication\Entities\JobApplication;
use App\Domain\JobApplication\ValueObjects\ApplicationData;
use App\Domain\JobApplication\ValueObjects\ApplicationSource;
use App\Domain\JobApplication\ValueObjects\ApplicationStatus;
use App\Infrastructure\Persistence\Repositories\EloquentJobApplicationRepository;

beforeEach(function () {
    $this->repository = new EloquentJobApplicationRepository();
    $this->user = User::factory()->create();
});

it('create persists and returns a JobApplication entity', function () {
    $application = new ApplicationData(
        company: 'Google',
        position: 'Software Engineer',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );
    $result = $this->repository->create($this->user->id, $application);

    expect($result)->toBeInstanceOf(JobApplication::class);
    expect($result->company)->toBe('Google');
    expect($result->position)->toBe('Software Engineer');
});

it('findAllForUser returns only applications belonging to the user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $application1 = new ApplicationData(
        company: 'Ubisoft',
        position: 'PDG',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );

    $application2 = new ApplicationData(
        company: 'Riot Games',
        position: 'DSI',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );

    $this->repository->create($user1->id, $application1);
    $this->repository->create($user2->id, $application2);

    expect($this->repository->findAllForUser($user1->id))->toHaveCount(1);
    expect($this->repository->findAllForUser($user1->id)[0]->company)->toBe('Ubisoft');

    expect($this->repository->findAllForUser($user2->id))->toHaveCount(1);
    expect($this->repository->findAllForUser($user2->id)[0]->company)->toBe('Riot Games');
});

it('findByIdForUser return application by id', function () {
    $application = new ApplicationData(
        company: 'Ubisoft',
        position: 'PDG',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );

    $result = $this->repository->create($this->user->id, $application);

    expect($this->repository->findByIdForUser($result->id, $this->user->id)->company)->toBe("Ubisoft");
});

it('findByIdForUser return null when getting application by not its owner', function () {
    $application = new ApplicationData(
        company: 'Ubisoft',
        position: 'PDG',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );

    $user2 = User::factory()->create();

    $result = $this->repository->create($this->user->id, $application);

    expect($this->repository->findByIdForUser($result->id, $user2->id))->toBeNull();
});

it('update persists new data and returns updated JobApplication', function () {
    $defaultApplication = new ApplicationData(
        company: 'Ubisoft',
        position: 'PDG',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );

    $newApplication = new ApplicationData(
        company: 'Ubisoft',
        position: 'PDG',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );

    $createResult = $this->repository->create($this->user->id, $defaultApplication);
    $this->repository->update($createResult->id, $this->user->id, $newApplication);

    expect($this->repository->findByIdForUser($createResult->id, $this->user->id)->company)->toBe('Ubisoft');
});

it('delete removes the application from the database', function () {
     $application = new ApplicationData(
        company: 'Ubisoft',
        position: 'PDG',
        status: ApplicationStatus::Postule,
        source: ApplicationSource::LinkedIn,
        appliedAt: null,
        location: null,
        salary: null,
        tags: [],
        isRemote: false,
        offerUrl: null,
        notes: null,
        message: null,
    );

    $result = $this->repository->create($this->user->id, $application);
    $this->repository->delete($result->id, $this->user->id);

    expect($this->repository->findByIdForUser($result->id, $this->user->id))->toBeNull();
});



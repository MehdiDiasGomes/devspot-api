<?php

declare(strict_types=1);
use App\Domain\JobApplication\ValueObjects\ApplicationStatus;

it('throws ValueError for invalid status value', function () {
    expect(fn() => ApplicationStatus::from('invalid_value'))->toThrow(\ValueError::class);
});

it('resolves valid string to correct enum case', function () {
    expect(ApplicationStatus::from('repere'))->toBe(ApplicationStatus::Repere);
});

it('returns null for trying invalid status value', function () {
    expect(ApplicationStatus::tryFrom('invalid_value'))->toBeNull();
});
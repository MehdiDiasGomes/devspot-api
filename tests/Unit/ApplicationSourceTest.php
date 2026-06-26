<?php

declare(strict_types=1);

use App\Domain\JobApplication\ValueObjects\ApplicationSource;


it('throws ValueError for invalid status value', function () {
    expect(fn() => ApplicationSource::from('invalid_value'))->toThrow(\ValueError::class);
});

it('resolves valid string to correct enum case', function () {
    expect(ApplicationSource::from('linkedin'))->toBe(ApplicationSource::LinkedIn);
});

it('returns null for trying invalid status value', function () {
    expect(ApplicationSource::tryFrom('invalid_value'))->toBeNull();
});

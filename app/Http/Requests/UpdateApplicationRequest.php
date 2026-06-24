<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\JobApplication\ValueObjects\ApplicationSource;
use App\Domain\JobApplication\ValueObjects\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateApplicationRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'company'    => ['required', 'string', 'max:255'],
            'position'   => ['required', 'string', 'max:255'],
            'status'     => ['required', Rule::enum(ApplicationStatus::class)],
            'source'     => ['required', Rule::enum(ApplicationSource::class)],
            'applied_at' => ['nullable', 'date'],
            'location'   => ['nullable', 'string', 'max:255'],
            'salary'     => ['nullable', 'string', 'max:100'],
            'tags'       => ['nullable', 'array'],
            'tags.*'     => ['string', 'max:50'],
            'is_remote'  => ['nullable', 'boolean'],
            'offer_url'  => ['nullable', 'url', 'max:500'],
            'notes'      => ['nullable', 'string'],
            'message'    => ['nullable', 'string'],
        ];
    }
}

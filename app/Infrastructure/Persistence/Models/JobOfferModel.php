<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

final class JobOfferModel extends Model
{
    protected $table = 'job_offers';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'company',
        'company_logo_url',
        'location',
        'is_remote',
        'type',
        'contract_label',
        'salary_min',
        'salary_max',
        'salary_currency',
        'description',
        'tags',
        'source',
        'source_url',
        'source_id',
        'deduplication_hash',
        'published_at',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
        'tags' => 'array',
        'published_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}

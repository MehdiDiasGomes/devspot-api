<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Auth\Entities\User;

final class JobApplicationModel extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'user_id',
        'company',
        'position',
        'status',
        'source',
        'applied_at',
        'location',
        'salary',
        'tags',
        'is_remote',
        'offer_url',
        'notes',
        'cover_letter_path',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'tags'       => 'array',
            'is_remote'  => 'boolean',
            'applied_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

final class CompanyLogoModel extends Model
{
    protected $table = 'company_logos';

    protected $primaryKey = 'company_name';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['company_name', 'logo_url', 'fetched_at'];
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('company');
            $table->string('location')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->string('type');
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->char('salary_currency', 3)->nullable();
            $table->text('description')->nullable();
            $table->jsonb('tags')->default('[]');
            $table->string('source');
            $table->text('source_url');
            $table->string('source_id');
            $table->char('deduplication_hash', 32)->unique();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('source');
            $table->index('type');
            $table->index('is_remote');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};

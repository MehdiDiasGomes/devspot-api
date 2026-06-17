<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company');
            $table->string('position');
            $table->enum('status', [
                'repere',
                'postule',
                'relance',
                'entretien',
                'test_tech',
                'offre',
                'refus',
                'abandonne',
            ])->default('repere');
            $table->enum('source', [
                'linkedin',
                'indeed',
                'reseau',
                'site_direct',
                'autre',
            ])->default('autre');
            $table->date('applied_at')->nullable();
            $table->string('location')->nullable();
            $table->string('salary')->nullable();
            $table->jsonb('tags')->default('[]');
            $table->boolean('is_remote')->default(false);
            $table->string('offer_url')->nullable();
            $table->text('notes')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

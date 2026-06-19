<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('company_logos');
    }

    public function down(): void
    {
        Schema::create('company_logos', function (Blueprint $table) {
            $table->string('company_name')->primary();
            $table->string('logo_url')->nullable();
            $table->timestamp('fetched_at');
        });
    }
};

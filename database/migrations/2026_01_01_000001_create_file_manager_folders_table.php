<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('itmosoft-file-manager.tables.folders', 'file_manager_folders'), function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->foreignId('parent_id')->nullable()
                ->constrained(config('itmosoft-file-manager.tables.folders', 'file_manager_folders'))
                ->nullOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->index(['owner_type', 'owner_id', 'name'], 'file_manager_folders_owner_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('itmosoft-file-manager.tables.folders', 'file_manager_folders'));
    }
};

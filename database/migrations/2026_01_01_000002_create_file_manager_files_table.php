<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('itmosoft-file-manager.tables.files', 'file_manager_files'), function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->foreignId('folder_id')->nullable()
                ->constrained(config('itmosoft-file-manager.tables.folders', 'file_manager_folders'))
                ->nullOnDelete();
            $table->string('disk', 80);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 190)->nullable();
            $table->string('extension', 32)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['owner_type', 'owner_id', 'created_at'], 'file_manager_files_owner_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('itmosoft-file-manager.tables.files', 'file_manager_files'));
    }
};

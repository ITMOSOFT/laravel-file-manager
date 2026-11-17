<?php

namespace ItmoSoft\FileManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FileAsset extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('itmosoft-file-manager.tables.files', 'file_manager_files');
    }

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(FileFolder::class, 'folder_id');
    }
}

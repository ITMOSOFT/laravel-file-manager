<?php

namespace ItmoSoft\FileManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FileFolder extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('itmosoft-file-manager.tables.folders', 'file_manager_folders');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function files(): HasMany
    {
        return $this->hasMany(FileAsset::class, 'folder_id');
    }
}

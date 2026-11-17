<?php

namespace ItmoSoft\FileManager\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use ItmoSoft\FileManager\Models\FileAsset;
use ItmoSoft\FileManager\Models\FileFolder;

trait HasFileManager
{
    public function fileAssets(): MorphMany
    {
        return $this->morphMany(FileAsset::class, 'owner');
    }

    public function fileFolders(): MorphMany
    {
        return $this->morphMany(FileFolder::class, 'owner');
    }
}

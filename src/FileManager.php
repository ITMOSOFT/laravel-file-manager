<?php

namespace ItmoSoft\FileManager;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ItmoSoft\FileManager\Exceptions\FileManagerException;
use ItmoSoft\FileManager\Models\FileAsset;
use ItmoSoft\FileManager\Models\FileFolder;

class FileManager
{
    public function createFolder(
        Model $owner,
        string $name,
        ?FileFolder $parent = null,
    ): FileFolder {
        $name = trim($name);

        if ($name === '') {
            throw new FileManagerException('A folder name is required.');
        }

        if ($parent && ! $this->sameOwner($parent, $owner)) {
            throw new FileManagerException('The parent folder belongs to a different owner.');
        }

        return FileFolder::query()->create([
            'owner_type' => $owner->getMorphClass(),
            'owner_id' => $owner->getKey(),
            'parent_id' => $parent?->getKey(),
            'name' => $name,
        ]);
    }

    public function upload(
        UploadedFile $file,
        Model $owner,
        ?FileFolder $folder = null,
        ?string $disk = null,
        array $metadata = [],
    ): FileAsset {
        if ($folder && ! $this->sameOwner($folder, $owner)) {
            throw new FileManagerException('The destination folder belongs to a different owner.');
        }

        $disk ??= config('itmosoft-file-manager.disk', config('filesystems.default', 'local'));
        $directory = trim((string) config('itmosoft-file-manager.directory', 'itmosoft-files'), '/');
        $path = $file->store($directory, ['disk' => $disk]);

        if (! is_string($path) || $path === '') {
            throw new FileManagerException('The uploaded file could not be stored.');
        }

        try {
            return DB::transaction(fn () => FileAsset::query()->create([
                'owner_type' => $owner->getMorphClass(),
                'owner_id' => $owner->getKey(),
                'folder_id' => $folder?->getKey(),
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->guessExtension(),
                'size' => $file->getSize(),
                'metadata' => $metadata,
            ]));
        } catch (\Throwable $exception) {
            Storage::disk($disk)->delete($path);
            throw $exception;
        }
    }

    public function moveToFolder(FileAsset $asset, ?FileFolder $folder): FileAsset
    {
        if ($folder && (
            $asset->owner_type !== $folder->owner_type
            || (string) $asset->owner_id !== (string) $folder->owner_id
        )) {
            throw new FileManagerException('The destination folder belongs to a different owner.');
        }

        $asset->folder_id = $folder?->getKey();
        $asset->save();

        return $asset->refresh();
    }

    public function delete(FileAsset $asset): void
    {
        Storage::disk($asset->disk)->delete($asset->path);
        $asset->delete();
    }

    private function sameOwner(FileFolder $folder, Model $owner): bool
    {
        return $folder->owner_type === $owner->getMorphClass()
            && (string) $folder->owner_id === (string) $owner->getKey();
    }
}

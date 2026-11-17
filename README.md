# Laravel File Manager

File uploads, folder organization, polymorphic ownership, and storage metadata for Laravel applications.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-file-manager
php artisan vendor:publish --tag=itmosoft-file-manager-config
php artisan migrate
```

## Add file management to a model

```php
use ItmoSoft\FileManager\Concerns\HasFileManager;

class User extends Model
{
    use HasFileManager;
}
```

## Create folders and upload files

```php
use ItmoSoft\FileManager\FileManager;

$manager = app(FileManager::class);

$folder = $manager->createFolder(auth()->user(), 'Contracts');

$asset = $manager->upload(
    file: $request->file('document'),
    owner: auth()->user(),
    folder: $folder,
    metadata: ['category' => 'legal'],
);
```

The package stores the binary through Laravel's configured filesystem disk and persists the
disk, path, original name, MIME type, extension, byte size, owner, folder, and metadata.

## Move and delete files

```php
$manager->moveToFolder($asset, $anotherFolder);
$manager->delete($asset);
```

Pass `null` as the destination folder to move an asset to the owner's root.

## Query files and folders

```php
$user->fileAssets()->latest()->get();
$user->fileFolders()->with('children', 'files')->get();
```

## Security responsibilities

- The package prevents assigning a folder to a different polymorphic owner through its manager methods.
- Your application must authorize uploads, downloads, listing, and deletion.
- Do not expose storage paths or generate public URLs without applying your access policy.
- Validate allowed file types, sizes, and content at the application boundary.
- Configure private storage disks for confidential files.

## Scope limitations

This release does not include a file-browser UI, HTTP routes, download authorization, image transformations,
virus scanning, chunked uploads, version history, sharing links, or a recycle bin.

## Testing

```bash
composer install
composer test
```

## License

MIT. See [LICENSE](LICENSE).

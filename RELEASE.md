# Release checklist

1. Run `composer install`.
2. Run `composer test` and ensure all tests pass.
3. Review package contents and `CHANGELOG.md`.
4. Commit the source to `main`.
5. Create and push the `v1.0.0` tag only after tests and CI pass.
6. Create a GitHub Release with the title and notes below.
7. Submit the repository URL to Packagist.

## GitHub Release title

`v1.0.0 - Initial Release`

## GitHub Release notes

### Highlights
- Polymorphic file ownership.
- Nested folders.
- Upload through Laravel filesystem disks.
- File metadata and owner-scoped folder assignment.
- Move and delete operations.

### Installation
```bash
composer require itmosoft/laravel-file-manager
php artisan vendor:publish --tag=itmosoft-file-manager-config
php artisan migrate
```

### Requirements
- PHP 8.3+
- Laravel 12 or 13

### Limitations
No file-browser UI, HTTP routes, download authorization, image transformations, virus scanning,
chunked uploads, version history, sharing links, or recycle bin.

# Changelog

## [1.0.0] - 2026-11-17

### Added
- Polymorphic file ownership for Eloquent models.
- Folder creation and nested folder organization.
- Uploads through Laravel's filesystem abstraction.
- File metadata persistence, including disk, path, original name, MIME type, extension, and size.
- Move files between folders belonging to the same owner.
- Delete stored files and their database records.
- `HasFileManager` model trait.
- Config publishing, migrations, PHPUnit tests, and GitHub Actions CI.

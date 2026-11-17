<?php

namespace ItmoSoft\FileManager\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use ItmoSoft\FileManager\Exceptions\FileManagerException;
use ItmoSoft\FileManager\Tests\Fixtures\User;
use ItmoSoft\FileManager\Tests\TestCase;
use ItmoSoft\FileManager\FileManager;

class FileManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config()->set('itmosoft-file-manager.disk', 'public');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $this->artisan('migrate', ['--force' => true]);
    }

    public function test_it_creates_a_folder_for_an_owner(): void
    {
        $user = $this->user();
        $folder = app(FileManager::class)->createFolder($user, '  Contracts  ');

        $this->assertSame('Contracts', $folder->name);
        $this->assertSame($user->getKey(), $folder->owner_id);
    }

    public function test_it_creates_nested_folders_for_the_same_owner(): void
    {
        $user = $this->user();
        $manager = app(FileManager::class);
        $parent = $manager->createFolder($user, 'Documents');
        $child = $manager->createFolder($user, 'Invoices', $parent);

        $this->assertSame($parent->id, $child->parent_id);
    }

    public function test_it_rejects_a_parent_folder_owned_by_another_user(): void
    {
        $manager = app(FileManager::class);
        $folder = $manager->createFolder($this->user('One'), 'Private');

        $this->expectException(FileManagerException::class);
        $manager->createFolder($this->user('Two'), 'Child', $folder);
    }

    public function test_it_uploads_a_file_and_persists_metadata(): void
    {
        $user = $this->user();
        $folder = app(FileManager::class)->createFolder($user, 'Documents');

        $asset = app(FileManager::class)->upload(
            UploadedFile::fake()->createWithContent('contract.txt', 'sample contract'),
            $user,
            $folder,
            metadata: ['category' => 'legal'],
        );

        Storage::disk('public')->assertExists($asset->path);
        $this->assertSame('contract.txt', $asset->original_name);
        $this->assertSame('legal', $asset->metadata['category']);
        $this->assertSame($folder->id, $asset->folder_id);
    }

    public function test_it_rejects_upload_to_another_owners_folder(): void
    {
        $manager = app(FileManager::class);
        $folder = $manager->createFolder($this->user('One'), 'Private');

        $this->expectException(FileManagerException::class);
        $manager->upload(
            UploadedFile::fake()->createWithContent('note.txt', 'text'),
            $this->user('Two'),
            $folder,
        );
    }

    public function test_it_can_move_a_file_between_folders_of_same_owner(): void
    {
        $user = $this->user();
        $manager = app(FileManager::class);
        $first = $manager->createFolder($user, 'First');
        $second = $manager->createFolder($user, 'Second');
        $asset = $manager->upload(
            UploadedFile::fake()->createWithContent('note.txt', 'text'),
            $user,
            $first,
        );

        $asset = $manager->moveToFolder($asset, $second);

        $this->assertSame($second->id, $asset->folder_id);
    }

    public function test_it_rejects_moving_a_file_to_another_owners_folder(): void
    {
        $manager = app(FileManager::class);
        $owner = $this->user('Owner');
        $other = $this->user('Other');
        $asset = $manager->upload(
            UploadedFile::fake()->createWithContent('note.txt', 'text'),
            $owner,
        );
        $folder = $manager->createFolder($other, 'Other folder');

        $this->expectException(FileManagerException::class);
        $manager->moveToFolder($asset, $folder);
    }

    public function test_it_deletes_the_physical_file_and_database_record(): void
    {
        $user = $this->user();
        $manager = app(FileManager::class);
        $asset = $manager->upload(
            UploadedFile::fake()->createWithContent('remove.txt', 'remove me'),
            $user,
        );
        $path = $asset->path;

        $manager->delete($asset);

        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('file_manager_files', ['id' => $asset->id]);
    }

    public function test_it_rejects_empty_folder_names(): void
    {
        $this->expectException(FileManagerException::class);
        app(FileManager::class)->createFolder($this->user(), '   ');
    }

    private function user(string $name = 'Example'): User
    {
        return User::query()->create(['name' => $name]);
    }
}

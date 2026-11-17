<?php

namespace ItmoSoft\FileManager\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use ItmoSoft\FileManager\Concerns\HasFileManager;

class User extends Model
{
    use HasFileManager;

    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;
}

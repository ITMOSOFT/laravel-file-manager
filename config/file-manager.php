<?php

return [
    'disk' => env('FILESYSTEM_DISK', 'local'),
    'directory' => 'itmosoft-files',
    'tables' => [
        'folders' => 'file_manager_folders',
        'files' => 'file_manager_files',
    ],
];

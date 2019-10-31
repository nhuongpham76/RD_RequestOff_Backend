<?php

return [
    'validate' => [
        'fail' => 'Data validation failed'
    ],
    'error' => [
        'create' => ':item cannot be created.',
        'update' => ':item cannot be updated.',
        'delete' => ':item cannot be deleted.',
        'list' => ':item cannot be listed.',
        'show' => ':item cannot be showed.',
        'permission' => 'Permissiond denied'
    ],
    'success' => [
        'create' => 'Create :item success.',
        'update' => 'Update :item success.',
        'delete' => 'Delete :item success.',
        'list' => 'List :item sussess.',
        'show' => 'Show :item cuccess.',
    ],
    App\Models\User::class => [
        'not_found' => 'User not found.'
    ],
];

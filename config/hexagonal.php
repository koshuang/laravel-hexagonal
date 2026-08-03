<?php

return [
    'modules_path' => base_path('Modules'),

    'modules_namespace' => 'Modules',

    'shared_module' => 'Shared',

    'stubs' => [
        'module' => base_path('stubs/hexagonal/module'),
        'shared' => base_path('stubs/hexagonal/shared'),
        'deptrac' => base_path('stubs/hexagonal/deptrac.yaml'),
    ],

    'deptrac' => [
        'config' => base_path('deptrac.yaml'),
        'binary' => base_path('vendor/bin/deptrac'),
        'timeout' => 120,
    ],
];

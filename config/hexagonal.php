<?php

return [
    'modules_path' => base_path('Modules'),

    'modules_namespace' => 'Modules',

    'shared_module' => 'Shared',

    'deptrac' => [
        'config' => base_path('deptrac.yaml'),
        'binary' => base_path('vendor/bin/deptrac'),
        'timeout' => 120,
    ],
];

<?php

return [
    'modules_path' => base_path('Modules'),

    'modules_namespace' => 'Modules',

    'shared_module' => 'Shared',

    'modules_statuses' => base_path('modules_statuses.json'),

    'stubs' => [
        'module' => base_path('stubs/hexagonal/module'),
        'shared' => base_path('stubs/hexagonal/shared'),
        'deptrac' => base_path('stubs/hexagonal/deptrac.yaml'),
    ],

    'deptrac' => [
        'config' => base_path('deptrac.yaml'),
        'binary' => base_path('vendor/bin/deptrac'),
        'timeout' => 120,
        // Allowances emitted into the generated deptrac.yaml. Set false when
        // your domains must not depend on a given framework class.
        'allowances' => [
            'carbon' => true,
            'illuminate' => true,
            'illuminate_support_facades' => true,
        ],
    ],
];

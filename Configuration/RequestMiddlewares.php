<?php

return [
    'backend' => [
        'webentwicklerat/openid-connect/backend-redirect' => [
            'target' => \WebentwicklerAt\OpenidConnect\Middleware\BackendRedirect::class,
            'after' => [
                'typo3/cms-backend/authentication',
            ],
        ],
    ],
];

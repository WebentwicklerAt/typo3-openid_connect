<?php

return [
    'frontend' => [
        'webentwicklerat/openid-connect/frontend-redirect' => [
            'target' => \WebentwicklerAt\OpenidConnect\Middleware\FrontendRedirect::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
    'backend' => [
        'webentwicklerat/openid-connect/backend-redirect' => [
            'target' => \WebentwicklerAt\OpenidConnect\Middleware\BackendRedirect::class,
            'after' => [
                'typo3/cms-backend/authentication',
            ],
        ],
    ],
];

<?php

return [
    'resolvers' => [
        'actors' => [
            //
        ],
        'database_setups' => [
            //
        ],
        'json_structures' => [
            'resource' => ['data'],
            'pagination' => [
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => [
                    'current_page', 'from', 'last_page',
                    'links' => ['*' => ['url', 'label', 'active']],
                ],
            ],
            'token' => ['token'],
            'message' => ['message'],
            'none' => null,
        ],
        'queries' => [
            //
        ],
    ],
];

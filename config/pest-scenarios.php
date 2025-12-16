<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | Control how the test suite reacts to errors.
    |
    | - configuration: invalid or missing scenario definitions
    | - resolution: valid definitions that fail at runtime
    |
    | true  → fail the test suite
    | false → skip the test instead
    |
    */

    'strict_mode' => [
        'configuration' => env('PEST_SCENARIOS_STRICT_CONFIGURATION', true),
        'resolution' => env('PEST_SCENARIOS_STRICT_RESOLUTION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scenario Resolvers
    |--------------------------------------------------------------------------
    |
    | Resolvers are named definitions used by scenarios and contexts to
    | dynamically resolve data at runtime.
    |
    | They allow scenarios to stay declarative while centralizing logic such as:
    | - resolving acting users (actors)
    | - preparing database state (database_setups)
    | - asserting API response shapes (json_structures)
    | - querying domain objects (queries)
    |
    */

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
                    'current_page',
                    'from',
                    'last_page',
                    'links' => ['*' => ['url', 'label', 'active']],
                ],
            ],
            'token' => ['token'],
            'message' => ['message'],
            'none' => null, // Explicitly assert no JSON structure
        ],

        'queries' => [
            //
        ],
    ],
];

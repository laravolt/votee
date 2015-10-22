<?php

return [
    // automatic loading of routes through main service provider
    'routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Default HTML format to be presented
    |--------------------------------------------------------------------------
    |
    | This option controls the HTML output that will be generated
    |
    | Supported: "semantic-ui", "bootstrap", "foundation"
    |
    */
    'presenter'     => 'semantic-ui',
    'values'        => [
        'up'      => 1,
        'down'    => -1,
        'neutral' => 0
    ]
];

<?php

return [

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

    /*
    |--------------------------------------------------------------------------
    | Value to be saved in database for each action
    |--------------------------------------------------------------------------
    |
    |
    */
    'values'        => [
        'up'      => 1,
        'down'    => -1,
        'neutral' => 0
    ],

    /*
    |--------------------------------------------------------------------------
    | Default voteable Model
    |--------------------------------------------------------------------------
    |
    |
    */
    'content_model' => null
];

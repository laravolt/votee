<?php

$router->get('votee/test', ['uses' => 'VoteeController@test', 'as' => 'votee.test']);

$router->post('votee/up', ['uses' => 'VoteeController@up', 'as' => 'votee.up']);
$router->post('votee/down', ['uses' => 'VoteeController@down', 'as' => 'votee.down']);

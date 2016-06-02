<?php
return array(
    'dispatcher' => array(
        'default' => 'Test\Controllers',
        'cli'     => 'Test\Tasks',
    ),
    'namespaces' => array(
        'Test\Controllers' => APP_DIR . '/Controllers',
        'Test\Models'      => APP_DIR . '/Models',
        'Test\Plugins'     => APP_DIR . '/Plugins',
        'Test\Tasks'       => APP_DIR . '/Tasks',
        'Test\Library'     => APP_DIR . '/Library',
    )
);
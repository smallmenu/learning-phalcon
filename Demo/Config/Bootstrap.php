<?php
return array(
    'dispatcher' => array(
        'default' => 'Demo\Controllers',
        'cli'     => 'Demo\Tasks',
    ),
    'namespaces' => array(
        'Demo\Controllers' => APP_DIR . '/Controllers',
        'Demo\Models'      => APP_DIR . '/Models',
        'Demo\Plugins'     => APP_DIR . '/Plugins',
        'Demo\Tasks'       => APP_DIR . '/Tasks',
        'Demo\Library'     => APP_DIR . '/Library',
    )
);
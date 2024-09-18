<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'bookstore_api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'class' => 'yii\web\Request',
            'parsers' => [
                    'application/json' => 'yii\web\JsonParser',
                ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'client' => [
            'class' => 'app\models\Client',
        ],
        'book' => [
            'class' => 'app\models\Book',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'user/login' => 'user/login',
                'client/create' => 'client/create',
                'client/list' => 'client/list',
                'book/create' => 'book/create',
                'book/list' => 'book/list',
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => yii\web\Response::FORMAT_JSON,
        ],
    ],
];

return $config;

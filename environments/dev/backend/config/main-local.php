<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'adminlte' => '@vendor/dmstr/yii-adminlte-asset/gii/templates/crud/simple',
                    'my_template' => Yii::getAlias('@common/wosotech/gii/crud/my_template')
                ],
                //'template' => 'yii2-app-kit',
                'template' => 'my_template',
                'messageCategory' => 'backend'
            ],
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'my_template' => Yii::getAlias('@common/wosotech/gii/model/my_template')
                ],
                'template' => 'my_template',
            ]
        ],
    ];
}

return $config;

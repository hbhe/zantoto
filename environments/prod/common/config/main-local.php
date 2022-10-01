<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=zantoto',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'pp_',
            'charset' => 'utf8',
            'enableSchemaCache' => !YII_DEBUG,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];

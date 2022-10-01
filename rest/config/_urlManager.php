<?php
return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        'GET,HEAD users' => 'article/index',
        [
            'class' => 'yii\rest\UrlRule',
            // 'pluralize' => false,
            'controller' => ['v1/article-category'],
            'except' => ['delete', 'update', 'create'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/article'],
            'except' => ['delete', 'update', 'create'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/area-code'],
            'except' => ['delete', 'update', 'create', 'view'],
            'extraPatterns' => [
                'GET get-current-area-code' => 'get-current-area-code',
                'GET get-province-agent-stat' => 'get-province-agent-stat',
                'GET get-city-code' => 'get-city-code',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/startup'],
            'pluralize' => false,
            'except' => ['delete', 'update', 'create'],
            'extraPatterns' => [
                'GET get-switch' => 'get-switch',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/term'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'only' => ['view'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/product'], // 商品列表
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'GET mine' => 'mine',
            ],
            'except' => ['delete', 'update', 'create'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/dashboard'],
            'pluralize' => false,
            'extraPatterns' => [
                'GET get-site-settings' => 'get-site-settings',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/cashout'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'only' => ['index', 'view', 'create'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/revenue-log'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'only' => ['index', 'view', 'revenue-detail', 'unclear', 'commission-stat'],
            'extraPatterns' => [
                'GET revenue-detail' => 'revenue-detail',
                'GET unclear' => 'unclear',
                'GET commission-stat' => 'commission-stat',
            ]
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/shop'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'GET mine' => 'mine',
            ]
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/wishlist'],
            'pluralize' => false,
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/shop-category'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'only' => ['index', 'view'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/banner'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'only' => ['index', 'view'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/cart'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/category'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'only' => ['index', 'view'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/rate'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'GET mine' => 'mine',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/product-option'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/sku'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/member-address'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/order'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'POST cancel/{id}' => 'cancel',
                'POST pay-orders-by-revenue' => 'pay-orders-by-revenue',
                'POST confirm-receive' => 'confirm-receive',
                'POST confirm-ship' => 'confirm-ship',
                'GET seller-orders' => 'seller-orders',
            ],
            //'except' => ['delete'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/order-sku-refund'],
            'pluralize' => false,
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'except' => ['delete'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/message'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'POST read-all' => 'read-all',
                'POST flush' => 'flush',
                'POST read-condition' => 'read-condition',
                'GET unread-count' => 'unread-count',
            ]
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/member'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'GET,POST login' => 'login',
                'GET,POST login-by-verify-code' => 'login-by-verify-code',
                'GET,POST login-by-openid' => 'login-by-openid',
                'GET,POST openid-bind-mobile' => 'openid-bind-mobile',
                'POST direct-bind' => 'direct-bind',
                'POST direct-unbind' => 'direct-unbind',
                'PUT reset-password' => 'reset-password',
                'GET,POST send-verify-code' => 'send-verify-code',
                'GET friend-members/{id}' => 'friend-members',
                'GET master-members/{id}' => 'master-members',
                'GET slave-members/{id}' => 'slave-members',
                'GET me' => 'me',
                'GET check-bind' => 'check-bind',
                'POST avatar-update' => 'avatar-update',
                'POST weixin-update' => 'weixin-update',
                'GET teacher' => 'teacher',
                'GET student' => 'student',
                'PUT update-password' => 'update-password',
                'POST update-name' => 'update-name',
                'POST power-to-fish' => 'power-to-fish',
            ],
            'except' => ['delete'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/site'],
            'pluralize' => false,
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'GET lookup' => 'lookup',
                'GET oauth' => 'oauth',
                'POST files-upload' => 'files-upload',
                'GET,POST files-delete' => 'files-delete',
            ],
            'except' => ['delete'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/tag'],
            'except' => ['delete', 'update', 'create'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

    ],
];

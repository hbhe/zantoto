<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'site/dashboard',
    'name' => '后台管理',
    'bootstrap' => ['log'],

    'container' => [
        'definitions' => [
            'yii\widgets\LinkPager' => ['maxButtonCount' => 10],
            'yii\data\Pagination' => ['defaultPageSize' => 20, ], // 'validatePage' => false
            'yii\grid\GridView' => [
                'layout' => "{summary}\n{items}\n{pager}",
            ],
            'yii\grid\ActionColumn' => [
                'template' => '{view} {update} {delete}',
            ],
            'yii\widgets\DetailView' => [
                'options' => ['class' => 'table table-bordered detail-view'],
            ],
        ],
    ],

    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            //'layout' => 'left-menu',
            //'mainLayout' => '@app/views/layouts/main-rbac.php',
            'menus' => [
                'assignment' => [
                    'label' => '角色分配'
                ],
                'user' => null, // disable menu
            ],
        ],

        // noam148图片管理
        'imagemanager' => [
            'class' => 'noam148\imagemanager\Module',
            //set accces rules ()
            'canUploadImage' => true,
            'canRemoveImage' => function () {
                return true;
            },
            'deleteOriginalAfterEdit' => false, // false: keep original image after edit. true: delete original image after edit
            // Set if blameable behavior is used, if it is, callable function can also be used
            'setBlameableBehavior' => false,
            //add css files (to use in media manage selector iframe)
            'cssFiles' => [
                //'https://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css',
            ],
        ],

        // 内容管理
        'content' => [
            'class' => 'common\modules\content\backend\Module',
        ],

        // 键值对(key-value)列表
        'ks' => [
            'class' => 'hbhe\settings\Module',
        ],

    ],

    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            //'linkAssets' => env('LINK_ASSETS'),
            'linkAssets' => YII_ENV_DEV ? false : true,
            'appendTimestamp' => YII_ENV_DEV,
            'assetMap' => [
                'jquery.js' => '//cdn.bootcss.com/jquery/2.2.4/jquery.min.js',
                'bootstrap.css' => '//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css',
                'bootstrap.js' => '//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js',
                'jquery-ui.css' => '//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.min.css',
                'jquery-ui.js' => '//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.min.js',
                'fontawesome-all.css' => '//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css',
            ],

            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    //'skin' => 'skin-yellow', // skin-green, skin-yellow, skin-purple, skin-red, skin-black, skin-blue
                ],
            ],
        ],

        'request' => [
            'csrfParam' => '_csrf-backend',
        ],

        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false, // 如果设为false, session到期后自动logout; 设为true, 会利用cookie自动再次登录,除非清掉cookie才会出现登录页面
            'loginUrl' => ['site/login'],
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'as afterLogin' => 'common\behaviors\AfterLoginBehavior',
            'as beforeLogout' => 'common\behaviors\BeforeLogoutBehavior',
        ],

        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
            'class' => 'yii\redis\Session',
            'keyPrefix' => 'backend-',
            'timeout' => 3600,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => require(__DIR__.'/_urlManager.php'),

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
    ],

    'params' => $params,

    'as globalAccess' => [
        'class' => '\common\behaviors\GlobalAccessBehavior',
        'rules' => [
            [
                'controllers' => ['gii/*', 'debug/*'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],

            [
                'controllers' => ['site'],
                'actions' => ['login', 'reset-password'],
                'allow' => true,
                'roles' => ['?'],
            ],

            [
                'controllers' => ['site'],
                'allow' => true,
                'actions' => ['dashboard', 'profile', 'account', 'avatar-upload', 'avatar-delete', 'logout'],
                'roles' => ['@'],
            ],
            [
                'controllers' => ['sign-in'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['get-rest-image-captcha', 'captcha']
            ],


            [
                'controllers' => ['site'],
                'actions' => ['ajax-broker', 'get-rest-image-captcha', 'captcha', 'error'],
                'allow' => true,
                'roles' => ['@', '?'],
            ],

            [
                'controllers' => ['user'],
                'allow' => true,
                'actions' => ['profile', 'account', 'avatar-upload', 'avatar-delete'],
                'roles' => ['@'],
            ],

            [
                'controllers' => ['member'],
                'allow' => true,
                'roles' => ['@'],
            ],

            [
                'controllers' => ['order'],
                'allow' => true,
                'roles' => ['@'],
            ],

            [
                'controllers' => ['message-system', 'message-sm', 'message', 'message-template'],
                'allow' => true,
                'roles' => ['消息中心'],
            ],

            [
                'controllers' => ['settings', 'tag'],
                'allow' => true,
                'roles' => ['参数设置模块'],
            ],

            [
                'controllers' => ['access-log'],
                'actions' => ['index'],
                'allow' => true,
                'roles' => ['日志模块'],
            ],

            [
                'controllers' => ['user'],
                'allow' => true,
                'roles' => ['后台用户模块'],
            ],

            [
                'controllers' => ['content/*'],
                'allow' => true,
                'roles' => ['内容模块'],
            ],

            [
                'controllers' => ['admin/*'],
                'allow' => true,
                'roles' => ['角色权限模块'],
            ],

            [
                'controllers' => ['imagemanager/*', 'i18n/*', 'file-manager-elfinder/*', 'ks/*'],
                'allow' => true,
                'roles' => ['@'],
            ],

            [
                'controllers' => ['picture'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['download']
            ],

            [
                'allow' => true,
                'roles' => [common\models\User::ROLE_ADMINISTRATOR],
            ],

        ]
    ]

];

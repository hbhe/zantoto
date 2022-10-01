<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        //'@hbhe/settings' => '@vendor/hbhe/yii2-settings',
    ],
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'tablePrefix' => getenv('DB_TABLE_PREFIX'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => !YII_DEBUG,
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => getenv('SMTP_HOST'),
                'port' => getenv('SMTP_PORT'),
                'username' => getenv('SMTP_USERNAME'),
                'password' => getenv('SMTP_PASSWORD'),
                'encryption' => 'ssl',
            ],

            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['jack@qq.com' => 'admin'],
            ],

        ],

        'em' => [
            'class' => 'common\models\Easemob',
            'org_name' => getenv('EASEMOB_ORG_NAME'),
            'app_name' => getenv('EASEMOB_APP_NAME'),
            'client_id' => getenv('EASEMOB_CLIENT_ID'),
            'client_secret' => getenv('EASEMOB_CLIENT_SECRET'),
        ],

        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'qq' => [
                    // 在connect.qq.com网站上配置时, 设置回调域要指定到页面如http://xxx.com/site/oauth (不只是域名)
                    'class' => 'hbhe\authclient\Qq',
                    'clientId' => getenv('QQ_CLIENT_ID'),
                    'clientSecret' => getenv('QQ_CLIENT_SECRET'),
                    'normalizeUserAttributeMap' => [
                        'username' => 'nickname',
                        'avatar_url' => 'figureurl_qq_2',
                    ],
                    // 'validateAuthState' => false,
                    // 'has_unionid' => true,
                    'validateAuthState' => false, // 当要提供输入code返回openid的api接口时, 设为false
                    'returnUrl' => 'http://127.0.0.1/zantoto/frontend/web/site/oauth',
                ],

                'weixin_web' => [
                    // 在 https://open.weixin.qq.com 网站上配置时, 授权回调设为www.xx.com, 不用具体到页面,但是xx.com是不行的,
                    'class' => 'hbhe\authclient\Weixin',
                    'clientId' => getenv('WEIXIN_WEB_CLIENT_ID'),
                    'clientSecret' => getenv('WEIXIN_WEB_CLIENT_SECRET'),
                    'scope' => 'snsapi_login',
                    'normalizeUserAttributeMap' => [
                        'id' => 'unionid',
                        'username' => 'nickname',
                        'avatar_url' => 'headimgurl',
                    ],
                    'validateAuthState' => false, // 当要提供输入code返回openid的api接口时, 设为false
                ],

                'weixin_mp' => [
                    // 在微信公众号后台网站上配置时, 授权回调设为www.site.com, 不用具体到页面
                    'class' => 'hbhe\authclient\WeixinMp',
                    'clientId' => getenv('APPID'),
                    'clientSecret' => getenv('APPSECRET'),
                    'scope' => 'snsapi_userinfo', // snsapi_base, snsapi_userinfo
                    'normalizeUserAttributeMap' => [
                        'id' => 'unionid',
                        'username' => 'nickname',
                        'avatar_url' => 'headimgurl',
                    ],
                    'validateAuthState' => false, // 当要提供输入code返回openid的api接口时, 设为false
                ],

                'github' => [
                    'class' => 'yii\authclient\clients\GitHub',
                    'clientId' => getenv('GITHUB_CLIENT_ID'),
                    'clientSecret' => getenv('GITHUB_CLIENT_SECRET'),
                    'normalizeUserAttributeMap' => [
                        'username' => 'login',
                        'avatar_url' => 'avatar_url',
                    ],
                    'validateAuthState' => false,
                    //'returnUrl' => 'http://xxx/site/oauth',  // 用户认证browser跳回来的url, 如果不指定就是当前redirect前的url
                    'returnUrl' => 'http://127.0.0.1/zantoto/frontend/web/site/oauth',
                ],
            ]
        ],

        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1', // 不要用localhost, 很慢
            //'port' => 6379,
            //'database' => 0,
        ],

        // 使用前面定义的redis组件作为cache
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],

        // 使用redis组件作为queue, 需要定义一个redis组件
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            // 'redis' => 'redis',
            // 'channel' => 'queue',
            'as log' => \yii\queue\LogBehavior::class,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => YII_DEBUG ? 1 : 1000,
            'targets' => [
                /*
                'db'=>[
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except'=>['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                    'prefix'=>function () {
                        $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                        return sprintf('[%s][%s]', Yii::$app->id, $url);
                    },
                    'logVars'=>[],
                    'logTable'=>'{{%system_log}}'
                ],
                */
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => YII_DEBUG ? 1 : 1000,
                    'logVars' => [],
                    'levels' => ['error', 'warning'],
                    //'levels' => ['error', 'warning', 'info'],
                    //'levels' => ['error', 'warning', 'profile', 'info', 'trace'],
                ],

                // 测试时打开, 上线后注掉
                'info-application' => [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => YII_DEBUG ? 1 : 1000,
                    'categories' => ['application'],
                    'logVars' => [],
                    'levels' => ['info'],
                ],
            ],
        ],

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'defaultTimeZone' => 'Asia/Shanghai',
            //'nullDisplay' => '-',
        ],

        'mutex' => [
            //'class' => 'yii\mutex\FileMutex',
            'class' => 'yii\mutex\MysqlMutex',
        ],

        'urlManagerBackend' => \yii\helpers\ArrayHelper::merge(
            [
                //'hostInfo' => 'http://127.0.0.1',
                'baseUrl' => Yii::getAlias('@backendUrl'),
            ],
            require(Yii::getAlias('@backend/config/_urlManager.php'))
        ),
        'urlManagerFrontend' => \yii\helpers\ArrayHelper::merge(
            [
                'baseUrl' => Yii::getAlias('@frontendUrl'),
            ],
            require(Yii::getAlias('@frontend/config/_urlManager.php'))
        ),
        'urlManagerStorage' => \yii\helpers\ArrayHelper::merge(
            [
                'baseUrl' => Yii::getAlias('@storageUrl'),
            ],
            require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),

        'authManager' => [
            //'class' => 'yii\rbac\PhpManager',
            'class' => 'yii\rbac\DbManager',
            //'cache' => 'cache',
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}'
        ],

        /*
        'fs' => [
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path' => '@storage/web/source'
            //'path' => '@backend/web/storage/source',
        ],
        */

        // 此组件与 trntv\filekit\actions\UploadAction 配套使用
        'fileStorage' => [
            'class' => trntv\filekit\Storage::class,
            'baseUrl' => '@storageUrl/source',
            //'baseUrl' => '@backendUrl/storage/source',
            // 先保存在文件系统目录中
            //'filesystemComponent'=> 'fs', // 内部使用fs组件保存上传的文件
            'filesystem' => function () {
                // 文件路径前缀为 Yii::$app->fileStorage->filesystem->getAdapter()->getPathPrefix()
                // 完整文件为 $location = $filesystem->getAdapter()->applyPathPrefix($path);
                $adapter = new \League\Flysystem\Adapter\Local(Yii::getAlias('@storage/web/source'));
                return new League\Flysystem\Filesystem($adapter);
            },
            // 然后也在db中记录一下(可选)
            'as log' => [
                'class' => common\behaviors\FileStorageLogBehavior::class,
                'component' => 'fileStorage'
            ]
        ],

        'ks' => [
            'class' => hbhe\settings\models\KeyStorage::class
        ],

        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
                '*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'common' => 'common.php',
                        'backend' => 'backend.php',
                        'frontend' => 'frontend.php',
                    ],
                    //'on missingTranslation' => [backend\modules\translation\Module::class, 'missingTranslation']
                ],
                /* Uncomment this code to use DbMessageSource
                 '*'=> [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%i18n_source_message}}',
                    'messageTable'=>'{{%i18n_message}}',
                    'enableCaching' => YII_ENV_DEV,
                    'cachingDuration' => 3600,
                    //'on missingTranslation' => ['\backend\modules\translation\Module', 'missingTranslation']
                ],
                */
            ],
        ],

        // 短信组件
        'sm' => new \Overtrue\EasySms\EasySms([
            'timeout' => 5.0,
            'default' => [
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => [
                    YII_DEBUG ? 'errorlog' : 'aliyun',
                    //'aliyun',
                    //'alidayu',
                ],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => Yii::getAlias('@common/runtime/easy-sms.log'),
                ],

                'aliyun' => [
                    'access_key_id' => getenv('ALIYUN_APP_ID'),
                    'access_key_secret' => getenv('ALIYUN_APP_SECRET'),
                    'sign_name' => getenv('ALIYUN_APP_SIGN_NAME'),
                ],
            ],
        ]),

        // noam148图片显示组件
        'imagemanager' => [
            'class' => 'noam148\imagemanager\components\ImageManagerGetPath',
            'mediaPath' => Yii::getAlias('@backend/web/image-upload'),
            'databaseComponent' => 'db', // The used database component by the image manager, this defaults to the Yii::$app->db component
            // 以下其实是对ImageResize的配置
            //path relative web folder to store the cache images
            'cachePath' => ['assets/image-cache'],
            //use filename (seo friendly) for resized images else use a hash
            'useFilename' => true,
            //show full url (for example in case of a API)
            'absoluteUrl' => true,
        ],

        'imageresize' => [
            'class' => 'noam148\imageresize\ImageResize',
            //path relative web folder. In case of multiple environments (frontend, backend) add more paths
            //'cachePath' =>  ['assets/images', '../../frontend/web/assets/images'],
            'cachePath' => ['assets/image-cache'],
            //use filename (seo friendly) for resized images else use a hash
            'useFilename' => true,
            //show full url (for example in case of a API)
            'absoluteUrl' => true,
        ],
/*
        'balanceManager' => [
            // 'class' => 'yii2tech\balance\ManagerDb',
            // 'accountTable' => '{{%member}}', // 用户表
            // 'transactionTable' => '{{%revenue_log}}',
            'class' => 'yii2tech\balance\ManagerActiveRecord',
            'accountClass' => 'common\models\Member', // 用户表
            'transactionClass' => 'common\models\RevenueLog', // 交易记录表

            'accountBalanceAttribute' => 'balance_revenue', // 用户表中余额字段
            'accountLinkAttribute' => 'member_id',
            'amountAttribute' => 'amount',
            'dateAttribute' => 'updated_at',
            'dateAttributeValue' => date('Y-m-d H:i:s'),
            'extraAccountLinkAttribute' => 'reason', // other_member_id, 对方账号id, 冲正时要用, 字段default value须为null
            'dataAttribute' => 'memo', // json串
        ],
*/
    ],

];

<?php
use common\models\Category;
use common\models\Startup;
use common\models\Tag;
use common\models\User;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel hide">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form hide">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => '', 'options' => ['class' => 'header']],
                    [
                        'label' => '概况',
                        'icon' => 'home',
                        'url' => ['/site/dashboard'], // index
                    ],

                    [
                        'label' => '用户管理',
                        'url' => '#',
                        'icon' => 'users',
                        'options' => ['class' => 'treeview'],
                        'visible' =>  Yii::$app->user->can('用户列表') || Yii::$app->user->can('用户审核'),
                        'items' => [
                            [
                                'label' => '用户列表',
                                'icon' => 'angle-double-right',
                                'url' => ['/member/index'],
                                'active' => Yii::$app->controller->id == 'member' && in_array(Yii::$app->controller->action->id, ['index', 'create', 'update', 'view']),
                                'visible' => Yii::$app->user->can('用户列表'),
                            ],

                        ],
                    ],

                    [
                        'label' => '商品管理',
                        'url' => '#',
                        'icon' => 'shopping-cart',
                        'options' => ['class' => 'treeview'],
                        //'visible' =>  Yii::$app->user->can('内容模块'),
                        'items' => [
                            [
                                'label' => '平台商品',
                                'icon' => 'angle-double-right',
                                'url' => ['/product/index', 'is_platform' => 1],
                                'active' => in_array(Yii::$app->controller->id, ['product', 'product-option', 'sku']) && Yii::$app->request->get('is_platform') == 1,
                            ],
                            [
                                'label' => '商户商品',
                                'icon' => 'angle-double-right',
                                'url' => ['/product/index', 'is_platform' => 0], // 'is_platform' => null 表示全部商品
                                'active' => in_array(Yii::$app->controller->id, ['product', 'product-option', 'sku']) && Yii::$app->request->get('is_platform') == 0,
                            ],

                        ],
                    ],

                    [
                        'label' => '订单管理',
                        'url' => '#',
                        'icon' => 'list',
                        'options' => ['class' => 'treeview'],
                        'visible' =>  Yii::$app->user->can('订单列表') || Yii::$app->user->can('订单审核'),
                        'items' => [
                            [
                                'label' => '平台订单',
                                'icon' => 'angle-double-right',
                                'url' => ['/order/index', 'is_platform' => 1],
                                'active' => in_array(Yii::$app->controller->id, ['order']) && Yii::$app->request->get('is_platform') == 1,
                                'visible' => Yii::$app->user->can('订单列表'),
                            ],
                            [
                                'label' => '购物车',
                                'icon' => 'angle-double-right',
                                'url' => ['/cart/index'],
                                'active' => in_array(Yii::$app->controller->id, ['cart']),
                                'visible' => YII_ENV_DEV,
                            ],
                            [
                                'label' => '退货列表',
                                'icon' => 'angle-double-right',
                                'url' => ['/order-sku-refund/index'], // 'need_ship' => 1
                                'active' => Yii::$app->controller->id == 'order-sku-refund' && in_array(Yii::$app->controller->action->id, ['index', 'create', 'update', 'view']),
                                'visible' => Yii::$app->user->can('订单列表'),
                            ],
                            [
                                'label' => '退款列表',
                                'icon' => 'angle-double-right',
                                'url' => ['/order-sku-refund-money/index'],
                                'active' => Yii::$app->controller->id == 'order-sku-refund-money' && in_array(Yii::$app->controller->action->id, ['index', 'create', 'update', 'view']),
                                'visible' => Yii::$app->user->can('订单列表'),
                            ],

                            [
                                'label' => '商户订单',
                                'icon' => 'angle-double-right',
                                'url' => ['/order/index', 'is_platform' => 0],
                                'active' => in_array(Yii::$app->controller->id, ['order']) && Yii::$app->request->get('is_platform') == 0,
                                'visible' => Yii::$app->user->can('订单列表'),
                            ],

                        ],
                    ],

                    [
                        'label' => '评价管理',
                        'url' => '#',
                        'icon' => 'comment',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            [
                                'label' => '商品评价',
                                'icon' => 'angle-double-right',
                                'url' => ['/rate/index', 'is_platform' => 1],
                                'active' => in_array(Yii::$app->controller->id, ['rate']),
                            ],
                        ],
                    ],

                    [
                        'label' => '内容管理',
                        'url' => '#',
                        'icon' => 'book',
                        'options' => ['class' => 'treeview'],
                        'visible' =>  Yii::$app->user->can('内容模块'),
                        'items' => [
                            [
                                'label' => '内容列表',
                                'icon' => 'angle-double-right',
                                'url' => ['/content/article/index'],
                                'active' => Yii::$app->controller->id == 'article' && in_array(Yii::$app->controller->action->id, ['index', 'create', 'update']),
                            ],

                            [
                                'label' => '内容分类',
                                'icon' => 'angle-double-right',
                                'url' => ['/content/article-category/index'],
                                'active' => Yii::$app->controller->id == 'article-category' && in_array(Yii::$app->controller->action->id, ['index', 'create', 'update']),

                            ],
                        ],
                    ],

                    [
                        'label' => '操作员管理',
                        'visible' =>  Yii::$app->user->can('后台用户模块') || Yii::$app->user->can('日志模块'),
                        'url' => '#',
                        'icon' => 'user-circle',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            [
                                'label' => '操作员列表',
                                'icon' => 'angle-double-right',
                                'url' => ['/user/index'],
                                'active' => Yii::$app->controller->id == 'user' && in_array(Yii::$app->controller->action->id, ['index', 'update']),
                                'visible' => Yii::$app->user->can('后台用户模块'),
                            ],

                            [
                                'label' => '操作日志',
                                'icon' => 'angle-double-right"',
                                'url' => ['/access-log/index'],
                                'visible' =>  Yii::$app->user->can('日志模块'),
                            ],

                        ],
                    ],

                    [
                        'label' => '角色权限管理',
                        'visible' =>  Yii::$app->user->can('角色权限模块'),
                        'url' => '#',
                        'icon' => 'diamond',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            [
                                'label' => '角色管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/role/index'],
                                'active' => in_array(Yii::$app->controller->id, ['role']),
                            ],
                            [
                                'label' => '分配角色',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/assignment/index'],
                                'active' => in_array(Yii::$app->controller->id, ['assignment']),
                            ],
                            [
                                'label' => '菜单管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/menu/index'],
                                'visible' => false,
                            ],
                            [
                                'label' => '权限管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/route/index'],
                                'visible' => false,
                            ],
                        ],
                    ],

                    [
                        'label' => '参数管理',
                        'visible' =>  Yii::$app->user->can('参数设置模块'),
                        'url' => '#',
                        'icon' => 'gear',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            [
                                'label' => 'BANNER管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/banner/index', 'cat' => common\models\Banner::CAT_HOMEPAGE_SLIDE],
                                'active' => Yii::$app->controller->id == 'banner',
                            ],

                            [
                                'label' => '商品规格配置',
                                'icon' => 'angle-double-right',
                                'url' => ['/option/index'],
                                'active' => in_array(Yii::$app->controller->id, ['option', 'option-value']),
                            ],

                            [
                                'label' => '商品分类管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/category/index', 'parent_id' => Category::ROOT_ID],
                                'active' => in_array(Yii::$app->controller->id, ['category', 'category-option']),
                            ],
                            [
                                'label' => '店铺分类管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/shop-category/index', 'parent_id' => 0],
                                'active' => in_array(Yii::$app->controller->id, ['outlet-category']),
                            ],
                            [
                                'label' => '全局设置',
                                'icon' => 'angle-double-right',
                                'url' => ['/settings/order'],
                                'active' => in_array(Yii::$app->controller->id, ['settings']),
                            ],
                            [
                                'label' => '图片库',
                                'icon' => 'angle-double-right',
                                'url' => ['/picture/index'],
                                'active' => in_array(Yii::$app->controller->id, ['picture']),
                                'visible' => YII_ENV_DEV,
                            ],
                        ],
                    ],

                    [
                        'label' => '键值对',
                        'icon' => 'list',
                        'url' => ['/ks/key-storage/index'],
                        // 'visible' =>  Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
                        'visible' => YII_ENV_DEV,
                    ],

/*
                    ['label' => 'Gii', 'icon' => 'file', 'url' => ['/gii'], 'visible' =>  YII_ENV_DEV],
                    ['label' => 'Debug', 'icon' => 'bug', 'url' => ['/debug'], 'visible' =>  YII_ENV_DEV],

                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Some tools',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
*/
                ],
            ]
        ) ?>

    </section>

</aside>

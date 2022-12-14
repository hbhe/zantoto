<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use hbhe\settings\models\FormModel;
use Yii;

/**
 * Settings controller
 */
class SettingsController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'frontend.maintenance' => [
                    'label' => Yii::t('backend', 'Frontend maintenance mode'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'disabled' => Yii::t('backend', 'Disabled'),
                        'enabled' => Yii::t('backend', 'Enabled')
                    ]
                ],
                'backend.theme-skin' => [
                    'label' => Yii::t('backend', 'Backend theme'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'skin-black' => 'skin-black',
                        'skin-blue' => 'skin-blue',
                        'skin-green' => 'skin-green',
                        'skin-purple' => 'skin-purple',
                        'skin-red' => 'skin-red',
                        'skin-yellow' => 'skin-yellow'
                    ]
                ],
                'backend.layout-fixed' => [
                    'label' => Yii::t('backend', 'Fixed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-boxed' => [
                    'label' => Yii::t('backend', 'Boxed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-collapsed-sidebar' => [
                    'label' => Yii::t('backend', 'Backend sidebar collapsed'),
                    'type' => FormModel::TYPE_CHECKBOX
                ]
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '????????????!');
            return $this->refresh();
        }

        return $this->render('index', [
            'title' => '????????????',
            'model' => $model
        ]);
    }

    /*
     * Yii::$app->ks->get('demo.number', 0)
     */
    public function actionDemo()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'demo.text' => [
                    'label' => '?????????',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['string', 'min' => 1, 'max' => 8]],
                ],
                'demo.number' => [
                    'label' => '?????????(1~100)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1, 'max' => 100]],
                ],
                'demo.dropdown' => [
                    'label' => '?????????',
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'AAAA' => 'AAAA',
                        'BBBB' => 'BBBB',
                    ]
                ],
                'demo.check' => [
                    'label' => '?????????',
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'demo.date' => [
                    'label' => '??????(?????????2019-01-01 01:01:01)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 24,],
                    'rules' => [['date', 'format' => 'php:Y-m-d H:i:s']],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '????????????!');
            return $this->refresh();
        }
        Yii::info([
            Yii::$app->ks->get('demo.number', 0),
        ]);
        return $this->render('index', [
            'title' => 'DEMO????????????',
            'model' => $model
        ]);
    }

    /**
     * Yii::$app->ks->get('order.max_wait_pay_minutes', 120)
     * Yii::$app->ks->get('order.max_wait_confirm_receive', 15)
     * Yii::$app->ks->get('order.max_wait_complain', 7)
     * Yii::$app->ks->get('order.max_wait_rate', 7)
     * @return string|\yii\web\Response
     */
    public function actionOrder()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'order.max_wait_pay_minutes' => [
                    'label' => '??????????????????xx????????????????????????????????????',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'order.max_wait_confirm_receive' => [
                    'label' => '????????????????????????xx?????????????????????????????????',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'order.max_wait_complain' => [
                    'label' => '??????????????????xx???, ???????????????????????????????????????',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'order.max_wait_rate' => [
                    'label' => '??????????????????xx???, ??????????????????',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '????????????!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '????????????????????????',
            'model' => $model
        ]);
    }

    /**
     * Yii::$app->ks->get('cashout.fee_ratio', 0)
     * Yii::$app->ks->get('cashout.min_amount_per_time', 1)
     * Yii::$app->ks->get('cashout.max_amount_per_time', 99999999)
     * Yii::$app->ks->get('cashout.max_time_per_day', 3)
     * Yii::$app->ks->get('cashout.need_audit', 1)
     * Yii::$app->ks->get('cashout.audit_threshold', 50)
     * @return string|\yii\web\Response
     */
    public function actionCashout()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'cashout.fee_ratio' => [
                    'label' => '????????????(0~1, ???0.12??????12%)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0, 'max' => 1]],
                ],
                'cashout.min_amount_per_time' => [
                    'label' => '?????????????????????????????????xx???',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'cashout.max_amount_per_time' => [
                    'label' => '?????????????????????????????????xx???',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'cashout.max_time_per_day' => [
                    'label' => '????????????????????????',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'cashout.need_audit' => [
                    'label' => '??????????????????',
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'cashout.audit_threshold' => [
                    'label' => '????????????xx???????????????',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '????????????!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '??????????????????',
            'model' => $model
        ]);
    }

    /**
     * Yii::$app->ks->get('revenue.open_star_member_money', 368)
     * Yii::$app->ks->get('revenue.bonus_per_star_member', 99)
     * Yii::$app->ks->get('revenue.training_bonus_ratio', 0.04)
     */
    public function actionRevenue()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'revenue.open_star_member_money' => [
                    'label' => '??????????????????(???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0]],
                ],
                'revenue.bonus_per_star_member' => [
                    'label' => '????????????(???)', // ??????????????????????????????????????????????????????
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0]],
                ],
                'revenue.training_bonus_ratio' => [
                    'label' => '????????????(0~1, ???0.12??????12%)', // ??????????????????????????????X??????????????????????????????????????????
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0, 'max' => 1]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '????????????!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '??????????????????',
            'model' => $model
        ]);
    }

    /**
     * Yii::$app->ks->get('power.beat_card', 1)
     * @return string|\yii\web\Response
     */
    public function actionPower()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'power.beat_card' => [
                    'label' => '????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.thumbup' => [
                    'label' => '???????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.reply_post' => [
                    'label' => '????????????????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.share_post' => [
                    'label' => '????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.create_post' => [
                    'label' => '????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.share_product_make_order' => [
                    'label' => '??????????????????????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.make_order_in_seller' => [
                    'label' => '?????????????????????????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.make_order' => [
                    'label' => '????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.rate_order' => [
                    'label' => '????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.invite_member' => [
                    'label' => '????????????????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.invite_member_star' => [
                    'label' => '????????????????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.max_increase_power_per_day' => [
                    'label' => '????????????????????????(???/???)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '????????????!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '????????????????????????',
            'model' => $model
        ]);
    }


}

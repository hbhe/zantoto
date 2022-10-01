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
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }

        return $this->render('index', [
            'title' => '主题设置',
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
                    'label' => '文本框',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['string', 'min' => 1, 'max' => 8]],
                ],
                'demo.number' => [
                    'label' => '数字框(1~100)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1, 'max' => 100]],
                ],
                'demo.dropdown' => [
                    'label' => '下拉框',
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'AAAA' => 'AAAA',
                        'BBBB' => 'BBBB',
                    ]
                ],
                'demo.check' => [
                    'label' => '勾选框',
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'demo.date' => [
                    'label' => '日期(格式如2019-01-01 01:01:01)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 24,],
                    'rules' => [['date', 'format' => 'php:Y-m-d H:i:s']],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        Yii::info([
            Yii::$app->ks->get('demo.number', 0),
        ]);
        return $this->render('index', [
            'title' => 'DEMO参数设置',
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
                    'label' => '正常订单超过xx分钟未付款，订单自动关闭',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'order.max_wait_confirm_receive' => [
                    'label' => '已发货后买家超过xx天未收货，订单自动完成',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'order.max_wait_complain' => [
                    'label' => '订单完成超过xx天, 自动结束交易，不能申请售后',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'order.max_wait_rate' => [
                    'label' => '订单完成超过xx天, 自动三星好评',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '订单交易参数设置',
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
                    'label' => '提现费率(0~1, 如0.12表示12%)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0, 'max' => 1]],
                ],
                'cashout.min_amount_per_time' => [
                    'label' => '单次提现金额最少不低于xx元',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'cashout.max_amount_per_time' => [
                    'label' => '单次提现金额最大不超过xx元',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'cashout.max_time_per_day' => [
                    'label' => '每日提现次数限制',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
                'cashout.need_audit' => [
                    'label' => '提现需要审核',
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'cashout.audit_threshold' => [
                    'label' => '提现超过xx元需要审核',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '提现参数设置',
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
                    'label' => '开通会员费用(元)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0]],
                ],
                'revenue.bonus_per_star_member' => [
                    'label' => '推荐奖励(元)', // 每成功发展一个会员奖励一定金额的收入
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0]],
                ],
                'revenue.training_bonus_ratio' => [
                    'label' => '提现奖励(0~1, 如0.12表示12%)', // 下级提现金额的百分之X作为本人的奖励，只限一级下级
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 0, 'max' => 1]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '推荐体系设置',
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
                    'label' => '到点打卡(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.thumbup' => [
                    'label' => '给别人点赞(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.reply_post' => [
                    'label' => '给别人评论及回复(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.share_post' => [
                    'label' => '分享动态(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.create_post' => [
                    'label' => '发布动态(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.share_product_make_order' => [
                    'label' => '分享商品他人成功购买(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.make_order_in_seller' => [
                    'label' => '小铺中商品他人成功购买(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.make_order' => [
                    'label' => '购买订单(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.rate_order' => [
                    'label' => '评价订单(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.invite_member' => [
                    'label' => '邀请成功注册会员(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.invite_member_star' => [
                    'label' => '邀请成功星钻会员(分/次)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
                'power.max_increase_power_per_day' => [
                    'label' => '每日原力获得上限(分/天)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['integer', 'min' => 0]],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        return $this->render('index', [
            'title' => '星球原力产生规则',
            'model' => $model
        ]);
    }


}

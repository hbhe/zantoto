<?php
/**
 *  @link http://github.com/hbhe/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\ResetPasswordForm;
use common\models\Member;
use common\models\MemberSearch;
use common\models\Order;
use common\models\OrderSearch;
use common\models\User;
use common\wosotech\base\Controller;
use common\wosotech\helper\CaptchaHelper;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;

//use yii\web\Controller;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionIndex()
    {
        return 'Hello, world';

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $query = $dataProvider->query;
        $total = $query->cache(YII_ENV_DEV ? 3 : 3600)->select(['count(*) AS count', 'sum(pay_amount) AS amount'])->asArray()->one(); // total_amount
        $valid = $query->cache(YII_ENV_DEV ? 3 : 3600)->select(['count(*) AS count', 'sum(pay_amount) AS amount'])->valid()->asArray()->one();
        $invalid = $query->cache(YII_ENV_DEV ? 3 : 3600)->select(['count(*) AS count', 'sum(total_amount) AS amount'])->invalid()->asArray()->one();
        $done = $query->cache(YII_ENV_DEV ? 3 : 3600)->select(['count(*) AS count', 'sum(total_amount) AS amount'])->done()->asArray()->one();

        $orderData = [
            'total_amount' => $total['amount'] ?? 0,
            'valid_count' => $valid['count'] ?? 0,
            'valid_amount' => $valid['amount'] ?? 0,
            'invalid_count' => $invalid['count'] ?? 0,
            'invalid_amount' => $invalid['amount'] ?? 0,
            'done_count' => $done['count'] ?? 0,
            'done_amount' => $done['amount'] ?? 0,
        ];

        $query = Member::find();
        $userData = [
            [
                'title' => '用户',
                'total' => Member::find()->cache(YII_ENV_DEV ? 3 : 3600)->count(),
                'today' => Member::find()->cache(YII_ENV_DEV ? 3 : 3600)->today()->count(),
                'month' => Member::find()->cache(YII_ENV_DEV ? 3 : 3600)->month()->count(),
            ],

        ];
        $userDataProvider = new ArrayDataProvider([
            'allModels' => $userData,
            'pagination' => false,
        ]);

        $realNameData = $searchModel->search(Yii::$app->request->queryParams)->query
            ->cache(YII_ENV_DEV ? 3 : 3600)
            ->joinWith('buyer m')
//            ->andWhere(['m.is_real_name' => 1])
            ->select(['member_id', 'buyer_id', 'count(*) AS count', 'sum(pay_amount) AS amount'])
            ->asArray()
            ->one();
        $realNameHeadcount = (new MemberSearch())->search(Yii::$app->request->queryParams)->query
            ->cache(YII_ENV_DEV ? 3 : 3600)
            //          ->andWhere(['is_real_name' => 1])
            ->count();

        $userOrderData = [
            [
                'title' => '普通用户',
                'headcount' => $realNameHeadcount,
                'amount' => $amount = $realNameData['amount'] ?? 0,
                'count' => $count = $realNameData['count'] ?? 0,
                'amount_per_member' => empty($realNameHeadcount) ? '' : $amount / $realNameHeadcount,
                'count_per_member' => empty($realNameHeadcount) ? '' : $count / $realNameHeadcount,
            ],
        ];

        $userOrderDataProvider = new ArrayDataProvider([
            'allModels' => $userOrderData,
            'pagination' => false,
        ]);

        return $this->render('index', [
            'model' => $searchModel,
            'orderData' => $orderData,
            'userDataProvider' => $userDataProvider,
            'userOrderDataProvider' => $userOrderDataProvider,
            'couponData' => [],
        ]);
    }

    public function actionDashboard()
    {
        Yii::$app->user->setReturnUrl(Url::current());
        $memberSearchModel = new MemberSearch();
        $memberDataProvider = $memberSearchModel->search(['status' => Member::STATUS_NOT_ACTIVE]);
        $memberDataProvider->pagination = [
            'defaultPageSize' => 3,
        ];

        $orderSearchModel = new OrderSearch();
        $orderDataProvider = $orderSearchModel->search(['status' => Order::STATUS_PAID]);
        $orderDataProvider->pagination = [
            'defaultPageSize' => 3,
        ];

        return $this->render('dashboard', [
            'memberStat' => self::getMemberStat(),
            'orderStat' => self::getOrderStat(),
            'memberDataProvider' => $memberDataProvider,
            'orderDataProvider' => $orderDataProvider,
            'orderChartData' => self::getOrderChartData(Yii::$app->request->get('range_order', 'month')),
            'tagData' => self::getProvinceOrderChartData(Yii::$app->request->get('range_tag', 'month')),
        ]);
    }

    public static function getMemberStat()
    {
        $key = [__METHOD__];
        if (false !== $data = yii::$app->cache->get($key)) {
            return $data;
        }
        $total = Member::find()->count();
        $day = Member::find()->today()->count();
        $day7 = Member::find()->yesterday()->count();
        $day30 = Member::find()->month()->count();
        $result = ['total' => $total, 'day' => $day, 'day7' => $day7, 'day30' => $day30];
        Yii::$app->cache->set($key, $result, YII_ENV_DEV ? 1 : 600);

        return $result;
    }

    public static function getOrderStat()
    {
        $key = [__METHOD__];
        if (false !== $data = yii::$app->cache->get($key)) {
            return $data;
        }
        $total = Order::find()->count();
        $day = Order::find()->today()->count();
        $day7 = Order::find()->yesterday()->count();
        $day30 = Order::find()->month()->count();
        $result = ['total' => $total, 'day' => $day, 'day7' => $day7, 'day30' => $day30];
        Yii::$app->cache->set($key, $result, YII_ENV_DEV ? 1 : 3600);

        return $result;
    }

    public static function getOrderChartData($range = 'day')
    {
        $key = [__METHOD__, $range];
        if (false !== $data = yii::$app->cache->get($key)) {
            return $data;
        }
        $result = [];
        if ($range == 'day') {
            $date = date("Y-m-d");
            $models = Order::find()
                ->select(['*', 'count(*) as count', 'HOUR(created_at) as hour'])
                ->where(['DATE(created_at)' => $date])
                ->groupBy(['HOUR(created_at)'])
                ->asArray()
                ->all();

            $rows = ArrayHelper::map($models, 'hour', 'count');
            $data = [];
            for ($i = 0; $i < 24; $i++) {
                $data[] = isset($rows[$i]) ? $rows[$i] : 0;
            }
            $result = array_combine(['0:00', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'], $data);
        }

        if ($range == 'week') {
            $date = date("Y-m-d");
            // DAYOFWEEK: 1->星期天, 2->星期一, ...
            $models = Order::find()
                ->select(['*', 'count(*) as count', 'DAYOFWEEK(created_at) as week'])
                ->where(['yearweek(created_at)' => new \yii\db\Expression("yearweek(\"$date\")")])
                ->groupBy(['DAYOFWEEK(created_at)'])
                ->asArray()
                ->all();

            $rows = ArrayHelper::map($models, 'week', 'count');
            $data = [];
            for ($i = 1; $i <= 7; $i++) {
                $data[] = isset($rows[$i]) ? $rows[$i] : 0;
            }
            $result = array_combine(['周日', '周一', '周二', '周三', '周四', '周五', '周六'], $data);
        }

        if ($range == 'month') {
            $date = date("Y-m-d");
            $models = Order::find()
                ->select(['*', 'count(*) as count', 'DAY(created_at) as day'])
                ->where([
                    'YEAR(created_at)' => new \yii\db\Expression("YEAR(\"$date\")"),
                    'MONTH(created_at)' => new \yii\db\Expression("MONTH(\"$date\")"),
                ])
                ->groupBy(['DAY(created_at)'])
                ->asArray()
                ->all();

            $rows = ArrayHelper::map($models, 'day', 'count');
            $result = [];
            $max = date("t"); // 当月最后一天
            for ($i = 1; $i <= $max; $i++) {
                $result[$i] = isset($rows[$i]) ? $rows[$i] : 0;
            }
        }
        Yii::$app->cache->set($key, $result, YII_ENV_DEV ? 1 : 600);

        return $result;
    }

    /**
     * 销量排行前10的省份
     * @param string $range
     * @return array|mixed
     */
    public static function getProvinceOrderChartData($range = 'day')
    {
        $key = [__METHOD__, $range];
        if (false !== $data = yii::$app->cache->get($key)) {
            return $data;
        }

        $date = date("Y-m-d");
        $query = Order::find()
            ->alias('order')
            ->joinWith('parentAreaCode area')
            ->select(['*', 'count(*) as count',])
            ->groupBy(['shipping_area_parent_id'])
            ->limit(10)
            ->asArray();

        if ($range == 'day') {
            $query->where(['DATE(order.created_at)' => $date]);
        }
        if ($range == 'week') {
            $query->where(['yearweek(order.created_at)' => new \yii\db\Expression("yearweek(\"$date\")")]);
        }
        if ($range == 'month') {
            $query->where([
                'YEAR(order.created_at)' => new \yii\db\Expression("YEAR(\"$date\")"),
                'MONTH(order.created_at)' => new \yii\db\Expression("MONTH(\"$date\")"),
            ]);
        }
        $models = $query->all();
        $rows = ArrayHelper::map($models, 'name', 'count');
        $result = $rows;
        Yii::$app->cache->set($key, $result, YII_ENV_DEV ? 1 : 3600);

        return $result;
    }

    public function actionResetPassword()
    {
        $this->layout = 'main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new ResetPasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findByUsername($model->username);
            if (null !== $user) {
                $user->setPassword($model->password);
                if ($user->save(false)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }

    // <img src="http://127.0.0.1/zantoto/backend/web/site/get-rest-image-captcha" />
    public function actionGetRestImageCaptcha()
    {
        $this->layout = false;
        $helper = new CaptchaHelper();
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/png');
        $response->format = Response::FORMAT_RAW;
        $response->content = $helper->generateImage(false);
        return $response->send();
    }
}

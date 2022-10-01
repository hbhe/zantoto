<?php
use common\models\Member;
use common\models\Order;
use dosamigos\chartjs\ChartJs;
use rmrevin\yii\fontawesome\FontAwesome;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = '概况';
$this->params['breadcrumbs'][] = $this->title;

/* @var $model \common\models\Todo */
?>

<!--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
<link rel="stylesheet" href="//cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css">

<section class="content">

    <div class="row">

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-person-add"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">今日新增</span>
                    <span class="info-box-number"><?= $memberStat['day'] ?></span>
                    <a href="<?php echo Url::to(['/member/index',]); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-person-add"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">昨日新增</span>
                    <span class="info-box-number"><?= $memberStat['day7'] ?></span>
                    <a href="<?php echo Url::to(['/member/index',]); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-person-add"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">本月</span>
                    <span class="info-box-number"><?= $memberStat['day30'] ?></span>
                    <a href="<?php echo Url::to(['/member/index',]); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-person-add"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">用户总数</span>
                    <span class="info-box-number"><?= $memberStat['total'] ?></span>
                    <a href="<?php echo Url::to(['/member/index',]); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-bag"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">今日新增</span>
                    <span class="info-box-number"><?= $orderStat['day'] ?></span>
                    <a href="<?php echo Url::to(['/order/index']); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-bag"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">昨日新增</span>
                    <span class="info-box-number"><?= $orderStat['day7'] ?></span>
                    <a href="<?php echo Url::to(['/order/index']); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-bag"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">本月新增</span>
                    <span class="info-box-number"><?= $orderStat['day30'] ?></span>
                    <a href="<?php echo Url::to(['/order/index']); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-bag"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">订单总数</span>
                    <span class="info-box-number"><?= $orderStat['total'] ?></span>
                    <a href="<?php echo Url::to(['/order/index']); ?>" class="hide small-box-footer">more <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

    </div>

    <br/>
    <div class="row">
        <?php Pjax::begin() ?>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <?php /* echo
            \dosamigos\highcharts\HighCharts::widget([
                'clientOptions' => [
                    'chart' => [
                        'type' => 'bar'
                    ],
                    'title' => [
                        'text' => 'Fruit Consumption'
                    ],
                    'xAxis' => [
                        'categories' => [
                            'Apples',
                            'Bananas',
                            'Oranges'
                        ]
                    ],
                    'yAxis' => [
                        'title' => [
                            'text' => 'Fruit eaten'
                        ]
                    ],
                    'series' => [
                        ['name' => 'Jane', 'data' => [1, 0, 4]],
                        ['name' => 'John', 'data' => [5, 7, 3]]
                    ]
                ]
            ]);  */
            ?>
            <?= ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'height' => 200,
                    //'width' => 400
                ],
                'data' => [
                    //'labels' => ["January", "February", "March", "April", "May", "June", "July"],
                    'labels' => array_keys($orderChartData),
                    'datasets' => [
                        [
                            'label' => "订单量",
                            'backgroundColor' => "rgba(179,181,198,0.2)",
                            'borderColor' => "rgba(179,181,198,1)",
                            'pointBackgroundColor' => "rgba(179,181,198,1)",
                            'pointBorderColor' => "#fff",
                            'pointHoverBackgroundColor' => "#fff",
                            'pointHoverBorderColor' => "rgba(179,181,198,1)",
                            //'data' => [65, 59, 90, 81, 56, 55, 40]
                            'data' => array_values($orderChartData),
                        ],
                    ]
                ]
            ]);
            ?>

            <div>
                <?php echo Html::a('日  ', ['/site/dashboard', 'range_order' => 'day']) ?>
                <?php echo Html::a('周  ', ['/site/dashboard', 'range_order' => 'week']) ?>
                <?php echo Html::a('月  ', ['/site/dashboard', 'range_order' => 'month']) ?>
            </div>
        </div>
        <?php Pjax::end() ?>

        <?php Pjax::begin(['id' => 'pjax2']) ?>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <?php
            echo ChartJs::widget([
                'type' => 'pie',
                'id' => 'structurePie',
                'options' => [
                    'height' => 280,
                    //'width' => 400,
                ],
                'data' => [
                    //'radius' => "90%",
                    //'labels' => ['Label 1', 'Label 2', 'Label 3'], // Your labels
                    'labels' => array_keys($tagData),
                    'datasets' => [
                        [
                            //'data' => ['35.6', '17.5', '46.9'], // Your dataset
                            'data' => array_values($tagData),
                            'label' => 'xxx',
                            'backgroundColor' => [
                                "#F7464A",
                                "#46BFBD",
                                "#FDB45C",
                                "#949FB1",
                                '#76a27b',
                                '#cc65fe',
                                '#ffce56',
                                '#ADC3FF',
                                '#FF9A9A',
                                "#f38b4a",
                                "#56d798",
                                "#6970d5",
                                "#1f8397",
                                'rgba(190, 124, 145, 0.8)'
                            ],
                            'borderColor' => [
                                '#fff',
                                '#fff',
                                '#fff',
                                '#fff',
                                '#fff',
                                '#fff',
                                '#fff',
                                '#fff',
                                '#fff',
                                '#fff',
                            ],
                            'borderWidth' => 1,
                            'hoverBorderColor' => ["#999", "#999", "#999"],
                        ]
                    ]
                ],
                'clientOptions' => [
                    'title' => [
                        'display' => false,
                        'text' => '省份统计图',
                    ],
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                        'labels' => [
                            'fontSize' => 14,
                            'fontColor' => "#425062",
                        ]
                    ],
                    'tooltips' => [
                        'enabled' => true,
                        'intersect' => true,
                        'callbacks' => [
                          'label' => new \yii\web\JsExpression("
                                function(tooltipItem, data) {
                                    var label = data.labels[tooltipItem.index] || '';
                                    var dataset = data.datasets[tooltipItem.datasetIndex];
                                    var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                                        return previousValue + currentValue;
                                    });
                                    var currentValue = dataset.data[tooltipItem.index];
                                    // var percentage = Math.floor(((currentValue/total) * 100) + 0.5);
                                    var percentage = ((currentValue / total) * 100).toFixed(2) + '%';                                    
                                    return label + ': ' + currentValue + ' (' + percentage + ') ';
                                }
                            "),
                        ],
                    ],
                    'hover' => [
                        'mode' => false
                    ],
                    'maintainAspectRatio' => false,
                ],
/*
                'plugins' => new \yii\web\JsExpression("[

                        {
                            afterDatasetsDraw: function(chart, easing) {
                                var ctx = chart.ctx;
                                chart.data.datasets.forEach(function (dataset, i) {
                                    var meta = chart.getDatasetMeta(i);
                                    if (!meta.hidden) {
                                        meta.data.forEach(function(element, index) {
                                            // Draw the text in black, with the specified font
                                            ctx.fillStyle = 'rgb(0, 0, 0)';

                                            var fontSize = 16;
                                            var fontStyle = 'normal';
                                            var fontFamily = 'Helvetica';
                                            ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                                            // Just naively convert to string for now
                                            var dataString = dataset.data[index].toString()+'%';

                                            // Make sure alignment settings are correct
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';

                                            var padding = 5;
                                            var position = element.tooltipPosition();
                                            ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                        });
                                    }
                                });
                            }
                        }

                ]")
*/
            ])
            ?>
        </div>

        <div style="margin:20px;">
        <?php echo Html::a('日  ', ['/site/dashboard', 'range_tag' => 'day']) ?>
        <?php echo Html::a('周  ', ['/site/dashboard', 'range_tag' => 'week']) ?>
        <?php echo Html::a('月  ', ['/site/dashboard', 'range_tag' => 'month']) ?>
        </div>
        <?php Pjax::end() ?>

    </div>

    <div class=" hide box-footer">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="description-block border-right">
                    <!--                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>-->
                    <h5 class="description-header">TOTAL:
                        <span class="text-green"><?= Yii::$app->formatter->format(9000, 'decimal'); ?></span>
                    </h5>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                    <h5 class="description-header">
                        <span class="text-red"><?= Yii::$app->formatter->format(1000, 'decimal'); ?></span>
                    </h5>
                    <span class="description-text">TODAY</span>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                    <h5 class="description-header">
                        <span class="text-red"><?= Yii::$app->formatter->format(2000, 'decimal'); ?></span>
                    </h5>
                    <span class="description-text">YESTERDAY</span>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-down"></i> 20%</span>
                    <h5 class="description-header">
                        <span class="text-red"><?= Yii::$app->formatter->format(3000, 'decimal'); ?></span>
                    </h5>
                    <span class="description-text">MONTH</span>
                </div>
            </div>
        </div>
    </div>

    <br/>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::a('待发货订单 ' . \rmrevin\yii\fontawesome\FontAwesome::icon('angle-double-right'), ['/order/wait-submit']) ?></h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="hide fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <ul class="products-list product-list-in-box">

                <?php foreach ($orderDataProvider->models as $model): ?>
                    <li class="item">
                        <div class="product-img hide">
                            <img src="" alt="Product Image"> <!-- dist/img/default-50x50.gif -->
                        </div>
                        <div class="product-info">
                            <a href="<?= Url::to(['/order/view', 'id' => $model->id]) ?>" class="product-title"><?= '待发货订单' ?> --- <?= $model->id . ' ' . $model->nickname; ?>
                                <span class="label label-warning pull-right"><?= '查看' ?></span></a>
                            <span class="product-description hide">
                          todo
                        </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- /.box-body -->
        <div class="box-footer text-center hide">
            <a href="javascript:void(0)" class="uppercase">View All Products</a>
        </div>
        <!-- /.box-footer -->
    </div>

</section>



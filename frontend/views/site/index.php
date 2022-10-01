<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <!--        <h1>Congratulations!</h1>-->

        <p class="lead" style="margin-top: 30px;">网站正在建设中，敬请期待!</p>

        <p><a class="hide btn btn-lg btn-success" href="#">敬请期待</a></p>
    </div>

    <div class="body-content">
<?php /*
        <div class="row">
        <?php
        $items = [];
        foreach ([
                     '01.jpg',
                     '1.jpg',
                     '02.jpg',
                     '2.jpg',
                     '03.jpg',
                     '3.jpg',
                     '04.jpg',
                     '4.jpg',
                     '05.jpg',
                     '5.jpg',
                     '06.jpg',
                     '6.jpg',
                     '07.jpg',
                     '7.jpg',
                     '08.jpg',
                     '8.jpg',
                     '09.jpg',
                     '9.jpg',
                     '10.jpg',
                     '11.jpg',
                     '12.jpg',
                     '13.jpg',
                 ] as $filename) {
            $items[] = [
                'url' => Yii::getAlias("@web/1200x900/$filename"),
                'src' => Yii::getAlias("@web/1200x900/$filename"),
                'options' => array('title' => ''),
                'imageOptions' => ['width' => '100'],
            ];
        } ?>

        <?= dosamigos\gallery\Gallery::widget(['items' => $items]); ?>
        </div>
*/ ?>
        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a>
                </p>
            </div>
        </div>

    </div>
</div>

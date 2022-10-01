<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


backend\assets\AppAsset::register($this);
//dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!--
            <style>
                body,p,div,h1,h2,h3,h4,h5{
                    font-family: "微软雅黑" !important;
                }
            </style>
    -->
    <script type="text/javascript">
        var REST_HOST_INFO = "<?= YII_ENV_DEV ? 'http://127.0.0.1/zantoto/rest/web' : 'http://api.zantoto.com'; // http://47.92.223.175:8082 ?>";
    </script>

</head>
<!--<body class="hold-transition sidebar-mini --><?//= 'skin-yellow'; //\dmstr\helpers\AdminLteHelper::skinClass() ?><!--">-->

<?php $defaultSkin = \dmstr\helpers\AdminLteHelper::skinClass() ?>
<?php echo Html::beginTag('body', [
    'class' => implode(' ', [
        'hold-transition sidebar-mini ',
        ArrayHelper::getValue($this->params, 'body-class'),
        Yii::$app->ks->get('backend.theme-skin', 'skin-blue'),
        Yii::$app->ks->get('backend.layout-fixed') ? 'fixed' : null,
        Yii::$app->ks->get('backend.layout-boxed') ? 'layout-boxed' : null,
        Yii::$app->ks->get('backend.layout-collapsed-sidebar') ? 'sidebar-collapse' : null,
    ])
])?>

<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    )
    ?>

    <?= $this->render(
        'content.php',
        ['content' => $content, 'directoryAsset' => $directoryAsset]
    ) ?>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

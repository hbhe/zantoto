<?php
namespace common\wosotech\base;

use Yii;
use yii\base\InlineAction;
use yii\helpers\Url;

class GridView extends \yii\grid\GridView
{
    //public $layout = "{items}";
    public $emptyText = false;

    public $options = ['class' => 'table-responsive'];
}

<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@storage', dirname(dirname(__DIR__)) . '/storage');
Yii::setAlias('@rest', dirname(dirname(__DIR__)) . '/rest');
Yii::setAlias('@wap', dirname(dirname(__DIR__)) . '/wap');

Yii::setAlias('@frontendUrl', getenv('FRONTEND_URL'));
Yii::setAlias('@backendUrl', getenv('BACKEND_URL'));
Yii::setAlias('@storageUrl', getenv('STORAGE_URL'));
Yii::setAlias('@restUrl', getenv('REST_URL'));

//Yii::$classMap['yii\base\ArrayableTrait'] = '@common/wosotech/base/ArrayableTrait.php';
//Yii::$classMap['yii\helpers\ArrayHelper'] = '@common/wosotech/base/ArrayHelper.php';
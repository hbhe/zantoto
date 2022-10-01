<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

/**
 * This is the model class for table "{{%picture}}".
 *
 * @property integer $id
 * @property string $global_sid
 * @property integer $global_iid
 * @property string $path
 * @property string $base_url
 * @property string $type
 * @property integer $size
 * @property string $name
 * @property integer $order
 * @property string $created_at
 * @property string $updated_at
 */
class ProductDetailPicture extends \common\models\Picture
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_detail_picture}}';
    }
}

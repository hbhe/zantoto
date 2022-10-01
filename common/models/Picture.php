<?php
/**
 * @link http://github.com/zantoto
 * @copyright Copyright (c) 2020 Zantoto
 * @author 57620133@qq.com
 */

namespace common\models;

use common\wosotech\helper\Util;
use Yii;
use yii\behaviors\TimestampBehavior;

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
class Picture extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%picture}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['size', 'order'], 'integer'],
            [['created_at', 'updated_at', 'global_iid'], 'safe'],
            [['global_sid'], 'string', 'max' => 64],
            [['path', 'base_url'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 512],
            [['order', 'global_iid', 'size'], 'default', 'value' => 0],
            [['order'], 'filter', 'filter' => 'intval'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'global_sid' => 'SID',
            'global_iid' => 'ID',
            'path' => '文件名',
            'base_url' => 'Url路径',
            'type' => '类型',
            'size' => '尺寸',
            'name' => '原文件名',
            'order' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function getImageUrl()
    {
        return $this->base_url . '/' . $this->path;
    }

    /**
     * 可以得到图片的任何尺寸的URL, 只要提供长宽参数就当场生成, 默认提供200x200
     */
    public function getThumbImageUrl($width = 200, $height = 200, $mode = "outbound", $quality = null, $fileName = null)
    {
        $path = $this->path;
        $filesystem = Yii::$app->get('fileStorage')->getFilesystem();
        if ($filesystem->has($path) === false) {
            // 再检查一下是否是demo目录下的文件
            $adapter = new \League\Flysystem\Adapter\Local(Yii::getAlias('@backend/web/image-samples/product'));
            $filesystem = new \League\Flysystem\Filesystem($adapter);
            if ($filesystem->has($path) === false) {
                // die('getThumbImageUrl fail, the file does not exists!');
                $adapter = new \League\Flysystem\Adapter\Local(Yii::getAlias('@backend/web/image-samples/cloth'));
                $filesystem = new \League\Flysystem\Filesystem($adapter);
                if ($filesystem->has($path) === false) {
                    // die("getThumbImageUrl fail, the file $path does not exists!");
                    Yii::error([__METHOD__, __LINE__, 'the file does not exists', $path]);
                    return '';
                }
            }
        }
        // $pathPrefix = \Yii::$app->fileStorage->filesystem->getAdapter()->getPathPrefix();
        // $location = $pathPrefix . $this->path;
        $location = $filesystem->getAdapter()->applyPathPrefix($path);
        // 使用imageresize组件生成thumb, 此组件有缓存, 如果文件存在是不会重新生成的
        if ($this->isImage()) {
            return \Yii::$app->imageresize->getUrl($location, $width, $height, $mode, $quality, $fileName);
        }

        if ($this->isVideo()) { // video封面图片
            if (file_exists($location . '.jpg')) {
                return $this->getImageUrl() . '.jpg';
            }
            Util::generateVideoPoster($location);
            return $this->getImageUrl() . '.jpg';
        }

        return '';
    }

    /**
     * 下载URL, 要使用上传时的原文件名
     */
    public function getFileDownUrl()
    {
        return Yii::$app->urlManagerStorage->createAbsoluteUrl(["/picture/download", "id" => $this->id, "path" => $this->path]);
    }

    /**
     * 也是下载URL, 不过使用的不是上传时的文件名
     */
    public function getImageDownUrl()
    {
        // 也是下载URL, 不过使用的不是上传时的文件名
        return Yii::$app->urlManagerStorage->createAbsoluteUrl(["/picture/see", 'path' => $this->path]);
    }

    public function isImage()
    {
        return in_array($this->type, ['image/jpeg', 'image/gif', 'image/bmp', 'image/png']);
    }

    public function isVideo()
    {
        return in_array($this->type, ['video/mp4', 'video/ogg', 'video/webm', 'video/mpeg']);
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['created_at'],
            $fields['updated_at'],
            $fields['path'],
            $fields['base_url']
        );
        $fields[] = 'imageUrl';
        if ($this->isImage() || $this->isVideo()) {
            $fields[] = 'thumbImageUrl';
        }
        // $fields['isImage'] = function ($model) { return $model->isImage() ? 1 : 0; };
        // $fields['isVideo'] = function ($model) { return $model->isVideo() ? 1 : 0; };
        // $fields[] = 'imageDownUrl';
        // $fields[] = 'fileDownUrl';
        return $fields;
    }
}

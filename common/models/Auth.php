<?php
/**
 *  @link http://github.com/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @author 57620133@qq.com
 */

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%auth}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property string $nickname
 * @property string $avatar_url
 * @property string $openid
 */
class Auth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['oauth_client'], 'string', 'max' => 64],
            [['oauth_client_user_id', 'nickname', 'avatar_url', 'openid'], 'safe'],
            [['oauth_client', 'oauth_client_user_id'], 'unique', 'targetAttribute' => ['oauth_client', 'oauth_client_user_id'], 'message' => 'The combination of Oauth Client and Oauth Client User ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'oauth_client' => '渠道',
            'oauth_client_user_id' => '该渠道下的用户ID',
            'nickname' => 'Nick Name',
            'avatar_url' => 'Avatar',
        ];
    }

    public function getMember() {
        return $this->hasOne(Member::className(), ['id' => 'user_id']);
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'member';
        return $fields;
    }
}

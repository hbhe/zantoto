<?php

namespace common\wosotech\helper;

use yii\captcha\CaptchaAction;
use yii\base\Exception;
use Yii;

/*
# yii2-rest-api-captcha
Simple captcha image generator for restful api

## Installation

Recommended installation via [composer](http://getcomposer.org/download/):

```
composer require venomousboy/yii2-rest-api-captcha
```

## Usage

Generate captcha code (image/png;base64):

```php
(new CaptchaHelper())->generateImage();
```

Use in HTML:

```html
<img src="<?= (new CaptchaHelper())->generateImage() ?>" />
```
Verify POST method captcha code:

```php
(new CaptchaHelper())->verify(\Yii::$app->request->post('code'));
```
*/
class CaptchaHelper extends CaptchaAction
{
    private $code;

    /**
     * CaptchaHelper constructor.
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct()
    {
        $this->init();
        $this->minLength = 4;
        $this->maxLength = 4;
        $this->offset = 0;
        if (file_exists(Yii::getAlias('@backend/web/css/msyh.ttf'))) {
            $this->fontFile = Yii::getAlias('@backend/web/css/msyh.ttf');
        }
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function generateImage($embed = true)
    {
        $code = $this->generateCode();
        $imageData = $this->renderImage($code);
        $key = $this->generateSessionKey($code);
        Yii::$app->cache->set($key, 1, YII_ENV_DEV ? 5 * 60 : 15 * 60);
        Yii::info("save image_verify_code $key in cache");
        if (!$embed) {
            return $imageData;
        }
        $base64 = "data:image/png;base64," . base64_encode($imageData);
        return $base64;
    }

    /**
     * @return string
     */
    public function generateCode()
    {
        if ($this->code) {
            return $this->code;
        }

        $this->code = $this->generateVerifyCode();
        return $this->code;
    }

    /**
     * @param string $code
     * @return bool
     * @throws Exception
     */
    public function verify($code)
    {
        $key = $this->generateSessionKey($code);
        if (Yii::$app->cache->get($key) !== false) {
            Yii::$app->cache->delete($key);
            return true;
        }
        Yii::info(['the code not found in cache', $key]);
        return false;
    }

    /**
     * @return string
     */
    private function generateSessionKey($code)
    {
        return $code;
    }
}

/*
 *
    public function generateImage($embed = true)
    {
        $code = $this->generateCode();
        $imageData = $this->renderImage($code);
        $key = $this->generateSessionKey($code);
        Yii::$app->cache->set($key, $code, YII_ENV_DEV ? 5 * 60 : 15 * 60);
        if (!$embed) {
            return $imageData;
        }
        $base64 = "data:image/png;base64," . base64_encode($imageData);
        return $base64;
    }

    public function verify($code)
    {
        $key = $this->generateSessionKey($code);
        Yii::info(['compare image_verify_code', $key, Yii::$app->cache->get($key), $code]);
        if (Yii::$app->cache->get($key) === $code) {
            Yii::$app->cache->delete($key);
            return true;
        }
        return false;
    }

    private function generateSessionKey($code)
    {
        return base64_encode(Yii::$app->request->getUserIP() . $code);
    }

*/

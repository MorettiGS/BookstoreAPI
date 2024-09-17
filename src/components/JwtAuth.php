<?php

namespace app\components;

use Yii;
use yii\web\Application;
use yii\base\Behavior;
use yii\web\UnauthorizedHttpException;
use Firebase\JWT\JWT;

class TokenAuth extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'validateToken',
        ];
    }

    public function validateToken()
    {
        $headers = Yii::$app->request->headers;
        $authHeader = $headers->get('Authorization');

        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            try {
                $decoded = Yii::$app->jwt->decode($token, 'your-secret-key', ['HS256']);
                Yii::$app->user->loginByAccessToken($decoded->login);
            } catch (\Exception $e) {
                throw new UnauthorizedHttpException('Invalid token.');
            }
        } else {
            throw new UnauthorizedHttpException('Authorization header is required.');
        }
    }
}
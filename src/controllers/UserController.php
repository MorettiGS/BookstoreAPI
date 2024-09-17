<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\User;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class UserController extends Controller
{
    public function actionLogin()
    {
        $request = Yii::$app->request->post();
        $login = $request['login'] ?? null;
        $password = $request['password'] ?? null;

        if (!$login || !$password) {
            throw new UnauthorizedHttpException('Login and password are required.');
        }

        $user = User::findOne(['login' => $login]);

        if (!$user || !Yii::$app->security->validatePassword($password, $user->password_hash)) {
            throw new UnauthorizedHttpException('Invalid login or password.');
        }

        $token = Yii::$app->jwt->encode([
            'login' => $user->login,
            'exp' => time() + 3600, // Token expiration time (1 hour)
        ], Yii::$app->params['jwtSecretKey']);

        return ['token' => $token];
    }

    protected function generateJwt($user)
    {
        $key = Yii::$app->params['jwtSecretKey'];

        $payload = [
            'iss' => 'localhost',        // Issuer of the token
            'aud' => 'localhost',        // Audience
            'iat' => time(),             // Issued at: current time
            'nbf' => time(),             // Not before
            'exp' => time() + 3600,      // Expiration time (1 hour)
            'uid' => $user->id,          // Subject (user ID)
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}

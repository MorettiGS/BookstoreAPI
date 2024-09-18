<?php
namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\User;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Ensure the responses are JSON
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    public function actionLogin()
    {
        $request = Yii::$app->request->bodyParams;
        $login = $request['login'] ?? null;
        $password = $request['password'] ?? null;

        if (!$login || !$password) {
            throw new UnauthorizedHttpException('Login e senha são necessários.');
        }

        $user = User::findOne(['login' => $login]);

        if (!$user || !password_verify($password, $user->password_hash)) {
            throw new UnauthorizedHttpException('Login ou senha inválidos.');
        }

        // Generate JWT token
        $token = $this->generateJwt($user);

        // You can store the token in the DB if needed for token revocation

        return ['token' => $token];
    }

    protected function generateJwt($user)
    {
        $key = Yii::$app->params['jwtSecretKey'];

        $payload = [
            'iss' => 'localhost', // Issuer of the token
            'aud' => 'localhost', // Audience
            'iat' => time(),      // Issued at: current time
            'exp' => time() + 3600, // Expiration time (1 hour)
            'uid' => $user->id,   // Subject (user ID)
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}

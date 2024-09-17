<?php

namespace app\commands;

use Yii;
use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class UserController extends Controller
{
    public function actionCreate($login, $password, $name)
    {
        $db = Yii::$app->db;
        
        // Check if the login already exists
        $existingUser = User::findOne(['login' => $login])->exists();

        if ($existingUser) {
            echo "Erro: O login '$login' já existe.\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        $sql = "INSERT INTO `user` (`login`, `password_hash`, `name`, `created_at`, `updated_at`)
            VALUES (:login, :password_hash, :name, NOW(), NOW())";

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $command = $db->createCommand($sql);
        $command->bindValue(':login', $login);
        $command->bindValue(':password_hash', $password_hash);
        $command->bindValue(':name', $name);
        
        try {
            $command->execute();
            echo "Usuário $login criado com sucesso!\n";
            return ExitCode::OK;
        } catch (\Exception $e) {
            echo "Erro ao criar usuário: " . $e->getMessage() . "\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}

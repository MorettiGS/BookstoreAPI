<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\httpclient\Client as HttpClient;

class Client extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%client}}';
    }

    public function rules()
    {
        return [
            [['name', 'cpf', 'cep', 'street', 'number', 'city', 'state', 'gender'], 'required'],
            [['name', 'street', 'city', 'complement'], 'string', 'max' => 255],
            [['cpf'], 'unique'],
            [['cpf'], 'string', 'length' => 14],
            [['cpf'], 'match', 'pattern' => '/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/', 'message' => 'CPF inválido. Utilize o formato XXX.XXX.XXX-XX.'],
            [['cpf'], 'validateCPF'], // Custom CPF validation rule
            [['cep'], 'string', 'length' => 9],
            [['cep'], 'match', 'pattern' => '/^\d{5}\-\d{3}$/', 'message' => 'CEP inválido. Utilize o formato XXXXX-XXX.'],
            [['cep'], 'validateCEP'], // Custom CEP validation rule
            [['number'], 'string', 'max' => 10],
            [['state'], 'string', 'length' => 2],
            [['gender'], 'in', 'range' => ['M', 'F'], 'message' => 'Sexo inválido. Escolha M ou F.'],
        ];
    }

    public function validateCPF($attribute, $params, $validator)
    {
        $cpf = preg_replace('/\D/', '', $this->$attribute); // Remove non-numeric characters from CPF

        // Check if CPF has 11 digits and if all digits are not the same
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            $this->addError($attribute, 'CPF inválido.');
            return;
        }

        // Validate the CPF's verification digits
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $this->addError($attribute, 'CPF inválido.');
                return;
            }
        }
    }

    public function validateCEP($attribute, $params, $validator)
    {
        $cep = preg_replace('/\D/', '', $this->$attribute); // Remove non-numeric characters from CEP

        
        try {
            $httpClient = new HttpClient();
            // Send the request to the BrasilAPI
            $response = $httpClient->createRequest()
                ->setMethod('GET')
                ->setUrl("https://brasilapi.com.br/api/cep/v1/$cep")
                ->send();

            if (!$response->isOk) {
                $this->addError($attribute, 'CEP inválido ou não encontrado.');
            }

            return true;
        } catch (\Exception $e) {
            $this->addError($attribute, 'Erro ao validar o CEP: ' . $e->getMessage());
        }
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

}


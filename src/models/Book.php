<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\httpclient\Client as HttpClient;

class Book extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%book}}';
    }

    public function rules()
    {
        return [
            [['isbn', 'title', 'author', 'price', 'stock'], 'required'],
            [['title', 'author'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 17],
            [['isbn'], 'unique'],
            [['isbn'], 'match', 'pattern' => '/^\d{3}\-\d{1}\-\d{2}\-\d{6}\-\d{1}$/', 'message' => 'ISBN inválido. Utilize o formato XXX-X-XX-XXXXXX-X.'],
            [['isbn'], 'validateISBN'], // Custom CPF validation rule
            [['price'], 'number', 'min' => 0],
            [['stock'], 'integer','min' => 0],
            [['stock'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => 'Stock cannot be less than zero.'],
        ];
    }


    public function validateISBN($attribute, $params, $validator)
    {
        $isbn = preg_replace('/\D/', '', $this->$attribute); // Remove non-numeric characters from CEP

        
        try {
            $httpClient = new HttpClient();
            // Send the request to the BrasilAPI
            $response = $httpClient->createRequest()
                ->setMethod('GET')
                ->setUrl("https://brasilapi.com.br/api/isbn/v1/$isbn")
                ->send();

            if (!$response->isOk) {
                $this->addError($attribute, 'ISBN inválido ou não encontrado.');
            }

            return true;
        } catch (\Exception $e) {
            $this->addError($attribute, 'Erro ao validar o ISBN: ' . $e->getMessage());
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


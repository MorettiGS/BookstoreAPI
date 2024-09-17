<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\Client;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\web\UnauthorizedHttpException;

class ClientController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Ensure the responses are JSON
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    /**
     * Lists all clients.
     * @return \yii\data\ActiveDataProvider
     */
    public function actionList()
    {
        // Use ActiveDataProvider for pagination
        $dataProvider = new ActiveDataProvider([
            'query' => Client::find(), // Fetch all clients
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Creates a new client.
     * @return Client
     */
    public function actionCreate()
    {
        $model = new Client();
        $request = Yii::$app->request->bodyParams;

        if ($model->load($request, '') && $model->validate() && $model->save()) {
            return [
                'status' => 'success',
                'message' => 'Client created successfully',
                'client' => $model,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to create client',
            'errors' => $model->errors,
        ];
    }
}

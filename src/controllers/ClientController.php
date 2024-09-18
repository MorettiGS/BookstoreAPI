<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\Client;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ClientController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Ensure the responses are JSON
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    protected function verifyJwt($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(Yii::$app->params['jwtSecretKey'], 'HS256'));
            return true;
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Invalid or expired token.');
        }
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
            if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
                throw new UnauthorizedHttpException('No token provided.');
            }

            $token = $matches[1];
            $this->verifyJwt($token);

            return true;
        }
        return false;
    }

    /**
     * Lists all clients with pagination, sorting, and filtering.
     * @param int $limit Number of records to return.
     * @param int $offset Number of records to skip.
     * @param string|null $orderBy Field to order by.
     * @param string|null $filter Field to filter by.
     * @param string|null $term Term to search within the filter field.
     * @return \yii\data\ActiveDataProvider
     */
    public function actionList($limit = 10, $offset = 0, $orderBy = null, $filter = null, $term = null)
    {
        // Define a query to fetch clients
        $query = Client::find();
        
        // Handle filtering if $filter and $term are provided
        if ($filter && $term) {
            // Add safety check to ensure valid fields for filtering
            $allowedFields = ['name', 'cpf']; // Lista de campos permitidos
            if (in_array($filter, $allowedFields)) {
                $query->andWhere(['like', $filter, $term]);
            } else {
                throw new BadRequestHttpException('Campo inválido para filtro.');
            }
        }

        // Handle ordering if $orderBy is provided
        if ($orderBy) {
            $allowedFields = ['name', 'cpf', 'city']; // Lista de campos permitidos para ordenação
            if (in_array($orderBy, $allowedFields)) {
                $query->orderBy([$orderBy => SORT_ASC]); // Ordenação crescente por padrão
            } else {
                throw new BadRequestHttpException('Campo inválido para ordenação.');
            }
        }

        // Calculate the total count of records
        $totalCount = $query->count();

        // Apply pagination
        $query->offset($offset)->limit($limit);

        // Fetch the data
        $data = $query->all();

        return [
            'total' => $totalCount,
            'status' => Yii::$app->response->statusCode,
            'data' => $data,
        ];
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
                'status' => Yii::$app->response->statusCode,
                'message' => 'Client created successfully',
                'data' => $model,
            ];
        }

        return [
            'status' => Yii::$app->response->statusCode,
            'message' => 'Failed to create client',
            'errors' => $model->errors,
        ];
    }
}

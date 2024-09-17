<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;

class BookController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Ensure the responses are JSON
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    /**
     * Lists all books with pagination, sorting, and filtering.
     * @param int $limit Number of records to return.
     * @param int $offset Number of records to skip.
     * @param string|null $orderBy Field to order by.
     * @param string|null $filter Field to filter by.
     * @param string|null $term Term to search within the filter field.
     * @return \yii\data\ActiveDataProvider
     */
    public function actionList($limit = 10, $offset = 0, $orderBy = null, $filter = null, $term = null)
    {
        // Define a query to fetch books
        $query = Book::find();
        
        // Handle filtering if $filter and $term are provided
        if ($filter && $term) {
            // Add safety check to ensure valid fields for filtering
            $allowedFields = ['isbn', 'title', 'author']; // Lista de campos permitidos
            if (in_array($filter, $allowedFields)) {
                $query->andWhere(['like', $filter, $term]);
            } else {
                throw new BadRequestHttpException('Campo inválido para filtro.');
            }
        }

        // Handle ordering if $orderBy is provided
        if ($orderBy) {
            $allowedFields = ['title', 'price']; // Lista de campos permitidos para ordenação
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
     * Creates a new book.
     * @return Book
     */
    public function actionCreate()
    {
        $model = new Book();
        $request = Yii::$app->request->bodyParams;

        if ($model->load($request, '') && $model->validate() && $model->save()) {
            return [
                'status' => Yii::$app->response->statusCode,
                'message' => 'Book created successfully',
                'data' => $model,
            ];
        }

        return [
            'status' => Yii::$app->response->statusCode,
            'message' => 'Failed to create book',
            'errors' => $model->errors,
        ];
    }
}

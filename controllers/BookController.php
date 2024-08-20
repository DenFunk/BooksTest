<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use app\models\BookSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use yii\helpers\ArrayHelper;
use app\models\Author;
use app\models\BookAuthor;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['update','create','delete'],
                    'rules' => [
                        [
                            'actions' => ['update','create','delete'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Book();
        
        $authors = $this->getAuthorList();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                
                $image = UploadedFile::getInstance($model, 'image');
                 if (!is_null($image)) {
                     $model->preview = $image->name;
                     Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/uploads/';
                     $path = Yii::$app->params['uploadPath'] . $model->preview;
                     $image->saveAs($path);
                 }
                
                if ($model->save()) {             
                    return $this->redirect(['view', 'id' => $model->id]);   
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $authors = $this->getAuthorList();

        if ($this->request->isPost && $model->load($this->request->post()) ) {
            
            $image = UploadedFile::getInstance($model, 'image');
                 if (!is_null($image)) {
                     $model->preview = $image->name;
                     Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/uploads/';
                     $path = Yii::$app->params['uploadPath'] . $model->preview;
                     $image->saveAs($path);
                 }
                
                if ($model->save()) {             
                    return $this->redirect(['view', 'id' => $model->id]);  
                }
            
            
            
            
            
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    
    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionTop($year = null)
    {
        
        /* Можно вынести отдельно в модель, где валидацию проходить и т.п */
        if(empty($year)){
            $year = date('Y');
        }
        
        $authors = Author::find()
            ->select(['author.*', 'COUNT(book_author.book_id) AS countbooks'])
            ->join('LEFT JOIN', BookAuthor::tableName(), 'author.id=book_author.author_id')
            ->join('LEFT JOIN', Book::tableName(), 'book_author.book_id=book.id')  
            ->where(['book.year'=>$year])
            ->groupBy('author.id')
            ->orderBy(['countbooks' => SORT_DESC])
            ->limit(10);
        $data = $authors->asArray()->all();        

        return $this->render('top', [
            'data' => $data
        ]);
    }
    

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    protected function getAuthorList() {
        
        if (($authors = Author::find()->asArray()->all()) !== null) {
            return ArrayHelper::map($authors,'id','fullname');
        }

        throw new NotFoundHttpException('The requested page does not exist.');

    }
}

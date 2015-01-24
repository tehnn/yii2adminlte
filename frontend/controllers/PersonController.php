<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Person;
use frontend\models\PersonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Goodby\CSV\Import\Standard\LexerConfig;

/**
 * PersonController implements the CRUD actions for Person model.
 */
class PersonController extends Controller {

    public function behaviors() {
        //$role = @\Yii::$app->user->identity->role;
        //echo \Yii::$app->user->identity->username;
        $arr = ['index', 'view'];
        /*
        if ($role == 1) {
            //$arr = ['index', 'view', 'create', 'update', 'delete',];
            array_push($arr, 'create');
            array_push($arr, 'update');
            array_push($arr, 'delete');
        }
        if ($role == 10) {
            //$arr = ['index', 'view'];
            array_push($arr, 'update');
        }*/
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    throw new \yii\web\ForbiddenHttpException("ไม่อนุญาติ");
                },
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => $arr,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => $arr,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
    public function actionTest() {



        $config = new LexerConfig();
        $config
                ->setDelimiter("\t") // Customize delimiter. Default value is comma(,)
                ->setEnclosure("'")  // Customize enclosure. Default value is double quotation(")
                ->setEscape("\\")    // Customize escape character. Default value is backslash(\)
                ->setToCharset('UTF-8') // Customize target encoding. Default value is null, no converting.
                ->setFromCharset('SJIS-win') // Customize CSV file encoding. Default value is null.
        ;
    }

    public function actionIndex() {
        $searchModel = new PersonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex3() {

        $query = new \yii\db\Query();
        $rawdata = $query->select('*')->from('person')->where(['id' => [1, 2, 3]])
                        ->createCommand()->queryAll();


        $provider = new \yii\data\ArrayDataProvider([
            //'key'=>'id',
            'allModels' => $rawdata,
            'sort' => [
                'attributes' => array_keys($rawdata[0])
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);


        return $this->render('index3', [
                    'dataProvider' => $provider,
        ]);
    }

    // ที่ controller
    public function actionIndex2() {

        $a = 'ก';

        $sql = "SELECT * FROM person where id>20 and fname like '$a%'";

        $rawdata = \Yii::$app->db->createCommand($sql)->queryAll();


        $provider = new \yii\data\ArrayDataProvider([
            //'key'=>'id',
            'allModels' => $rawdata,
            'sort' => [
                'attributes' => array_keys($rawdata[0])
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index2', [
                    'dataProvider' => $provider,
                    'sql' => $sql
        ]);
    }

    /**
     * Displays a single Person model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Person model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Person();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Person model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Person model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Person::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}

<?php

namespace wdmg\robots\controllers;

use Yii;
use wdmg\robots\models\Rules;
use wdmg\robots\models\RulesSearch;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ListController implements the CRUD actions for Rules model.
 */
class ListController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'delete' => ['post'],
                    'view' => ['get'],
                    'generate' => ['get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ]
        ];

        // If auth manager not configured use default access control
        if (!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    public function beforeAction($action)
    {
        if ($action->id !== 'generate') {
            $path = Yii::getAlias($this->module->robotsWebRoot);
            if (!file_exists($path)) {
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/robots',
                        'Robots.txt by path `{path}` is not exists.',
                        [
                            'path' => $path
                        ]
                    )
                );
            }

            if (!is_writable($path)) {
                Yii::$app->getSession()->setFlash(
                    'warning',
                    Yii::t(
                        'app/modules/robots',
                        'Robots.txt by path `{path}` is not writable.',
                        [
                            'path' => $path
                        ]
                    )
                );
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->get('change') == "status") {
                if (Yii::$app->request->post('id', null)) {
                    $id = Yii::$app->request->post('id');
                    $status = Yii::$app->request->post('value', 0);
                    $model = $this->findModel(intval($id));
                    if ($model) {
                        $model->status = $status;
                        if ($model->update())
                            return true;
                        else
                            return false;
                    }
                }
            }
        }

        $searchModel = new RulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {

        $model = new Rules();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                Yii::$app->response->format =  Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                return true;
            }
        }

        if (!Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {

                // Log activity
                $this->module->logActivity(
                    'Robots.txt rule with ID `' . $model->id . '` has been successfully added.',
                    $this->uniqueId . ":" . $this->action->id,
                    'success',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/robots', 'Robots.txt rule has been successfully added!')
                );
            } else {

                // Log activity
                $this->module->logActivity(
                    'An error occurred while add the new Robots.txt rule.',
                    $this->uniqueId . ":" . $this->action->id,
                    'danger',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/robots', 'An error occurred while add the rule.')
                );
            }
        }

        if (Yii::$app->request->isAjax)
            return $this->renderAjax('_form', [
                'model' => $model,
                'module' => $this->module
            ]);
        else
            return $this->redirect(['index']);

    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                Yii::$app->response->format =  Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                return true;
            }
        }

        if (!Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {

                // Log activity
                $this->module->logActivity(
                    'Robots.txt rule with ID `' . $model->id . '` has been successfully updated.',
                    $this->uniqueId . ":" . $this->action->id,
                    'success',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/robots', 'Robots.txt rule has been successfully updated!')
                );
            } else {

                // Log activity
                $this->module->logActivity(
                    'An error occurred while update the Robots.txt rule with ID `' . $model->id . '`.',
                    $this->uniqueId . ":" . $this->action->id,
                    'danger',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/robots', 'An error occurred while updating the rule.')
                );
            }
        }

        if (Yii::$app->request->isAjax)
            return $this->renderAjax('_form', [
                'model' => $model,
                'module' => $this->module
            ]);
        else
            return $this->redirect(['index']);

    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {

            // Log activity
            $this->module->logActivity(
                'Robots.txt rule with ID `' . $model->id . '` has been successfully deleted.',
                $this->uniqueId . ":" . $this->action->id,
                'success',
                1
            );

            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(
                    'app/modules/robots',
                    'OK! Rule successfully deleted.'
                )
            );
        } else {

            // Log activity
            $this->module->logActivity(
                'An error occurred while deleting the Robots.txt rule with ID `' . $model->id . '`.',
                $this->uniqueId . ":" . $this->action->id,
                'danger',
                1
            );

            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/robots',
                    'An error occurred while deleting the rule.'
                )
            );
        }

        return $this->redirect(['index']);
    }

    public function actionView() {
        if (Yii::$app->request->isAjax) {
            $source = $this->module->getRobotsTxt();
            return $this->renderAjax('_view', [
                'source' => $source,
                'path' => Yii::getAlias('@web/robots.txt')
            ]);
        }
        $this->redirect(['index']);
    }

    public function actionGenerate() {
        if ($this->module->genRobotsTxt()) {
            // Log activity
            $this->module->logActivity(
                'Robots.txt rules has been successfully regenerated.',
                $this->uniqueId . ":" . $this->action->id,
                'success',
                1
            );
        } else {
            // Log activity
            $this->module->logActivity(
                'An error occurred while regenerate the Robots.txt rules.',
                $this->uniqueId . ":" . $this->action->id,
                'danger',
                1
            );
        }
        $this->redirect(['index']);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rules::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/robots', 'The requested rule does not exist.'));
    }
}

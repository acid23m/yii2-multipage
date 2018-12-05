<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 12:50
 */

namespace multipage\controllers;

use multipage\models\Marker;
use multipage\models\Parameter;
use multipage\models\ParameterSearch;
use yii\base\InvalidArgumentException;
use yii\db\IntegrityException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\View;

/**
 * Class ParameterController.
 *
 * @package multipage\controllers
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class ParameterController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs']['actions']['input-type'] = ['post'];

        return $behaviors;
    }

    /**
     * Lists all Parameter models.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function actionIndex()
    {
        $searchModel = new ParameterSearch;
        $dataProvider = $searchModel->search(\Yii::$app->getRequest()->getQueryParams());

        $marks = Marker::allList();

        return $this->render('index', \compact('searchModel', 'dataProvider', 'marks'));
    }

    /**
     * Displays a single Parameter model.
     * @param integer $id
     * @return string|View
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Parameter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|View|Response
     * @throws InvalidArgumentException
     */
    public function actionCreate()
    {
        $model = new Parameter;

        $marks = Marker::allList(false);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('multipage', 'zapis dobavlena'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', \compact('model', 'marks'));
    }

    /**
     * Updates an existing Parameter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|View|Response
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $marks = Marker::allList(false);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('multipage', 'zapis obnovlena'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', \compact('model', 'marks'));
    }

    /**
     * Deletes an existing Parameter model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id): Response
    {
        $model = $this->findModel($id);

        try {
            $model->delete();
        } catch (IntegrityException $e) {
            \Yii::$app->getSession()->setFlash('warning', \Yii::t('multipage', 'ne udaleno iz za svasannih dannih'));

            return $this->redirect(['view', 'id' => $model->id]);
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage());
            \Yii::$app->getSession()->setFlash('error', 'Error.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        \Yii::$app->getSession()->setFlash('success', \Yii::t('multipage', 'zapis udalena'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the Parameter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Parameter The loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Parameter
    {
        if (($model = Parameter::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('multipage', 'zapis ne sushestvuet'));
    }

    /**
     * Form inputs.
     * @return string
     */
    public function actionTypeInput(): string
    {
        $request = \Yii::$app->getRequest();
        if (!$request->getIsAjax()) {
            return '';
        }

        /** @var int $type */
        $type = (int) $request->post('type');
        $form = \unserialize(
            \base64_decode($request->post('form'))
        );
        /** @var Parameter $model */
        $model = \unserialize(
            \base64_decode($request->post('model'))
        );

        switch ($type) {
            case $model::TYPE_URL_QUERY:
                return $this->renderAjax('_form_url', \compact('form', 'model'));
            case $model::TYPE_GEO_COUNTRY:
                return $this->renderAjax('_form_country', \compact('form', 'model'));
            case $model::TYPE_GEO_REGION:
                return $this->renderAjax('_form_region', \compact('form', 'model'));
            case $model::TYPE_GEO_CITY:
                return $this->renderAjax('_form_city', \compact('form', 'model'));
            default:
                return '';
        }
    }

}

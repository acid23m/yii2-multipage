<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 2:28
 */

namespace multipage\controllers;

use multipage\models\Marker;
use multipage\models\MarkerSearch;
use yii\base\InvalidArgumentException;
use yii\db\IntegrityException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\View;

/**
 * Class MarkerController.
 *
 * @package multipage\controllers
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class MarkerController extends BaseController
{
    /**
     * Lists all Marker models.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function actionIndex()
    {
        $searchModel = new MarkerSearch;
        $dataProvider = $searchModel->search(\Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * Displays a single Marker model.
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
     * Creates a new Marker model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|View|Response
     * @throws InvalidArgumentException
     */
    public function actionCreate()
    {
        $model = new Marker();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('multipage', 'zapis dobavlena'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', compact('model'));
    }

    /**
     * Updates an existing Marker model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|View|Response
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('multipage', 'zapis obnovlena'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', compact('model'));
    }

    /**
     * Deletes an existing Marker model.
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
     * Finds the Marker model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Marker The loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Marker
    {
        if (($model = Marker::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('multipage', 'zapis ne sushestvuet'));
    }

}

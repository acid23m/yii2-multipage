<?php

namespace multipage\controllers;

use multipage\actions\UpdateInfoAction;
use multipage\models\Region;
use multipage\models\RegionSearch;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * RegionController implements the CRUD actions for Region model.
 *
 * @package multipage\controllers
 */
final class RegionController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        $actions = [
            'update-info' => UpdateInfoAction::class
        ];

        return ArrayHelper::merge(parent::actions(), $actions);
    }

    /**
     * Lists all Region models.
     * @return string
     * @throws InvalidArgumentException
     */
    public function actionIndex(): string
    {
        $searchModel = new RegionSearch;
        $dataProvider = $searchModel->search(\Yii::$app->getRequest()->getQueryParams());


        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * Displays a single Region model.
     * @param integer $id
     * @return string
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Finds the Region model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Region The loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Region
    {
        if (($model = Region::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('multipage', 'zapis ne sushestvuet'));
    }

}

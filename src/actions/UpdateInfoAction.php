<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.09.18
 * Time: 18:05
 */

namespace multipage\actions;

use multipage\models\GeoUpdater;
use yii\base\Action;
use yii\web\Response;

/**
 * Class UpdateInfoAction.
 *
 * @package multipage\actions
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class UpdateInfoAction extends Action
{
    /**
     * Update information about countries, regions and cities.
     * @return Response
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\db\Exception
     */
    public function run(): Response
    {
        if (GeoUpdater::getInfo()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('multipage', 'gotovo'));
        } else {
            \Yii::$app->getSession()->setFlash('error', \Yii::t('yii', 'Error'));
        }

        return $this->controller->redirect(['index']);
    }

}

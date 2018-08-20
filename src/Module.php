<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.08.18
 * Time: 2:11
 */

namespace multipage;

use yii\i18n\I18N;
use yii\i18n\PhpMessageSource;

/**
 * Class Module.
 *
 * @package multipage
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Module extends \yii\base\Module
{
    public const DB_NAME = 'dbMultiPage';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->defaultRoute = 'marker/index';

        // common configuration
        \Yii::configure(\Yii::$app, [
            'components' => [
                'i18n' => [
                    'class' => I18N::class,
                    'translations' => [
                        'multipage' => [
                            'class' => PhpMessageSource::class,
                            'basePath' => '@vendor/acid23m/yii2-multipage/src/messages'
                        ]
                    ]
                ]
            ]
        ]);
    }

}

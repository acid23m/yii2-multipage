<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 2:23
 */

namespace multipage\controllers;

use yii\filters\AccessControl;
use yii\filters\HttpCache;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Class BaseController.
 *
 * @package multipage\controllers
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class BaseController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'httpCache' => [
                'class' => HttpCache::class,
                'sessionCacheLimiter' => 'private_no_expire'
            ],

            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?']
                    ]
                ]
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post']
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        $actions = [
            'error' => ErrorAction::class
        ];

        return ArrayHelper::merge(parent::actions(), $actions);
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->layout = \multipage\Module::getInstance()->layout;
    }

}

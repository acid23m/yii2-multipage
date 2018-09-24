<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.08.18
 * Time: 2:11
 */

namespace multipage;

/**
 * Class Module.
 *
 * @package multipage
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Module extends \yii\base\Module
{
    public const DEFAULT_ID = 'multipage';
    public const DB_NAME = 'dbMultiPage';
    public const GEOIP_COMPONENT_ID = 'geoip';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->defaultRoute = 'marker/index';
    }

}

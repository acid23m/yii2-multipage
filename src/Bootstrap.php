<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.08.18
 * Time: 22:57
 */

namespace multipage;

use multipage\components\GeoIp;
use multipage\models\GeoUpdater;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\Connection;
use yii\helpers\FileHelper;
use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap.
 *
 * @package multipage
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     * @param \yii\web\Application|\yii\console\Application $app
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\base\InvalidParamException
     */
    public function bootstrap($app): void
    {
        \Yii::configure($app, [
            'components' => [
                // configure db
                \multipage\Module::DB_NAME => [
                    'class' => Connection::class,
                    'dsn' => 'sqlite:@common/data/multipagedata.db',
                    'schemaCacheDuration' => 3600,
                    'on afterOpen' => function (Event $event) {
                        /** @var Connection $connection */
                        $connection = $event->sender;
                        $connection->createCommand('PRAGMA foreign_keys = ON;')->execute();
                        $connection->createCommand('PRAGMA case_sensitive_like = false;')->execute();
                        $connection->createCommand('PRAGMA count_changes = false;')->execute();
                        $connection->createCommand('PRAGMA journal_mode = OFF;')->execute();
                        $connection->createCommand('PRAGMA synchronous = NORMAL;')->execute();
                    }
                ]
            ]
        ]);

        if ($app instanceof \yii\web\Application) {
            \Yii::configure($app, [
                'components' => [
                    // define country, region and city by ip
                    'geoip' => [
                        'class' => GeoIp::class
                    ]
                ]
            ]);

            $app->getI18n()->translations['multipage'] = [
                'class' => PhpMessageSource::class,
                'basePath' => '@vendor/acid23m/yii2-multipage/src/messages'
            ];
        }

        // check multipage data
        $multipage_db_file = \Yii::getAlias('@common/data/multipagedata.db');

        if (!file_exists($multipage_db_file)) {
            FileHelper::createDirectory(\Yii::getAlias('@common/data'));
            $multipage_db = new \SQLite3($multipage_db_file);

            // geo data
            $multipage_db->exec(<<<'SQL'
CREATE TABLE "country" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "iso" VARCHAR(2) NOT NULL,
    "continent" VARCHAR(2) NOT NULL,
    "name_ru" VARCHAR(150) NOT NULL,
    "name_en" VARCHAR(150) NOT NULL,
    "latitude" DECIMAL(10,5) NOT NULL,
    "longitude" DECIMAL(10,5) NOT NULL,
    "timezone" VARCHAR(30) NOT NULL
);
SQL
            );
            $multipage_db->exec(<<<'SQL'
CREATE TABLE "region" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "iso" VARCHAR(7) NOT NULL,
    "country_id" INTEGER NOT NULL,
    "name_ru" VARCHAR(150) NOT NULL,
    "name_en" VARCHAR(150) NOT NULL,
    "timezone" VARCHAR(30) NOT NULL,
    FOREIGN KEY ("country_id") REFERENCES "country" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);
SQL
            );
            $multipage_db->exec('CREATE INDEX "idx_region_country_id" ON "region" ("country_id");');
            $multipage_db->exec(<<<'SQL'
CREATE TABLE "city" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "region_id" INTEGER NOT NULL,
    "country_id" INTEGER NOT NULL,
    "name_ru" VARCHAR(150) NOT NULL,
    "name_en" VARCHAR(150) NOT NULL,
    "latitude" DECIMAL(10,5) NOT NULL,
    "longitude" DECIMAL(10,5) NOT NULL,
    FOREIGN KEY ("region_id") REFERENCES "region" ("id") ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY ("country_id") REFERENCES "country" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);
SQL
            );
            $multipage_db->exec('CREATE INDEX "idx_city_region_id" ON "city" ("region_id");');
            $multipage_db->exec('CREATE INDEX "idx_city_country_id" ON "city" ("country_id");');

            // markers for replacement
            $multipage_db->exec(<<<'SQL'
CREATE TABLE "marker" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "name" VARCHAR(50) NOT NULL,
    "text" TEXT,
    "status" INTEGER NOT NULL DEFAULT (1),
    "created_at" DATETIME NOT NULL,
    "updated_at" DATETIME NOT NULL
);
SQL
            );
            $multipage_db->exec('CREATE INDEX "idx_marker_status" ON "marker" ("status");');
            // replacement rules
            $multipage_db->exec(<<<'SQL'
CREATE TABLE "parameter" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "marker_id" INTEGER NOT NULL,
    "language" VARCHAR(7),
    "type" INTEGER NOT NULL,
    "query_name" VARCHAR(50),
    "query_value" TEXT,
    "country" VARCHAR(150),
    "region" VARCHAR(150),
    "city" VARCHAR(150),
    "replacement" TEXT,
    "operator" INTEGER NOT NULL,
    "status" INTEGER NOT NULL DEFAULT (1),
    "created_at" DATETIME NOT NULL,
    "updated_at" DATETIME NOT NULL,
    FOREIGN KEY ("marker_id") REFERENCES "marker" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);
SQL
            );
            $multipage_db->exec('CREATE INDEX "idx_parameter_marker_id" ON "parameter" ("marker_id");');
            $multipage_db->exec('CREATE INDEX "idx_parameter_status" ON "parameter" ("status");');

            chmod($multipage_db_file, 0664);

            // update geo data
            GeoUpdater::getInfo();
        }

        // check sxgeo data
        $sxgeo_data_file = \Yii::getAlias(GeoUpdater::DATA_DIR . '/' . GeoIp::DB_FILE);
        if (!file_exists($sxgeo_data_file)) {
            GeoUpdater::getData();
        }
    }

}

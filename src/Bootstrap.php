<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.08.18
 * Time: 22:57
 */

namespace multipage;

use yii\base\BootstrapInterface;
use yii\db\Connection;
use yii\helpers\FileHelper;

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
        // check multipage data
        $multipage_db_file = \Yii::getAlias('@common/data/multipagedata.db');

        if (!file_exists($multipage_db_file)) {
            FileHelper::createDirectory(\Yii::getAlias('@common/data'));
            $multipage_db = new \SQLite3($multipage_db_file);

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
            $multipage_db->exec(<<<'SQL'
CREATE TABLE "parameter" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "marker_id" INTEGER NOT NULL,
    "name" VARCHAR(50) NOT NULL,
    "operator" INTEGER NOT NULL,
    "text" TEXT NOT NULL,
    "replacement" TEXT,
    "status" INTEGER NOT NULL DEFAULT (1),
    "created_at" DATETIME NOT NULL,
    "updated_at" DATETIME NOT NULL,
    FOREIGN KEY ("marker_id") REFERENCES "marker" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);
SQL
            );
            $multipage_db->exec('CREATE INDEX "idx_parameter_status" ON "parameter" ("status");');

            chmod($multipage_db_file, 0664);
        }

        // configure db
        if ($app instanceof \yii\web\Application) {
            \Yii::configure($app, [
                'components' => [
                    \multipage\Module::DB_NAME => [
                        'class' => Connection::class,
                        'dsn' => 'sqlite:@common/data/multipagedata.db',
                        'schemaCacheDuration' => 3600
                    ]
                ]
            ]);
        }
    }

}

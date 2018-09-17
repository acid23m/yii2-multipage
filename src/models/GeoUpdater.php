<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 13.09.18
 * Time: 12:29
 */

namespace multipage\models;

use yii\helpers\FileHelper;

/**
 * Class GeoUpdater.
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class GeoUpdater
{
    public const DATA_DIR = '@common/data/sxgeo';
    protected const BUFFER_SIZE = 500;

    /**
     * Iterate through csv|tsv file.
     * @param resource $file File handler
     * @param string $csv_delimiter CSV delimiter
     * @return \Generator
     */
    protected static function csvIterator($file, $csv_delimiter = ','): \Generator
    {
        while (($data = fgetcsv($file, 0, $csv_delimiter)) !== false) {
            if ($data === null || $data[0] === null) {
                continue;
            }
            yield $data;
        }
    }

    /**
     * Update information about countries, regions and cities.
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\db\Exception
     */
    public static function getInfo(): bool
    {
        set_time_limit(0);

        $data_dir = \Yii::getAlias(self::DATA_DIR);
        $temp_dir = sys_get_temp_dir() . '/sxgeo';
        $last_upd_file = $data_dir . '/sxgeoinfo.upd';
        $download_file = $temp_dir . '/sxgeoinfo.zip';


        // check data directory
        if (!FileHelper::createDirectory($data_dir)) {
            \Yii::warning("Directory $data_dir for geo data has not been created.");

            return false;
        }
        // check temporary directory
        if (!FileHelper::createDirectory($temp_dir)) {
            \Yii::warning("Temporary directory $temp_dir for geo data has not been created.");

            return false;
        }
        // check file with update time
        if (!file_exists($last_upd_file)) {
            $f = fopen($last_upd_file, 'cb');
            fwrite($f, gmdate('D, d M Y H:i:s', 100) . ' GMT');
            fclose($f);
            unset($f);
        }


        // download archive file
        $f = fopen($download_file, 'wb');
        if ($f === false) {
            \Yii::warning("Can\'t create temporary file $download_file");

            return false;
        }

        $ch = curl_init('https://sypexgeo.net/files/SxGeo_Info.zip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FILE, $f);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['If-Modified-Since: ' . file_get_contents($last_upd_file)]);

        if (curl_exec($ch) === false) {
            \Yii::warning('SxGeo info file has not been downloaded: ' . curl_error($ch));

            return false;
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($f);
        unset($f, $ch);

        // do nothing if file was not modified
        if ($code === 304) {
            try {
                unlink($download_file);
            } catch (\Throwable $e) {
            }

            return true;
        }

        // change update time
        $f = fopen($last_upd_file, 'wb');
        fwrite($f, gmdate('D, d M Y H:i:s') . ' GMT');
        fclose($f);
        unset($f, $code);


        // unpack downloaded file
        $zip = new \ZipArchive;
        $extract = function (string $filename) use (&$zip, &$data_dir): void {
            $f = fopen($data_dir . '/' . $filename, 'wb');
            fwrite($f, $zip->getFromName($filename));
            fclose($f);
        };
        if (($zip_status = $zip->open($download_file)) !== true) {
            \Yii::warning("SxGeo info file extraction error: $zip_status");

            return false;
        }
        $extract('country.tsv');
        $extract('region.tsv');
        $extract('city.tsv');
        $zip->close();
        unset($zip, $extract);

        try {
            unlink($download_file);
        } catch (\Throwable $e) {
        }


        // drop indexes
        Region::getDb()->createCommand('DROP INDEX "idx_region_country_id";');
        City::getDb()->createCommand('DROP INDEX "idx_city_region_id";');
        City::getDb()->createCommand('DROP INDEX "idx_city_country_id";');


        // save countries to db
        Country::deleteAll();
        Country::getDb()->createCommand('VACUUM')->execute();

        $filename = $data_dir . '/country.tsv';
        $f = fopen($filename, 'rb');
        if ($f === false) {
            return false;
        }

        $table_name = Country::tableName();
        $country_id_query_c = Country::getDb()->createCommand("SELECT id FROM $table_name WHERE iso=:iso;");
        $insert = function (array $data) use (&$table_name): void {
            if (!empty($data)) {
                try {
                    Country::getDb()
                        ->createCommand()
                        ->batchInsert(
                            $table_name,
                            ['id', 'iso', 'continent', 'name_ru', 'name_en', 'latitude', 'longitude', 'timezone'],
                            $data
                        )
                        ->execute();
                } catch (\Throwable $e) {
                    \Yii::warning('Country has not been saved: ' . $e->getMessage());
                }
            }
        };

        $i = 0;
        $buffer = [];
        foreach (self::csvIterator($f, "\t") as [$id, $iso, $cont, $name_ru, $name_en, $lat, $lon, $tz]) {
            $buffer[] = [(int) $id, $iso, $cont, $name_ru, $name_en, $lat, $lon, $tz];
            $i ++;
            if ($i === self::BUFFER_SIZE) {
                $insert($buffer);
                $i = 0;
                $buffer = [];
            }
        }
        $insert($buffer);
        fclose($f);
        unset($filename, $f, $table_name, $insert, $i, $buffer, $id, $iso, $cont, $name_ru, $name_en, $lat, $lon, $tz, $r);


        // save regions to db
        Region::deleteAll();
        Region::getDb()->createCommand('VACUUM')->execute();

        $filename = $data_dir . '/region.tsv';
        $f = fopen($filename, 'rb');
        if ($f === false) {
            return false;
        }

        $table_name = Region::tableName();
        $country_id_query_r = Region::getDb()->createCommand("SELECT country_id FROM $table_name WHERE id=:id;");
        $insert = function (array $data) use (&$table_name): void {
            if (!empty($data)) {
                try {
                    Region::getDb()
                        ->createCommand()
                        ->batchInsert($table_name, ['id', 'iso', 'country_id', 'name_ru', 'name_en', 'timezone'], $data)
                        ->execute();
                } catch (\Throwable $e) {
                    \Yii::warning('Region has not been saved: ' . $e->getMessage());
                }
            }
        };

        $i = 0;
        $buffer = [];
        foreach (self::csvIterator($f, "\t") as [$id, $iso, $country_iso, $name_ru, $name_en, $tz, $okato]) {
            $country_id = $country_id_query_c->bindValues([':iso' => $country_iso])->queryScalar();
            if ($country_id !== null && $country_id !== false) {
                $buffer[] = [(int) $id, $iso, (int) $country_id, $name_ru, $name_en, $tz];
                $i ++;
            }
            if ($i === self::BUFFER_SIZE) {
                $insert($buffer);
                $i = 0;
                $buffer = [];
            }
        }
        $insert($buffer);
        fclose($f);
        Region::getDb()->createCommand('CREATE INDEX "idx_region_country_id" ON "' . $table_name . '" ("country_id");');
        unset($filename, $f, $table_name, $insert, $i, $buffer, $id, $iso, $country_iso, $name_ru, $name_en, $tz, $okato, $country_id, $r, $country_id_query_c);


        // save cities to db
        City::deleteAll();
        City::getDb()->createCommand('VACUUM')->execute();

        $filename = $data_dir . '/city.tsv';
        $f = fopen($filename, 'rb');
        if ($f === false) {
            return false;
        }

        $table_name = City::tableName();
        $insert = function (array $data) use (&$table_name): void {
            if (!empty($data)) {
                try {
                    City::getDb()
                        ->createCommand()
                        ->batchInsert(
                            $table_name,
                            ['id', 'region_id', 'country_id', 'name_ru', 'name_en', 'latitude', 'longitude'],
                            $data
                        )
                        ->execute();
                } catch (\Throwable $e) {
                    \Yii::warning('City has not been saved: ' . $e->getMessage());
                }
            }
        };

        $i = 0;
        $buffer = [];
        foreach (self::csvIterator($f, "\t") as [$id, $region_id, $name_ru, $name_en, $lat, $lon, $okato]) {
            $country_id = $country_id_query_r->bindValues([':id' => $region_id])->queryScalar();
            if ($country_id !== null && $country_id !== false) {
                $buffer[] = [(int) $id, (int) $region_id, (int) $country_id, $name_ru, $name_en, $lat, $lon];
                $i ++;
            }
            if ($i === self::BUFFER_SIZE) {
                $insert($buffer);
                $i = 0;
                $buffer = [];
            }
        }
        $insert($buffer);
        fclose($f);
        City::getDb()->createCommand('CREATE INDEX "idx_city_region_id" ON "' . $table_name . '" ("region_id");');
        City::getDb()->createCommand('CREATE INDEX "idx_city_country_id" ON "' . $table_name . '" ("country_id");');
        City::getDb()->createCommand('VACUUM')->execute();

        return true;
    }

    /**
     * Update IP data.
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidArgumentException
     */
    public static function getData(): bool
    {
        set_time_limit(0);

        $data_dir = \Yii::getAlias(self::DATA_DIR);
        $temp_dir = sys_get_temp_dir() . '/sxgeo';
        $last_upd_file = $data_dir . '/sxgeodata.upd';
        $download_file = $temp_dir . '/sxgeodata.zip';

        // check data directory
        if (!FileHelper::createDirectory($data_dir)) {
            \Yii::warning("Directory $data_dir for geo data has not been created.");

            return false;
        }
        // check temporary directory
        if (!FileHelper::createDirectory($temp_dir)) {
            \Yii::warning("Temporary directory $temp_dir for geo data has not been created.");

            return false;
        }
        // check file with update time
        if (!file_exists($last_upd_file)) {
            $f = fopen($last_upd_file, 'cb');
            fwrite($f, gmdate('D, d M Y H:i:s', 100) . ' GMT');
            fclose($f);
            unset($f);
        }


        // download archive file
        $f = fopen($download_file, 'wb');
        if ($f === false) {
            \Yii::warning("Can\'t create temporary file $download_file");

            return false;
        }

        $ch = curl_init('https://sypexgeo.net/files/SxGeoCity_utf8.zip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FILE, $f);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['If-Modified-Since: ' . file_get_contents($last_upd_file)]);

        if (curl_exec($ch) === false) {
            \Yii::warning('SxGeo data file has not been downloaded: ' . curl_error($ch));

            return false;
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($f);
        unset($f, $ch);

        // do nothing if file was not modified
        if ($code === 304) {
            try {
                unlink($download_file);
            } catch (\Throwable $e) {
            }

            return true;
        }

        // change update time
        $f = fopen($last_upd_file, 'wb');
        fwrite($f, gmdate('D, d M Y H:i:s') . ' GMT');
        fclose($f);
        unset($f, $code);


        // unpack downloaded file
        $zip = new \ZipArchive;
        if (($zip_status = $zip->open($download_file)) !== true) {
            \Yii::warning("SxGeo data file extraction error: $zip_status");

            return false;
        }
        $zip->extractTo($data_dir);
        $zip->close();
        unset($zip, $extract);

        try {
            unlink($download_file);
        } catch (\Throwable $e) {
        }

        return true;
    }

    /**
     * Define language postfix for names which depends on current locale.
     * @return string
     */
    public static function getGeoInfoLanguage(): string
    {
        switch (\Yii::$app->language) {
            case 'ru':
                return 'ru';
            case 'en':
                return 'en';
                break;
            default:
                return 'en';
        }
    }

}

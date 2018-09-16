<?php

namespace multipage\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $iso
 * @property int $country_id
 * @property string $name_ru
 * @property string $name_en
 * @property string $timezone
 *
 * @property City[] $cities
 * @property Country $country
 *
 * @package multipage\models
 */
class Region extends ActiveRecord
{
    /**
     * {@inheritdoc}
     * @return Connection
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return \Yii::$app->get(\multipage\Module::DB_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['iso', 'name_ru', 'name_en', 'timezone'], 'trim'],
            [['iso', 'country_id', 'name_ru', 'name_en', 'timezone'], 'required'],
            [['country_id'], 'integer'],
            [
                ['country_id'],
                'exist',
                'skipOnError' => true,
                'targetRelation' => 'country'
            ],
            [['iso'], 'string', 'max' => 7],
            [['name_ru', 'name_en'], 'string', 'max' => 150],
            [['timezone'], 'string', 'max' => 30]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'iso' => 'ISO',
            'country_id' => \Yii::t('multipage', 'strana'),
            'name_ru' => \Yii::t('multipage', 'nazvanie') . ' Ru',
            'name_en' => \Yii::t('multipage', 'nazvanie') . ' En',
            'timezone' => \Yii::t('multipage', 'vrem zona')
        ];
    }

    /**
     * @return ActiveQuery|CityQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::class, ['region_id' => 'id'])->inverseOf('region');
    }

    /**
     * @return ActiveQuery|CountryQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country_id'])->inverseOf('regions');
    }

    /**
     * {@inheritdoc}
     * @return RegionQuery the active query used by this AR class.
     */
    public static function find(): RegionQuery
    {
        return new RegionQuery(static::class);
    }

    /**
     * @return array Regions list
     */
    public static function getDropdownList(): array
    {
        switch (\Yii::$app->language) {
            case 'ru':
                $lang = 'ru';
                break;
            case 'en':
                $lang = 'en';
                break;
            default:
                $lang = 'en';
        }

        return ArrayHelper::map(static::find()->with('country')->all(), 'iso', "name_$lang", "country.name_$lang");
    }

}

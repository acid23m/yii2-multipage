<?php

namespace multipage\models;

use yii\base\InvalidConfigException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $iso
 * @property string $continent
 * @property string $name_ru
 * @property string $name_en
 * @property string $latitude
 * @property string $longitude
 * @property string $timezone
 *
 * @property Region[] $regions
 * @property City[] $cities
 * @property Parameter[] $parameters
 *
 * @package multipage\models
 */
class Country extends ActiveRecord
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
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'latitude' => AttributeTypecastBehavior::TYPE_FLOAT,
                    'longitude' => AttributeTypecastBehavior::TYPE_FLOAT
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => false,
                'typecastAfterFind' => true
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['iso', 'continent', 'name_ru', 'name_en', 'latitude', 'longitude', 'timezone'], 'trim'],
            [['iso', 'continent', 'name_ru', 'name_en', 'latitude', 'longitude', 'timezone'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['iso', 'continent'], 'string', 'max' => 2],
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
            'continent' => \Yii::t('multipage', 'continent'),
            'name_ru' => \Yii::t('multipage', 'nazvanie') . ' Ru',
            'name_en' => \Yii::t('multipage', 'nazvanie') . ' En',
            'latitude' => \Yii::t('multipage', 'shirota'),
            'longitude' => \Yii::t('multipage', 'dolgota'),
            'timezone' => \Yii::t('multipage', 'vrem zona')
        ];
    }

    /**
     * @return ActiveQuery|RegionQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::class, ['country_id' => 'id'])->inverseOf('country');
    }

    /**
     * @return ActiveQuery|CityQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::class, ['country_id' => 'id'])->inverseOf('country');
    }

    /**
     * @return ActiveQuery|CountryQuery
     */
    public function getParameters()
    {
        return $this->hasMany(Parameter::class, ['country_id' => 'id'])->inverseOf('country');
    }

    /**
     * {@inheritdoc}
     * @return CountryQuery the active query used by this AR class.
     */
    public static function find(): CountryQuery
    {
        return new CountryQuery(static::class);
    }

    /**
     * @return array Country list
     */
    public static function getDropdownList(): array
    {
        $lang = GeoUpdater::getGeoInfoLanguage();

        return ArrayHelper::map(static::find()->all(), 'id', "name_$lang");
    }

}

<?php

namespace multipage\models;

use yii\base\InvalidConfigException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property int $region_id
 * @property int $country_id
 * @property string $name_ru
 * @property string $name_en
 * @property string $latitude
 * @property string $longitude
 *
 * @property Region $region
 * @property Country $country
 *
 * @package multipage\models
 */
class City extends ActiveRecord
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
        return 'city';
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
            [['region_id', 'country_id', 'name_ru', 'name_en', 'latitude', 'longitude'], 'required'],
            [['region_id', 'country_id'], 'integer'],
            [
                ['region_id'],
                'exist',
                'skipOnError' => true,
                'targetRelation' => 'region'
            ],
            [
                ['country_id'],
                'exist',
                'skipOnError' => true,
                'targetRelation' => 'country'
            ],
            [['name_ru', 'name_en'], 'string', 'max' => 150],
            [['latitude', 'longitude'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'region_id' => \Yii::t('multipage', 'region'),
            'country_id' => \Yii::t('multipage', 'strana'),
            'name_ru' => \Yii::t('multipage', 'nazvanie') . ' Ru',
            'name_en' => \Yii::t('multipage', 'nazvanie') . ' En',
            'latitude' => \Yii::t('multipage', 'shirota'),
            'longitude' => \Yii::t('multipage', 'dolgota')
        ];
    }

    /**
     * @return ActiveQuery|RegionQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'region_id'])->inverseOf('cities');
    }

    /**
     * @return ActiveQuery|CountryQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country_id'])->inverseOf('cities');
    }

    /**
     * {@inheritdoc}
     * @return CityQuery the active query used by this AR class.
     */
    public static function find(): CityQuery
    {
        return new CityQuery(static::class);
    }

    /**
     * @return array Cities list
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

        return ArrayHelper::map(static::find()->with('country')->all(), 'name_en', "name_$lang", "country.name_$lang");
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 1:56
 */

namespace multipage\models;

use multipage\traits\DateTime;
use multipage\traits\Model;
use yii\base\InvalidConfigException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "parameter".
 *
 * @property int $id
 * @property int $marker_id
 * @property string $language
 * @property int $type
 * @property string $query_name
 * @property string $query_value
 * @property string $country
 * @property string $region
 * @property string $city
 * @property string $replacement
 * @property int $operator
 * @property bool $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Marker $marker
 *
 * @method void touch(string $attribute) Updates a timestamp attribute to the current timestamp
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Parameter extends ActiveRecord
{
    use DateTime;
    use Model;

    public const STATUS_ACTIVE = 1;
    public const STATUS_NOT_ACTIVE = 0;

    public const OPERATOR_EQUALLY = 10;
    public const OPERATOR_CONTAINS = 20;

    public const TYPE_URL_QUERY = 10;
    public const TYPE_GEO_COUNTRY = 20;
    public const TYPE_GEO_REGION = 21;
    public const TYPE_GEO_CITY = 22;

    /**
     * @var array
     */
    protected $statuses;
    /**
     * @var array
     */
    protected $operators;
    /**
     * @var array
     */
    protected $types;

    /**
     * {@inheritdoc}
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
        return 'parameter';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => \call_user_func([$this, 'getNow'])
            ],
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'type' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'operator' => AttributeTypecastBehavior::TYPE_INTEGER
                ],
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
            [['language', 'query_name', 'query_value', 'country', 'region', 'city', 'replacement'], 'trim'],
            [['marker_id', 'type', 'status'], 'required'],
            [['operator'], 'default', 'value' => self::OPERATOR_EQUALLY],
            [['marker_id', 'type', 'operator'], 'integer'],
            [
                ['marker_id'],
                'exist',
                'skipOnError' => true,
                'targetRelation' => 'marker'
            ],
            [['language', 'query_name', 'query_value', 'country', 'region', 'city'], 'default', 'value' => ''],
            [['language'], 'string', 'max' => 7],
            [['query_name'], 'string', 'max' => 50],
            [['query_value', 'replacement'], 'string'],
            [['country', 'region', 'city'], 'string', 'max' => 150],
            [['status'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'marker_id' => \Yii::t('multipage', 'marker'),
            'language' => \Yii::t('multipage', 'yazyk'),
            'type' => \Yii::t('multipage', 'istochnik'),
            'query_name' => \Yii::t('multipage', 'parameter'),
            'query_value' => \Yii::t('multipage', 'znachenie parametra'),
            'country' => \Yii::t('multipage', 'strana'),
            'region' => \Yii::t('multipage', 'region'),
            'city' => \Yii::t('multipage', 'gorod'),
            'replacement' => \Yii::t('multipage', 'zamena markera'),
            'operator' => \Yii::t('multipage', 'operator'),
            'status' => \Yii::t('multipage', 'status'),
            'created_at' => \Yii::t('multipage', 'data sozdania'),
            'updated_at' => \Yii::t('multipage', 'data obnovleniya')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->statuses = [
            self::STATUS_ACTIVE => \Yii::t('multipage', 'aktiven'),
            self::STATUS_NOT_ACTIVE => \Yii::t('multipage', 'ne aktiven')
        ];

        $this->operators = [
            self::OPERATOR_EQUALLY => \Yii::t('multipage', 'ravno'),
            self::OPERATOR_CONTAINS => \Yii::t('multipage', 'soderzhit')
        ];

        $this->types = [
            self::TYPE_URL_QUERY => \Yii::t('multipage', 'parameter'),
            self::TYPE_GEO_COUNTRY => \Yii::t('multipage', 'strana'),
            self::TYPE_GEO_REGION => \Yii::t('multipage', 'region'),
            self::TYPE_GEO_CITY => \Yii::t('multipage', 'gorod')
        ];
    }

    /**
     * @return ActiveQuery|MarkerQuery
     */
    public function getMarker()
    {
        return $this->hasOne(Marker::class, ['id' => 'marker_id']);
    }

    /**
     * {@inheritdoc}
     * @return ParameterQuery the active query used by this AR class.
     */
    public static function find(): ParameterQuery
    {
        return new ParameterQuery(static::class);
    }

}

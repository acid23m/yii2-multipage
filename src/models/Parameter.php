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
 * @property string $name
 * @property int $operator
 * @property string $text
 * @property string $replacement
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

    /**
     * @var array
     */
    protected $statuses;
    /**
     * @var array
     */
    protected $operators;

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
            [['name', 'text', 'replacement'], 'trim'],
            [['marker_id', 'name', 'operator', 'text', 'status'], 'required'],
            [['marker_id', 'operator'], 'integer'],
            [
                ['marker_id'],
                'exist',
                'skipOnError' => true,
                'targetRelation' => 'marker'
            ],
            [['name'], 'string', 'max' => 50],
            [['text', 'replacement'], 'string'],
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
            'name' => \Yii::t('multipage', 'parameter'),
            'operator' => \Yii::t('multipage', 'operator'),
            'text' => \Yii::t('multipage', 'znachenie parametra'),
            'replacement' => \Yii::t('multipage', 'zamena markera'),
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

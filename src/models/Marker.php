<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 21.08.18
 * Time: 1:29
 */

namespace multipage\models;

use multipage\traits\DateTime;
use multipage\traits\Model;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "marker".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property bool $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Parameter[] $parameters
 *
 * @method void touch(string $attribute) Updates a timestamp attribute to the current timestamp
 *
 * @package multipage\models
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Marker extends ActiveRecord
{
    use DateTime;
    use Model;

    public const STATUS_ACTIVE = 1;
    public const STATUS_NOT_ACTIVE = 0;

    /**
     * @var array
     */
    protected $statuses;

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
        return '{{marker}}';
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
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'text'], 'trim'],
            [['name', 'status'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'match', 'pattern' => '|^[\{]{2}[a-z0-9_\-]+[\}]{2}$|i'],
            [['text'], 'string'],
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
            'name' => \Yii::t('multipage', 'marker'),
            'text' => \Yii::t('multipage', 'po umolchaniu'),
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
    }

    /**
     * @return ActiveQuery|ParameterQuery
     */
    public function getParameters()
    {
        return $this->hasMany(Parameter::class, ['marker_id' => 'id'])->inverseOf('marker');
    }

    /**
     * {@inheritdoc}
     * @return MarkerQuery the active query used by this AR class.
     */
    public static function find(): MarkerQuery
    {
        return new MarkerQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes): void
    {
        // deactivate rules if marker was deactivated
        if (isset($changedAttributes['status']) && $this->status == self::STATUS_NOT_ACTIVE) {
            $rules = $this->parameters;
            foreach ($rules as &$rule) {
                $rule->status = $rule::STATUS_NOT_ACTIVE;
                $rule->save();
            }
            unset($rule);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Get list of all markers.
     * @param bool $with_drafts Show all or just active markers
     * @return array
     */
    public static function allList(bool $with_drafts = true): array
    {
        $query = static::find();

        $data = $with_drafts ? $query->all() : $query->published()->all();

        return ArrayHelper::map($data, 'id', 'name');
    }

}

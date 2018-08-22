<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.08.18
 * Time: 16:33
 */

namespace multipage\behaviors;

use multipage\models\Process;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Replace markers on 'after find' event.
 *
 * @package multipage\behaviors
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class ReplaceMarkersBehavior extends Behavior
{
    /**
     * @var string|array Attribute name (or list of names) that stores the content with markers to replace
     */
    public $attribute = 'text';

    /**
     * {@inheritdoc}
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'process'
        ];
    }

    /**
     * Replace markers.
     * @param Event $event
     * @throws InvalidConfigException
     */
    public function process(Event $event): void
    {
        /** @var ActiveRecord $model */
        $model = $event->sender;
        $attribute = $this->attribute;

        if (\is_string($attribute)) {
            try {
                $content = Process::replaceMarkers($model->$attribute);
                $model->$attribute = $content;
            } catch (\Throwable $e) {
            }
        } elseif (\is_array($attribute)) {
            foreach ($attribute as $attr) {
                try {
                    $content = Process::replaceMarkers($model->$attr);
                    $model->$attr = $content;
                } catch (\Throwable $e) {
                }
            }
        } else {
            throw new InvalidConfigException('Attribute must be type of string or array.');
        }
    }

}

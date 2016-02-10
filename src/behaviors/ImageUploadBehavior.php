<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\modelimage\behaviors;

use yii\base\Behavior,
    yii\base\InvalidConfigException;
use jlorente\modelimage\traits\ImageUploadTrait;
use yii\db\ActiveRecord;
use yii\validators\Validator;
use Yii;
use yii\log\Logger;

/**
 * Class that attaches the functionallity of uploading the image by using a behavior.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class ImageUploadBehavior extends Behavior {

    use ImageUploadTrait;

    /**
     *
     * @var string
     */
    public $storeAttribute = 'img_path';

    /**
     *
     * @var int
     */
    public $width;

    /**
     *
     * @var int
     */
    public $heigh;

    /**
     *
     * @var string
     */
    public $uploadsPrefix;

    /**
     * @var \yii\validators\Validator[]
     */
    protected $validators = [];

    /**
     * @inheritdoc
     */
    public function rules() {
        return $this->imageRules();
    }

    /**
     * @inheritdoc
     */
    public function attach($owner) {
        parent::attach($owner);
        $validators = $owner->validators;
        foreach ($this->imageRules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
                $this->validators[] = $rule;
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) {
                $validator = Validator::createValidator($rule[1], $owner, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
                $this->validators[] = $validator;
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function detach() {
        $ownerValidators = $this->owner->validators;
        $cleanValidators = [];
        foreach ($ownerValidators as $validator) {
            if (!in_array($validator, $this->validators)) {
                $cleanValidators[] = $validator;
            }
        }
        $ownerValidators->exchangeArray($cleanValidators);
    }

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_SAVE => 'onBeforeSave'
            , ActiveRecord::EVENT_BEFORE_DELETE => 'deleteImage'
        ];
    }

    /**
     * Saves the image.
     */
    public function onBeforeSave() {
        try {
            if ($this->image !== null) {
                $this->resizeAndUpload();
            } else if ($this->image_deleted) {
                $this->deleteImage();
            }
            return true;
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR, 'file-upload');
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function owner() {
        return $this->owner;
    }

    /**
     * @inheritdoc
     */
    public function width() {
        return $this->width;
    }

    /**
     * @inheritdoc
     */
    public function height() {
        return $this->height;
    }

    /**
     * @inheritdoc
     */
    public function uploadsPrefix() {
        return $this->uploadsPrefix;
    }

    /**
     * @inheritdoc
     */
    public function imagePathAttribute() {
        return $this->storeAttribute;
    }

}

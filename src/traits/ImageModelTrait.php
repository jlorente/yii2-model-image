<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\modelimage\traits;

use yii\web\UploadedFile;
use custom\db\exceptions\SaveException;
use Exception;
use Yii;
use yii\log\Logger;

/**
 * Trait that attaches the functionallity of uploading the image by using a trait.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
trait ImageModelTrait {

    use ImageUploadTrait;
    
    /**
     * @inheritdoc
     */
    public function load($data, $formName = null) {
        if (parent::load($data, $formName)) {
            $this->image = UploadedFile::getInstance($this, 'image');
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null) {
        $trans = $this->getDb()->beginTransaction();
        try {
            if (parent::save($runValidation, $attributeNames) === false) {
                throw new SaveException($this);
            }
            if ($this->image !== null) {
                $this->resizeAndUpload();
            } else if ($this->image_deleted) {
                $this->deleteImage();
            }

            $trans->commit();
            return true;
        } catch (Exception $ex) {
            $trans->rollback();
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR, 'file-upload');
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete() {
        $this->deleteImage();
        return parent::delete();
    }
}

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
use common\exceptions\FileException;
use Yii;
use Imagick;

/**
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
trait ImageUploadTrait {

    /**
     *
     * @var UploadedFile 
     */
    public $image;

    /**
     *
     * @var int 
     */
    public $image_deleted;

    /**
     * @inheritdoc
     */
    public function imageRules() {
        return [
            ['image', 'image', 'extensions' => 'png, jpg, gif', 'maxSize' => 4 * 1024 * 1024],
            ['image_deleted', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function imageAttributeLabels() {
        return [
            'image' => Yii::t('item', 'Imagen')
        ];
    }

    /**
     * Resizes the image and uploads the image.
     * 
     * @throws FileException
     */
    protected function resizeAndUpload() {
        $this->deleteImage();
        $imgAttribute = $this->imagePathAttribute();
        $image = new Imagick($this->image->tempName);
        $image->resizeImage($this->width(), $this->height(), Imagick::FILTER_LANCZOS, 1, !$this->width() || !$this->height() ? false : true);
        $dir = Yii::getAlias(Yii::$app->params['uploadsPath'] . $this->uploadsPrefix());
        $name = $this->uploadsPrefix() . DIRECTORY_SEPARATOR . $this->getImageBaseName() . '_' . date('YmdHis') . '.' . $this->image->extension;
        if (file_exists($dir) === false && @mkdir($dir, 0777, true) === false) {
            $message = Yii::t('jlorente/modelimage', 'The directory to store the image can not be created [{dir}]. Please check the path and the write permissions of this directory.', [
                        'dir' => $dir
            ]);
            $this->addError('image', $message);
            throw new FileException($message);
        }
        if ($image->writeImage(Yii::getAlias(Yii::$app->params['uploadsPath'] . $name)) === false) {
            $message = Yii::t('jlorente/modelimage', 'An error ocurred when writting the image to path [{path}]. Please check the write permission of the directory.', [
                        'path' => Yii::$app->params['uploadsPath'] . $name
            ]);
            $this->addError('image', $message);
            throw new FileException($message);
        }
        $image->destroy();
        $this->$imgAttribute = $name;
        if ($this->update([$imgAttribute]) === false) {
            throw new SaveException($this);
        }
    }

    /**
     * Gets the complete image url.
     * 
     * @return string
     */
    public function getImageUrl() {
        $imgAttribute = $this->imagePathAttribute();
        return empty($this->$imgAttribute) ? null : (Yii::$app->params['uploadsUrl'] . $this->$imgAttribute);
    }

    /**
     * Deletes the stored image.
     * 
     * @throws FileException
     */
    public function deleteImage() {
        $imgAttribute = $this->imagePathAttribute();
        if ($this->$imgAttribute !== null) {
            $path = Yii::getAlias(Yii::$app->params['uploadsPath'] . $this->$imgAttribute);
            if (@unlink($path) === false) {
                $this->addError('image', Yii::t('item', 'No se ha podido eliminar la imagen anterior.'));
                throw new FileException('An error has ocurred when deleting the last uploaded image.');
            }
            $this->$imgAttribute = null;
            if ($this->update([$imgAttribute]) === false) {
                throw new SaveException($this);
            }
        }
    }

    /**
     * Returns the attribute name where the image path will be stored.
     * 
     * @return string
     */
    abstract public function imagePathAttribute();

    /**
     * Returns the prefix where to upload the images in the uploads path.
     * 
     * @return string
     */
    abstract public function uploadsPrefix();

    /**
     * Returns the width of the image.
     * 
     * @return integer
     */
    abstract public function width();

    /**
     * Returns the height of the image.
     * 
     * @return integer
     */
    abstract public function height();
}

<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\modelimage\exceptions;

use yii\base\Exception as BaseException;

/**
 * Exception thrown on failed image upload operations.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class ImageUploadException extends BaseException {

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'FileException';
    }

}

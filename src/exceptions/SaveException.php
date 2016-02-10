<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\modelimage\exceptions;

use yii\base\Model;
use yii\helpers\Json;

/**
 * Exception thrown on failed saving operations.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class SaveException extends \yii\base\Exception {

    /**
     * Constructor of the exception.
     * 
     * @param Model $model
     * @param \Exception $previous
     */
    public function __construct(Model $model, \Exception $previous = null) {
        $message = 'Unable to save ' . get_class($model) . '. Errors: [' . Json::encode($model->getErrors()) . ']';
        parent::__construct($message, 1100, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'SaveException';
    }

}

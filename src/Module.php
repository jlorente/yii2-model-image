<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\modelimage;

use yii\base\Module as BaseModule;
use Yii;

/**
 * Module class for the model image module.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class Module extends BaseModule {

    /**
     *
     * @var string 
     */
    public $messageConfig = [];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'jlorente\modelimage\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->setAliases([
            '@jlorenteModelImage' => '@vendor/jlorente/yii2-model-image/src',
        ]);
        Yii::$app->i18n->translations['jlorente/modelimage'] = $this->getMessageConfig();
    }

    /**
     * 
     * @return array
     */
    protected function getMessageConfig() {
        return array_merge([
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@jlorenteModelImage/messages',
            'forceTranslation' => true
                ], $this->messageConfig);
    }

}

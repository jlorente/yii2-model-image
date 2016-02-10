<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\modelimage\widgets;

use yii\base\Widget,
    yii\base\InvalidConfigException;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use kartik\file\FileInput;

/**
 * Widget to create an image preview and an upload interface to use in the form 
 * where the image is going to be uploaded.
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class ImageUploadWidget extends Widget {

    /**
     *
     * @var ActiveRecord
     */
    public $model;

    /**
     *
     * @var ActiveForm
     */
    public $form;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if ($this->model === null) {
            throw new InvalidConfigException('Model property must be provided on initialization.');
        }
    }

    /**
     * @inheritdoc
     */
    public function run() {
        $id = Html::getInputId($this->model, 'image_deleted');
        $options = [
            'options' => [
                'accept' => 'image/*'
            ],
            'pluginOptions' => [
                'showUpload' => false
            ],
            'pluginEvents' => [
                'fileclear' => "function() { $('#{$id}').val(1);}"
            ]
        ];
        if ($this->model->imageUrl !== null) {
            $options['pluginOptions']['initialPreview'] = [
                Html::img($this->model->imageUrl, [
                    'class' => 'file-preview-image'
                ])
            ];
        }
        echo $this->form->field($this->model, 'image_deleted', [
            'template' => '{input}'
        ])->hiddenInput();
        echo $this->form->field($this->model, 'image')->widget(FileInput::classname(), $options);
    }

    /**
     * Sets the ActiveRecord model.
     * 
     * @param ActiveRecord $m
     */
    public function setModel(ActiveRecord $m) {
        $this->model = $m;
    }

    /**
     * Gets the ActiveRecord model.
     * 
     * @return ActiveRecord
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Sets the ActiveForm.
     * 
     * @param ActiveForm $f
     */
    public function setForm(ActiveForm $f) {
        $this->form = $f;
    }

    /**
     * Gets the ActiveForm.
     * 
     * @return ActiveForm
     */
    public function getForm() {
        return $this->form;
    }

}

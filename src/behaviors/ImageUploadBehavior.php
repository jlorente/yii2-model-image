<?php

/**
 * @author      José Lorente <jose.lorente.martin@gmail.com>
 * @license     The MIT License (MIT)
 * @copyright   José Lorente
 * @version     1.0
 */

namespace jlorente\modelimage\behaviors;

use yii\base\Behavior;
use jlorente\modelimage\traits\ImageUploadTrait;
use yii\db\ActiveRecord;

/**
 * 
 * @author José Lorente <jose.lorente.martin@gmail.com>
 */
class ImageUploadBehavior extends Behavior {

    use ImageUploadTrait;
    
    protected $attribute = 'img_path';
    protected $width;
    protected $heigh;
    protected $uploadsPrefix;

    public function attach() {
        
    }
    
    public function rules() {
        
    }
    
    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_SAVE => 'uploadImage',
        ];
    }

}

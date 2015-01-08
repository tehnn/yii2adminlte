<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "person".
 *
 * @property integer $id
 * @property string $prename
 * @property string $fname
 * @property string $lname
 * @property string $mtype
 */
class Person extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prename', 'fname', 'lname', 'mtype'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prename' => 'คำนำหน้า',
            'fname' => 'ชื่อ',
            'lname' => 'สกุล',
            'mtype' => 'ใบประกอบ',
        ];
    }
}

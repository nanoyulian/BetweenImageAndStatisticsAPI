<?php

/**
 * This is the model class for table "newtemplate.wilayah".
 *
 * The followings are the available columns in table 'newtemplate.wilayah':
 * @property string $id
 * @property integer $id_vertical
 * @property string $nama
 * @property string $level
 * @property string $parent
 *
 * The followings are the available model relations:
 * @property Mfd $id
 * @property ItemVerticalVariabel $idVertical
 */
class Wilayah extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'newtemplate.wilayah';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, id_vertical', 'required'),
			array('id_vertical', 'numerical', 'integerOnly'=>true),
			array('id, parent', 'length', 'max'=>10),
			array('nama', 'length', 'max'=>1000),
			array('level', 'length', 'max'=>1),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_vertical, nama, level, parent', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'id' => array(self::BELONGS_TO, 'Mfd', 'id'),
			'idVertical' => array(self::BELONGS_TO, 'ItemVerticalVariabel', 'id_vertical'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_vertical' => 'Id Vertical',
			'nama' => 'Nama',
			'level' => 'Level',
			'parent' => 'Parent',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('id_vertical',$this->id_vertical);
		$criteria->compare('nama',$this->nama,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('parent',$this->parent,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Wilayah the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

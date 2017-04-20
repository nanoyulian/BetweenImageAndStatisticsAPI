<?php

/**
 * This is the model class for table "newtemplate._satuan".
 *
 * The followings are the available columns in table 'newtemplate._satuan':
 * @property integer $id_satuan
 * @property string $nama_satuan
 * @property string $nama_satuan_eng
 *
 * The followings are the available model relations:
 * @property Variabel[] $variabels
 */
class Satuan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'newtemplate._satuan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_satuan', 'required'),
			array('id_satuan', 'numerical', 'integerOnly'=>true),
			array('nama_satuan, nama_satuan_eng', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_satuan, nama_satuan, nama_satuan_eng', 'safe', 'on'=>'search'),
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
			'variabels' => array(self::HAS_MANY, 'Variabel', 'id_satuan'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_satuan' => 'Id Satuan',
			'nama_satuan' => 'Nama Satuan',
			'nama_satuan_eng' => 'Nama Satuan Eng',
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

		$criteria->compare('id_satuan',$this->id_satuan);
		$criteria->compare('nama_satuan',$this->nama_satuan,true);
		$criteria->compare('nama_satuan_eng',$this->nama_satuan_eng,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Satuan the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

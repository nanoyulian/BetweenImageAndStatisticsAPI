<?php

/**
 * This is the model class for table "newtemplate._data".
 *
 * The followings are the available columns in table 'newtemplate._data':
 * @property integer $id_variabel
 * @property integer $id_variabel_turunan
 * @property integer $id_wilayah
 * @property integer $id_tahun
 * @property integer $id_turunan_tahun
 * @property double $data_content
 * @property integer $id_data
 *
 * The followings are the available model relations:
 * @property Tahun $idTahun
 * @property TurunanTahun $idTurunanTahun
 * @property TurunanVariabel $idVariabelTurunan
 * @property Variabel $idVariabel
 * @property ItemVerticalVariabel $idWilayah
 */
class Data extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'newtemplate._data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_variabel, id_variabel_turunan, id_wilayah, id_tahun, id_turunan_tahun', 'required'),
			array('id_variabel, id_variabel_turunan, id_wilayah, id_tahun, id_turunan_tahun', 'numerical', 'integerOnly'=>true),
			array('data_content', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_variabel, id_variabel_turunan, id_wilayah, id_tahun, id_turunan_tahun, data_content, id_data', 'safe', 'on'=>'search'),
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
			'idTahun' => array(self::BELONGS_TO, 'Tahun', 'id_tahun'),
			'idTurunanTahun' => array(self::BELONGS_TO, 'TurunanTahun', 'id_turunan_tahun'),
			'idVariabelTurunan' => array(self::BELONGS_TO, 'TurunanVariabel', 'id_variabel_turunan'),
			'idVariabel' => array(self::BELONGS_TO, 'Variabel', 'id_variabel'),
			'idWilayah' => array(self::BELONGS_TO, 'ItemVerticalVariabel', 'id_wilayah'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_variabel' => 'Id Variabel',
			'id_variabel_turunan' => 'Id Variabel Turunan',
			'id_wilayah' => 'Id Wilayah',
			'id_tahun' => 'Id Tahun',
			'id_turunan_tahun' => 'Id Turunan Tahun',
			'data_content' => 'Data Content',
			'id_data' => 'Id Data',
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

		$criteria->compare('id_variabel',$this->id_variabel);
		$criteria->compare('id_variabel_turunan',$this->id_variabel_turunan);
		$criteria->compare('id_wilayah',$this->id_wilayah);
		$criteria->compare('id_tahun',$this->id_tahun);
		$criteria->compare('id_turunan_tahun',$this->id_turunan_tahun);
		$criteria->compare('data_content',$this->data_content);
		$criteria->compare('id_data',$this->id_data);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Data the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

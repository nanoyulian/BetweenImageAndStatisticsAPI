<?php

/**
 * This is the model class for table "newtemplate._variabel".
 *
 * The followings are the available columns in table 'newtemplate._variabel':
 * @property integer $id_variabel
 * @property string $nama_variabel
 * @property integer $id_subject
 * @property string $definisi_variabel
 * @property integer $id_kelompok_vertical_variabel
 * @property integer $id_kelompok_turunan_variabel
 * @property integer $id_kelompok_turunan_tahun
 * @property string $keterangan_variabel
 * @property integer $id_satuan
 * @property integer $id_grafik
 * @property string $nama_variabel_eng
 * @property string $definisi_variabel_eng
 * @property string $keterangan_variabel_eng
 *
 * The followings are the available model relations:
 * @property Data[] $datas
 * @property TV[] $tVs
 * @property TransaksiInputData[] $transaksiInputDatas
 * @property Grafik $idGrafik
 * @property KelompokTurunanTahun $idKelompokTurunanTahun
 * @property KelompokTurunanVariabel $idKelompokTurunanVariabel
 * @property KelompokVerticalVariabel $idKelompokVerticalVariabel
 * @property Satuan $idSatuan
 * @property Subjek $idSubject
 * @property QuickIndicator[] $quickIndicators
 * @property QuickMap[] $quickMaps
 */
class Variabel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'newtemplate._variabel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_subject, id_kelompok_vertical_variabel, id_kelompok_turunan_variabel, id_kelompok_turunan_tahun, id_satuan, id_grafik', 'numerical', 'integerOnly'=>true),
			array('nama_variabel', 'length', 'max'=>100),
			array('nama_variabel_eng', 'length', 'max'=>200),
			array('definisi_variabel, keterangan_variabel, definisi_variabel_eng, keterangan_variabel_eng', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_variabel, nama_variabel, id_subject, definisi_variabel, id_kelompok_vertical_variabel, id_kelompok_turunan_variabel, id_kelompok_turunan_tahun, keterangan_variabel, id_satuan, id_grafik, nama_variabel_eng, definisi_variabel_eng, keterangan_variabel_eng', 'safe', 'on'=>'search'),
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
			'datas' => array(self::HAS_MANY, 'Data', 'id_variabel'),
			'tVs' => array(self::HAS_MANY, 'TV', 'id_variabel'),
			'transaksiInputDatas' => array(self::HAS_MANY, 'TransaksiInputData', 'id_variabel'),
			'idGrafik' => array(self::BELONGS_TO, 'Grafik', 'id_grafik'),
			'idKelompokTurunanTahun' => array(self::BELONGS_TO, 'KelompokTurunanTahun', 'id_kelompok_turunan_tahun'),
			'idKelompokTurunanVariabel' => array(self::BELONGS_TO, 'KelompokTurunanVariabel', 'id_kelompok_turunan_variabel'),
			'idKelompokVerticalVariabel' => array(self::BELONGS_TO, 'KelompokVerticalVariabel', 'id_kelompok_vertical_variabel'),
			'idSatuan' => array(self::BELONGS_TO, 'Satuan', 'id_satuan'),
			'idSubject' => array(self::BELONGS_TO, 'Subjek', 'id_subject'),
			'quickIndicators' => array(self::HAS_MANY, 'QuickIndicator', 'variabel_id'),
			'quickMaps' => array(self::HAS_MANY, 'QuickMap', 'variabel_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_variabel' => 'Id Variabel',
			'nama_variabel' => 'Nama Variabel',
			'id_subject' => 'Id Subject',
			'definisi_variabel' => 'Definisi Variabel',
			'id_kelompok_vertical_variabel' => 'Id Kelompok Vertical Variabel',
			'id_kelompok_turunan_variabel' => 'Id Kelompok Turunan Variabel',
			'id_kelompok_turunan_tahun' => 'Id Kelompok Turunan Tahun',
			'keterangan_variabel' => 'Keterangan Variabel',
			'id_satuan' => 'Id Satuan',
			'id_grafik' => 'Id Grafik',
			'nama_variabel_eng' => 'Nama Variabel Eng',
			'definisi_variabel_eng' => 'Definisi Variabel Eng',
			'keterangan_variabel_eng' => 'Keterangan Variabel Eng',
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
		$criteria->compare('nama_variabel',$this->nama_variabel,true);
		$criteria->compare('id_subject',$this->id_subject);
		$criteria->compare('definisi_variabel',$this->definisi_variabel,true);
		$criteria->compare('id_kelompok_vertical_variabel',$this->id_kelompok_vertical_variabel);
		$criteria->compare('id_kelompok_turunan_variabel',$this->id_kelompok_turunan_variabel);
		$criteria->compare('id_kelompok_turunan_tahun',$this->id_kelompok_turunan_tahun);
		$criteria->compare('keterangan_variabel',$this->keterangan_variabel,true);
		$criteria->compare('id_satuan',$this->id_satuan);
		$criteria->compare('id_grafik',$this->id_grafik);
		$criteria->compare('nama_variabel_eng',$this->nama_variabel_eng,true);
		$criteria->compare('definisi_variabel_eng',$this->definisi_variabel_eng,true);
		$criteria->compare('keterangan_variabel_eng',$this->keterangan_variabel_eng,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Variabel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

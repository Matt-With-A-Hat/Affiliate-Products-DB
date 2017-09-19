<?php

class ApdCsv {

	protected $csv;

	protected $file;

	protected $fileDelimiter;

	protected $csvFields;

	protected $fieldInfo;

	public function __construct( $csv ) {
		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}
		$this->setCsv( $csv );
		$file = new SplFileObject( $this->csv );
		$this->setFile( $file );
		$this->setFileDelimiter();
		$this->setCsvFields();
	}

	/**
	 * @return mixed
	 */
	public function getCsv() {
		return $this->csv;
	}

	/**
	 * @param mixed $csv
	 */
	public function setCsv( $csv ) {
		$this->csv = $csv;
	}

	/**
	 * @return mixed
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @param mixed $file
	 */
	public function setFile( $file ) {
		$this->file = $file;
	}

	/**
	 * @return mixed
	 */
	public function getFileDelimiter() {
		return $this->fileDelimiter;
	}

	/**
	 * @internal param $csv
	 *
	 * @internal param $file
	 */
	public function setFileDelimiter() {

		$control   = $this->file->getCsvControl();
		$delimiter = $control[0];

		$this->fileDelimiter = $delimiter;
	}

	/**
	 * @return mixed
	 */
	public function getCsvFields() {
		return $this->csvFields;
	}

	/**
	 * path to csv file or $_FILE array of csv file
	 *
	 * @internal param $csv
	 */
	public function setCsvFields() {
		$fileDelimiter   = $this->getFileDelimiter();
		$fields          = $this->file->fgetcsv( $fileDelimiter );
		$this->csvFields = $fields;
	}

	/**
	 * match the CSVs fields with fields from a database and look for fieldtypes
	 *
	 * @return mixed
	 */
	public function getFieldInfo( $tablename ) {
		if ( empty( $this->fieldInfo ) ) {
			$this->setFieldInfo( $tablename );
		}

		return $this->fieldInfo;
	}

	/**
	 * @param $tablename
	 */
	public function setFieldInfo( $tablename ) {
		$table     = new ApdDatabase( $tablename );
		$tableinfo = $table->getTableInfo();
		$csvFields = array_remove_tail( $this->csvFields, "_" );
		$fieldinfo = array();

		$i = 0;
		krumo( $csvFields );
		foreach ( $csvFields as $csvField ) {
			$fieldinfo[ $i ] = array(
				'Name' => $csvField,
				'Type' => 'text'
			);
			foreach ( $tableinfo as $columninfo ) {
				if ( $columninfo['Field'] == $csvField ) {
					$fieldinfo[ $i ]['Type'] = $columninfo['Type'];
					break;
				}
			}
			$i ++;
		}

		$this->fieldInfo = $fieldinfo;
	}

	/**
	 *
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
//	function getFieldType( $name ) {
//		$tableinfo = $this->getTableInfo();
//
//
//		return $fieldtype;
//	}
}
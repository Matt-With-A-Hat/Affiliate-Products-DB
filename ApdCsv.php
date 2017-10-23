<?php

class ApdCsv {

	protected $csv;

	protected $fileObject;

	protected $array;

	protected $fileDelimiter;

	protected $csvFields;

	protected $fieldInfo;

	public function __construct( $csv ) {
		if ( is_array( $csv ) ) {
			$csv = $csv['tmp_name'];
		}
		$this->setCsv( $csv );
		$fileObject = new SplFileObject( $this->csv );
		$this->setFileObject( $fileObject );
		$this->setFileDelimiter();
		$this->setCsvFields();
		$this->setArray();
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
	public function getFileObject() {
		return $this->fileObject;
	}

	/**
	 * @param mixed $fileObject
	 */
	public function setFileObject( $fileObject ) {
		$this->fileObject = $fileObject;
	}

	/**
	 * @return mixed
	 */
	public function getArray() {
		return $this->array;
	}

	/**
	 *
	 */
	public function setArray() {
		$array = array();
		$this->fileObject->setFlags( SplFileObject::READ_CSV );
		foreach ( $this->fileObject as $row ) {
			$array[] = $row;
		}
		$this->array = $array;
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

		$control   = $this->fileObject->getCsvControl();
		$delimiter = $control[0];

		$this->fileDelimiter = $delimiter;
	}

	/**
	 * @param bool $noTail
	 *
	 * @return array
	 */
	public function getCsvFields( $noTail = false ) {
		if ( $noTail === false ) {
			return $this->csvFields;
		} else {
			return array_remove_tail( $this->csvFields, "_" );
		}
	}

	/**
	 * path to csv file or $_FILE array of csv file
	 *
	 * @internal param $csv
	 */
	public function setCsvFields() {
		$fileDelimiter   = $this->getFileDelimiter();
		$fields          = $this->fileObject->fgetcsv( $fileDelimiter );
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
	 * Match the fields of the CSV with the corresponding databases table column info.
	 * It will set a 2D array, containing an array for each CSV field with the respective column type in the database, like so:
	 *
	 * Array(2)
	 *      Name String(15) => ChildProtection
	 *      Type String(10) => tinyint(1)
	 * Array(2)
	 * Array(2)
	 * ...
	 *
	 * @param $tablename
	 */
	public function setFieldInfo( $tablename ) {
		$table     = new ApdDatabase( $tablename );
		$tableinfo = $table->getTableInfo();
		$csvFields = $this->getCsvFields( true );
		$fieldinfo = array();

		$i = 0;
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
}
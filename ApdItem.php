<?php

/**
 * Class ApdItem
 */
class ApdItem{

	/**
	 * the tablename where the item can be found
	 *
	 * @var
	 */
	protected $tablename;

	function __construct($tablename){

		$this->setTablename(($tablename));

	}

	/**
	 * @return mixed
	 */
	public function getTablename() {
		return $this->tablename;
	}

	/**
	 * @param mixed $tablename
	 */
	public function setTablename( $tablename ) {
		$this->tablename = add_table_prefix($tablename);
	}

	/**
	 * get the APD item selected by asin
	 *
	 * @param $asin
	 *
	 * @return array|bool|null|object|void
	 */
	public function getItem( $asin ) {

		$apdDB = new ApdDatabase();

		$item = $apdDB->getRow( $asin, $this->tablename );

		return $item;
	}


}
<?php

/**
 * Class ApdItem
 */
class ApdItem {

	/**
	 * the tablename where the item can be found
	 *
	 * @var
	 */
	protected $tablename;

	function __construct( $tablename ) {

		$this->setTablename( ( $tablename ) );

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
		$this->tablename = add_table_prefix( $tablename );
	}

	/**
	 * get the APD item selected by asin
	 *
	 * @param $asin
	 *
	 * @return array|bool|null|object|void
	 */
	public function getItem( $asin ) {

		global $wpdb;
		$apdDatabase = new ApdDatabase();

		$uniqeField   = $apdDatabase->getUniqueColumn( $this->tablename );
		$sql          = "SELECT * FROM $this->tablename WHERE $uniqeField = %s";
		$apdDatabase->dbItem = $wpdb->get_row( $wpdb->prepare( $sql, $asin ), OBJECT );

		if ( empty( $apdDatabase->dbItem ) ) {

			if ( APD_DEBUG ) {
				$error = "Entry does not exist: $asin";
				print_error($error, __METHOD__, __LINE__);
			}

			return false;

		}

		return $apdDatabase->dbItem;

		return $item;
	}


}
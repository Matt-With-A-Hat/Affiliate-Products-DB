<?php

/**
 * Class ApdCustomItem
 *
 * The class representing custom items that were uploaded to the database.
 */
class ApdCustomItem {

	protected static $uniqueItemFields = array(
		'asin',
		'post_id'
	);

	protected static $itemFields = array(
		'asin',
		'post_id',
		'table',
		'last_edit'
	);

	/**
	 * the cronjobs' name for updating the asins table
	 *
	 * @var string
	 */
	protected static $cronjobName = 'asin';

	/**
	 * the products table where the item can be found
	 *
	 * @var
	 */
	protected $itemTable;

	/**
	 * the junction table where all items are stored with their respective asin
	 * and the products table containing each item
	 *
	 * @var string
	 */
	protected $asinTable;

	/**
	 * the items asin
	 *
	 * @var
	 */
	protected $asin;

	/**
	 * The refined array version of the custom item object
	 *
	 * @var array|bool|null|object|void
	 */
	protected $arrayN;

	/**
	 * The refined associative array version of the custom item object
	 *
	 * @var array|bool|null|object|void
	 */
	protected $arrayA;

	/**
	 * The refined associative array version with changes made to the content, such as HTML integration
	 *
	 * @var
	 */
	protected $arrayR;

	/**
	 * The custom item object
	 *
	 * @var array|bool|null|object|void
	 */
	protected $object;

	/**
	 * The refined object version with changes made to the content, such as HTML integration
	 *
	 * @var
	 */
	protected $objectR;

	function __construct( $asin ) {

		$this->setAsinTable();
		$this->setAsin( $asin );
		$this->setItemTable();

		$this->arrayN  = $this->getItem( ARRAY_N );
		$this->arrayA  = $this->getItem( ARRAY_A );
		$this->object  = $this->getItem();
		$this->objectR = $this->getRefinedObject();
		$this->arrayR  = $this->getRefinedArray();

	}

	/**
	 * @return mixed
	 */
	public function getAsin() {
		return $this->asin;
	}

	/**
	 * @return mixed
	 */
	public function getItemTable() {
		return $this->itemTable;
	}

	/**
	 * @return array
	 */
	public static function getUniqueItemFields() {
		return self::$uniqueItemFields;
	}

	/**
	 * @return array
	 */
	public static function getItemFields() {
		return self::$itemFields;
	}

	/**
	 * @return string
	 */
	public static function getCronjobName() {
		return self::$cronjobName;
	}

	/**
	 * @return string
	 */
	public function getAsinTable() {
		return $this->asinTable;
	}

	/**
	 * @param mixed $asin
	 */
	public function setAsin( $asin ) {
		$this->asin = $asin;
	}

	/**
	 * @return array|bool|null|object|void
	 */
	public function getArrayN() {
		return $this->arrayN;
	}

	/**
	 * @param array|bool|null|object|void $arrayN
	 */
	public function setArrayN( $arrayN ) {
		$this->arrayN = $arrayN;
	}

	/**
	 * @return array|bool|null|object|void
	 */
	public function getArrayA() {
		return $this->arrayA;
	}

	/**
	 * @param array|bool|null|object|void $arrayA
	 */
	public function setArrayA( $arrayA ) {
		$this->arrayA = $arrayA;
	}

	/**
	 * @return array|bool|null|object|void
	 */
	public function getObject() {
		return $this->object;
	}

	/**
	 * @param array|bool|null|object|void $object
	 */
	public function setObject( $object ) {
		$this->object = $object;
	}

	/**
	 * @return mixed
	 */
	public function getArrayR() {
		return $this->arrayR;
	}

	/**
	 * @param mixed $arrayR
	 */
	public function setArrayR( $arrayR ) {
		$this->arrayR = $arrayR;
	}

	/**
	 * @return mixed
	 */
	public function getObjectR() {
		return $this->objectR;
	}

	/**
	 * @param mixed $objectR
	 */
	public function setObjectR( $objectR ) {
		$this->objectR = $objectR;
	}

	/**
	 * get the item tablename from the asin table
	 */
	public function setItemTable() {
		global $wpdb;

		$sql       = "SELECT `table` FROM $this->asinTable WHERE `Asin` = %s";
		$itemTable = $wpdb->get_var( $wpdb->prepare( $sql, $this->asin ) );

		if ( ! empty( $itemTable ) ) {
			$this->itemTable = add_table_prefix( $itemTable );
		} else {
			$error = "Item doesn't exist.";
			print_error( $error, __METHOD__, __LINE__ );
		}
	}

	/**
	 *
	 */
	public function setAsinTable() {
		$this->asinTable = add_table_prefix( APD_ASIN_TABLE );
	}

	/**
	 * get the APD item selected by asin
	 * @return array|bool|null|object|void
	 * @internal param $asin
	 *
	 */
	private function getItem( $output = OBJECT ) {

		global $wpdb;
		$database         = new ApdDatabase( $this->itemTable );
		$sql              = "SELECT * FROM $this->itemTable WHERE Asin = %s";
		$database->dbItem = $wpdb->get_row( $wpdb->prepare( $sql, $this->asin ), $output );

		if ( empty( $database->dbItem ) ) {
			if ( APD_DEBUG ) {
				$error = "Entry does not exist: $this->asin";
				print_error( $error, __METHOD__, __LINE__ );
			}

			return false;
		}

		return $database->dbItem;
	}

	private function getRefinedObject() {

		$database         = new ApdDatabase( $this->itemTable );
		$tableInfo        = $database->getTableInfo();
		$customItemObject = $this->getObject();

		/**
		 * =Reformat advantages list
		 */
		$advantagesArray = explode( "*", $customItemObject->Advantages );
		$advantagesHtml  = '';

		foreach ( $advantagesArray as $advantage ) {
			$advantagesHtml .= "<li>" . $advantage . "</li>";
		}
		$customItemObject->Advantages = $advantagesHtml;

		/**
		 * =Reformat disadvantages list
		 */
		$disadvantagesArray = explode( "*", $customItemObject->Disadvantages );
		$disadvantagesHtml  = '';

		foreach ( $disadvantagesArray as $disadvantage ) {
			$disadvantagesHtml .= "<li>" . $disadvantage . "</li>";
		}
		$customItemObject->Disadvantages = $disadvantagesHtml;

		/**
		 * =Reformat scope of delivery
		 * creates two adjacent columns in bootstrap
		 */
		$deliveryArray = explode( "*", $customItemObject->ScopeOfDelivery );
		$deliveryHtml  = '<div class="row"><div class="col-md-6"><ul class="list-pro">{$column1}</ul></div><div class="col-md-6"><ul class="list-pro">{$column2}</ul></div></div>';
		$columnSize    = ceil( sizeof( $deliveryArray ) / 2 );
		$i             = 0;
		$column1       = '';
		$column2       = '';
		foreach ( $deliveryArray as $deliveryItem ) {
			if ( $i < $columnSize ) {
				$column1 .= "<li>$deliveryItem</li>";
			} else {
				$column2 .= "<li>$deliveryItem</li>";
			}
			$i ++;
		}
		$deliveryHtml = preg_replace( '/{\$column1}/', $column1, $deliveryHtml );
		$deliveryHtml = preg_replace( '/{\$column2}/', $column2, $deliveryHtml );
		$customItemObject->ScopeOfDelivery = $deliveryHtml;

		/**
		 * =Convert bool values to checkbox
		 */
		$i = 0;
		foreach ( $customItemObject as $key => $item ) {

			$fieldType = $tableInfo[ $i ++ ]['Type'];

			if ( type_is_boolean( $fieldType ) ) {

				if ( field_is_true( $item ) ) {
					$customItemObject->$key = '<i class="check"></i>';
				} else if ( field_is_false( $item ) ) {
					$customItemObject->$key = '<i class="times"></i>';
				}

			}

		}

		//convert decimal percent values to percent numbers
		foreach ( $customItemObject as $key => $item ) {
			if ( preg_match( "/percent/i", $key ) ) {
				$customItemObject->$key = $item * 100;
			}
		}

		return $customItemObject;
	}

	private function getRefinedArray() {
		if ( empty( $this->objectR ) ) {
			return (array) $this->getRefinedObject();
		} else {
			return (array) $this->objectR;
		}
	}
}
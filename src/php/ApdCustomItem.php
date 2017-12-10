<?php

/**
 * Class ApdCustomItem
 *
 * The class representing custom items that were uploaded to the database.
 */
class ApdCustomItem {

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

	/**
	 * The refined object as json
	 *
	 * @var
	 */
	protected $json;

	/**
	 * the objects fields
	 *
	 * @var
	 */
	protected $objectFields;

	function __construct( $asin ) {

		if ( empty( $asin ) ) {
			$error = "No ASIN has been supplied";
			print_error( $error, __METHOD__, __LINE__ );

			return null;
		}
		$this->setAsinTable();
		$this->setAsin( $asin );
		$this->setItemTable();

		$this->arrayN  = $this->getItem( ARRAY_N );
		$this->arrayA  = $this->getItem( ARRAY_A );
		$this->object  = $this->getItem();
		$this->objectR = $this->refineObject();
		$this->arrayR  = $this->getRefinedArray();
		$this->json    = json_encode( $this->getObjectR() );

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
	 * @return mixed
	 */
	public function getJson() {
		return $this->json;
	}

	/**
	 * @param mixed $json
	 */
	public function setJson( $json ) {
		$this->json = $json;
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
	public function getObjectFields() {
		return $this->objectFields;
	}

	/**
	 * @param mixed $objectFields
	 */
	public function setObjectFields( $objectFields ) {
		$this->objectFields = $objectFields;
	}

	/**
	 * get the item tablename from the asin table
	 */
	public function setItemTable() {
		global $wpdb;

		$sql = "SELECT `table` FROM $this->asinTable WHERE `Asin` = %s";
//		krumo($wpdb->prepare( $sql, $this->asin ));
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

	/**
	 * refine values and return object
	 *
	 * @return array|bool|null|object|void
	 */
	private function refineObject() {
		$database         = new ApdDatabase( $this->itemTable );
		$tableinfo        = $database->getTableInfo();
		$customItemObject = $this->getObject();

//		/**
//		 * =Reformat advantages list
//		 */
//		$advantagesArray = explode( "*", $customItemObject->Advantages );
//		$advantagesHtml  = '';
//
//		foreach ( $advantagesArray as $advantage ) {
//			$advantagesHtml .= "<li>" . $advantage . "</li>";
//		}
//		$customItemObject->Advantages = $advantagesHtml;
//
//		/**
//		 * =Reformat disadvantages list
//		 */
//		$disadvantagesArray = explode( "*", $customItemObject->Disadvantages );
//		$disadvantagesHtml  = '';
//
//		foreach ( $disadvantagesArray as $disadvantage ) {
//			$disadvantagesHtml .= "<li>" . $disadvantage . "</li>";
//		}
//		$customItemObject->Disadvantages = $disadvantagesHtml;

		foreach ( $customItemObject as $key => $value ) {
			/**
			 * =Reformat list values
			 * creates two adjacent columns in bootstrap and shorter version
			 */
			if ( preg_match( '/^(List:)/', $value ) ) {
				$value = str_replace( "List:", "", $value );
				$list  = explode( "*", $value );

				//if list is empty, put 'k. A.' as one list item
				( $list[0] === '' ) ? $list[0] = 'k. A.' : true;

				$htmlListWideTpl   = '<div class="row"><div class="col-md-6"><ul>{$column1}</ul></div><div class="col-md-6"><ul>{$column2}</ul></div></div>';
				$htmlListNarrowTpl = '<div class="row"><div class="col-md-12"><ul>{$columnNarrow}</ul></div></div>';
				$columnSize        = ceil( sizeof( $list ) / 2 );
				$i                 = 0;
				$j                 = 0;
				$column1           = '';
				$column2           = '';
				$columnNarrow      = '';
				$columnShort       = '';
				$columnOne         = '';
				foreach ( $list as $listitem ) {
					if ( $i < $columnSize ) {
						$column1 .= "<li>$listitem</li>";
					} else {
						$column2 .= "<li>$listitem</li>";
					}
					$columnNarrow .= "<li>$listitem</li>";
					if ( $j < 3 ) {
						$columnShort .= "<li>$listitem</li>";
					}
					if ( $j < 1 ) {
						$columnOne .= "<li>$listitem</li>";
					}
					$i ++;
					$j ++;
				}
				$htmlListWide   = preg_replace( '/{\$column1}/', $column1, $htmlListWideTpl );
				$htmlListWide   = preg_replace( '/{\$column2}/', $column2, $htmlListWide );
				$htmlListNarrow = preg_replace( '/{\$columnNarrow}/', $columnNarrow, $htmlListNarrowTpl );
				$htmlListShort  = preg_replace( '/{\$columnNarrow}/', $columnShort, $htmlListNarrowTpl );
				$htmlListOne    = preg_replace( '/{\$columnNarrow}/', $columnOne, $htmlListNarrowTpl );

				$customItemObject->$key           = $htmlListWide;
				$narrowListKey                    = $key . "Narrow";
				$customItemObject->$narrowListKey = $htmlListNarrow;
				$shortListKey                     = $key . "Short";
				$customItemObject->$shortListKey  = $htmlListShort;
				$shortListKey                     = $key . "One";
				$customItemObject->$shortListKey  = $htmlListOne;
			}

			/**
			 * =Reformat vertical list values
			 */
			if ( preg_match( '/^(VList:)/', $value ) ) {
				$value = str_replace( "VList:", "", $value );
				$list  = explode( "*", $value );

				$htmlList = '<ul class="v-list">';
				foreach ( $list as $item ) {
					$htmlList .= '<li>';
					$htmlList .= $item;
					$htmlList .= '</li>';
				}
				$htmlList .= '</ul>';

				$customItemObject->$key = $htmlList;
			}

			/**
			 * =Reformat stars
			 */
			if ( preg_match( '/^(Stars:)/', $value ) ) {
				$numberStars = str_replace( "Stars:", "", $value );

				$ratingStarsHtml = '<span class="rating-stars">';
				$fullStar        = '<i class="fa fa-star"></i>';
				$halfStar        = '<i class="fa fa-star-half-o"></i>';
				$emptyStar       = '<i class="fa fa-star-o"></i>';

				$nFullStars  = floor( $numberStars );
				$nHalfStars  = $numberStars - $nFullStars;
				$nEmptyStars = floor( 5 - $numberStars );

				for ( $i = 0; $i < $nFullStars; $i ++ ) {
					$ratingStarsHtml .= $fullStar;
				}
				if ( $nHalfStars != 0 ) {
					$ratingStarsHtml .= $halfStar;
				}
				for ( $i = 0; $i < $nEmptyStars; $i ++ ) {
					$ratingStarsHtml .= $emptyStar;
				}
				$ratingStarsHtml .= "</span>";

//				$htmlKey                    = $key . "Html";
				$customItemObject->$key = $ratingStarsHtml;
			}

			/**
			 * =Reformat english digit format to german
			 */
//			if ( preg_match( "/[0-9]{1,9}\.[0-9]{1,9}/", $value ) ) {
//				$customItemObject->$key = preg_replace( "/\./", ",", $value );
//			}
		}


		/**
		 * =Convert bool values to checkbox
		 */
		$i = 0;
		foreach ( $customItemObject as $key => $item ) {

			$fieldType = $tableinfo[ $i ++ ]['Type'];

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
				if ( is_numeric( $item ) ) {
					$customItemObject->$key = (float) $item * 100;
				}
			}
		}

		return $customItemObject;
	}

	private function getRefinedArray() {
		if ( empty( $this->objectR ) ) {
			return (array) $this->refineObject();
		} else {
			return (array) $this->objectR;
		}
	}
}
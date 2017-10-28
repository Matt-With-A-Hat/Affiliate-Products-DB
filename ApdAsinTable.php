<?php

Class ApdAsinTable {
	/**
	 * the cronjobs' name for updating the asins table
	 *
	 * @var string
	 */
	public static $cronjobName = 'apd_update_asin_table';

	/**
	 * @var array
	 */
	public static $itemFields = array(
		'asin'      => 'varchar',
		'post_id'   => 'varchar',
		'table'     => 'varchar',
		'last_edit' => 'varchar'
	);

	/**
	 * @var array
	 */
	public static $uniqueItemFields = array(
		'asin'
	);

	/**
	 * @return array
	 */
	public static function getUniqueItemFields() {
		return self::$uniqueItemFields;
	}

	/**
	 * If info is true, an associative array will be returned, containing the field types and field names
	 * If info is false, a simple array will be returned, containing only the field names
	 *
	 * @param bool $info
	 *
	 * @return array
	 */
	public static function getItemFields( $info = true ) {
		if ( $info ) {
			return self::$itemFields;
		} else {
			return array_keys( self::$itemFields );
		}
	}

	/**
	 * @return string
	 */
	public static function getCronjobName() {
		return self::$cronjobName;
	}
}
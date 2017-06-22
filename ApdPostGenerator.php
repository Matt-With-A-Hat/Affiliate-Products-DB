<?php

/**
 * Class ApdPostGenerator
 *
 * generates posts for all productes of one table
 */
class ApdPostGenerator {

	/**
	 * the table whose items are used to create posts
	 *
	 * @var
	 */
	protected $tablename;

	/**
	 * the name of the column containing the posts title
	 *
	 * @var
	 */
	protected $titleColumn;

	/**
	 * the categories the posts should have.
	 *
	 * @var
	 */
	protected $categories;

	/**
	 * the default content the posts should have
	 *
	 * @var
	 */
	protected $content;

	public function __construct( $tablename, $titleColumn, array $categories = null, $content = '' ) {
		$this->setTablename( $tablename );
		$this->setTitleColumn( $titleColumn );
		$this->setCategories( $categories );
		$this->setContent( $content );
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
	 * @return mixed
	 */
	public function getTitleColumn() {
		return $this->titleColumn;
	}

	/**
	 * @param mixed $titleColumn
	 */
	public function setTitleColumn( $titleColumn ) {
		$this->titleColumn = $titleColumn;
	}

	/**
	 * @return mixed
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param mixed $categories
	 */
	public function setCategories( $categories ) {
		$this->categories = $categories;
	}

	/**
	 * @return mixed
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param mixed $content
	 */
	public function setContent( $content ) {
		$this->content = $content;
	}

	/**
	 * get all items of the specified table
	 *
	 * @return array|bool|null|object|void
	 */
	public function getItems() {

		$databaseService = new ApdDatabaseService();
		$asins           = $databaseService->getAsins( $this->tablename );

		$items = array();
		foreach ( $asins as $asin ) {
			$items[] = ( new ApdItem( $asin ) )->getItem();
		}

		return $items;
	}

	/**
	 * generates a post for every item inside the supplied table
	 *
	 * @return int
	 */
	public function generatePosts() {
		global $wpdb;
		$items = $this->getItems();
		$core = new ApdCore();

		$count = 0;
		foreach ( $items as $item ) {
			$content = $core->parseTpl($item->Asin, $this->content);
			$titleColumn = $this->titleColumn;
			$postarr     = array(
				'post_content' => $content,
				'post_title'   => $item->$titleColumn,
				'post_status'  => 'draft',
			);

			krumo($postarr);
			$postId = wp_insert_post( $postarr );
			if ( $postId ) {
				if ( $this->categories ) {
					wp_set_post_categories( $this->categories );
				}
				$permalink = get_permalink($postId, true);
				//insert post ID and permalink into products table
				$sql  = "UPDATE $this->tablename SET `PostId` = %s, `Permalink` = %s WHERE `Asin` = %s";
				$args = array(
					0 => $postId,
					1 => $permalink,
					2 => $item->Asin
				);
				krumo( $wpdb->prepare( $sql, $args ) );
				$wpdb->query( $wpdb->prepare( $sql, $args ) );
			}
			$count ++;
		}

		return $count;
	}
}
<?php
/**
 * Create the setup menupage
 */
function setup_menu_page() {

	$answer = handle_upload_form();

	include( 'inc/tpl/apd-setupmenu-tpl.php' );

}

/**
 * Handles the form content
 */
function handle_upload_form() {

	$answer = array( null );

	if ( $_POST['upload'] ) {
		// First check if the file appears in the _FILES array
		if ( isset( $_FILES['csv-file'] ) AND $_POST['table-name'] ) {

			$csv       = $_FILES['csv-file'];
			$tablename = $_POST['table-name'];

			//check if file is csv an abort if not
			$csv_name = explode( ".", $csv['name'] );
			$csv_name = $csv_name[1];

			if ( $csv_name != 'csv' ) {
				$answer['text']    = "Error uploading file: File is not a CSV file";
				$answer['success'] = 'alert-danger';

				return $answer;
			}

			$apdDB  = new ApdDatabase( $tablename );
			$result = $apdDB->addCsvToDatabase( $csv );

			if ( $result === 1 ) {
				$answer['text']    = "CSV upload successful. Table <strong><em>\"$tablename\"</em></strong> was <strong>created</strong>.";
				$answer['success'] = 'alert-success';
			} else if ( $result === 2 ) {
				$answer['text']    = "Existing table <strong><em>\"$tablename\"</em></strong> was <strong>replaced</strong> with new one (debug mode).";
				$answer['success'] = 'alert-success';
			} else if ( $result === 3 ) {
				$answer['text']    = "Existing table <strong><em>\"$tablename\"</em></strong> has been <strong>updated</strong> with content from CSV file.";
				$answer['success'] = 'alert-success';
			} else {
				$answer['text']    = "<strong>Something went wrong</strong>, while trying to upload the CSV";
				$answer['success'] = 'alert-danger';

				return $answer;
			}

			return $answer;

		} else {

			$answer['text']    = "Please fill out missing fields!";
			$answer['success'] = 'alert-danger';

			return $answer;
		}
	} else if ( $_POST['generate-posts'] ) {
		if ( isset( $_POST['product-tables-selection'] ) ) {
			$tablename   = $_POST['product-tables-selection'];
			$titleColumn = $_POST[ 'title-column-' . $tablename ];
			$categories  = $_POST['categories'];
			$content     = $_POST['content'];
			( empty( $categories ) ) ? $categories = null : false;
			( empty( $content ) ) ? $content = '' : false;

			if ( $_POST['single-post'] ) {
				$asin          = $_POST[ 'product-' . $tablename ];
				$postGenerator = new ApdPostGenerator( $tablename, $titleColumn, $categories, $content, $asin );
			} else {
				$postGenerator = new ApdPostGenerator( $tablename, $titleColumn, $categories, $content );
			}
			$count = $postGenerator->generatePosts();

			$answer['text']    = "$count posts generated successfully";
			$answer['success'] = 'alert-success';

			return $answer;
		} else {
			$answer['text']    = "Please fill in required fields";
			$answer['success'] = 'alert-danger';

			return $answer;
		}
	}
}
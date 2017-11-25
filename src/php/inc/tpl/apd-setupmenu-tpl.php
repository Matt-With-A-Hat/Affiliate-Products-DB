<?php
$htmlTablenames  = '';
$htmlColumns     = '';
$databaseService = new ApdDatabaseService();
$productTables   = $databaseService->getProductTables();
$i               = 0;
foreach ( $productTables as $tablename ) {
	( $i == 0 ) ? $class = 'display-block' : $class = '';
	$tablename = remove_table_prefix( $tablename );
	$htmlTablenames .= "<option value='$tablename'>$tablename</option>";

	$database = new ApdDatabase( $tablename );
	$columns  = $database->getTableColumns();
	$htmlColumns .= "<div id='$tablename-column-box' class='row column-box $class'>";
	$htmlColumns .= "<div class='col-md-4 text-right'><label for='$tablename-title-column'>Column in <strong><em>\"$tablename\"</em></strong> for Title</label></div>";
	$htmlColumns .= "<div class='col-md-6'><select id='$tablename-title-column' name='title-column-$tablename' class='form-control'>";
//	$htmlColumns .= "<option value=''></option>";
	foreach ( $columns as $column ) {
		$htmlColumns .= "<option value='$column'>$column</option>";
	}
	$htmlColumns .= "</select>";
	$htmlColumns .= "</div>";
	$htmlColumns .= "</div>";

	$columns  = array( 'Asin', 'Longname' );
	$products = $database->getColumns( $columns );
	$htmlProducts .= "<div id='$tablename-product-box' class='row product-box $class'>";
	$htmlProducts .= "<div class='col-md-4 text-right'><label class='product-selector disabled' for='$tablename-product'>Product from <strong><em>\"$tablename\"</em></strong></label></div>";
	$htmlProducts .= "<div class='col-md-6'><select disabled id='$tablename-product' name='product-$tablename' class='product-selector form-control'>";
//	$htmlProducts .= "<option value=''></option>";
	foreach ( $products as $product ) {
		$htmlProducts .= "<option value='" . $product['Asin'] . "'>" . $product['Longname'] . "</option>";
	}
	$htmlProducts .= "</select>";
	$htmlProducts .= "</div>";
	$htmlProducts .= "</div>";
	$i ++;
}

$htmlCategories  = '';
$databaseService = new ApdDatabaseService();
$categories      = $databaseService->getPostCategories();
foreach ( $categories as $category ) {
	$htmlCategories .= "<option value='$category->cat_ID'>$category->cat_name</option>";
}
?>

<div class="apdsetupmenu">
	<h1>Affiliate Products Database</h1>
	<div class="alert <?= $answer['success']; ?>" role="alert"><?= ( $answer['text'] ) ? $answer['text'] : false; ?></div>
	<div class="row">
		<div class="col-md-9 section">
			<h2>Upload CSV</h2>
			<div class="form-description">Please choose a name for the database table to be created and choose a CSV file to upload.</div>
			<form method="post" enctype="multipart/form-data">
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label for="csv-upload">CSV File*</label>
							<input type="file" class="form-control-file" id="csv-file" name="csv-file">
						</div>
						<div class="col-md-6">
							<label for="table-name">Tablename*</label>
							<input type="text" class="form-control" id="table-name" name="table-name">
						</div>
					</div>
				</div>
				<?php submit_button( 'Upload', 'primary', 'upload' ) ?>
				<!--		<button type="submit" class="btn btn-primary">Submit</button>-->
			</form>
		</div>
		<div class="col-md-6"></div>
	</div>
	<div class="row post-generation">
		<div class="col-md-9 section">
			<h2>Generate Posts From Products</h2>
			<div class="form-description">Choose a products table you want to use for generating posts</div>
			<form method="post">
				<div class="form-group">
					<div class="row">
						<div class="col-md-4 text-right">
							<label for="product-tables-selection">Table</label>
						</div>
						<div class="col-md-6">
							<select class="form-control" id="product-tables-selection" name="product-tables-selection">
								<?= $htmlTablenames; ?>
							</select>
						</div>
					</div>
					<?= $htmlColumns; ?>
					<div class="row">
						<div class="col-md-4 text-right">
							<label for="categories">Category for posts</label>
						</div>
						<div class="col-md-6">
							<select class="form-control" id="categories" name="categories">>
								<?= $htmlCategories; ?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 text-right">
							<input id="single-post" type="checkbox" name="single-post" value="1">
							<label for="single-post">Single Post</label>
						</div>
						<div class="col-md-4"></div>
					</div>
					<?= $htmlProducts; ?>
					<div class="row">
						<div class="col-md-12">
							<?php submit_button( 'Generate Posts', 'primary', 'generate-posts' ) ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label for="content">Default content</label>
							<textarea class="form-control" rows="20" id="content" name="content"><?= file_get_contents( APD_PLUGIN_PATH . "/inc/tpl/default-post-content.html" ); ?></textarea>
						</div>
					</div>
				</div>
				<?php submit_button( 'Generate Posts', 'primary', 'generate-posts' ) ?>
			</form>
		</div>
	</div>
	<span>* required fields</span>
</div>
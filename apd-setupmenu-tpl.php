<div class="apdsetupmenu">
	<h1>Affiliate Products Database</h1>
	<div class="alert <?= $answer['success']; ?>" role="alert"><?= ( $answer['text'] ) ? $answer['text'] : false; ?></div>
	<div class="row">
		<div class="col-md-6 section">
			<h2>Upload CSV</h2>
			<div class="form-description">Please choose a name for the database table to be created and choose a CSV file to upload.</div>
			<form method="post" enctype="multipart/form-data">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
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
	<div class="row">
		<div class="col-md-6 section">
			<h2>Generate posts from products</h2>
			<div class="form-description">Choose a products table you want to use for generating posts</div>
			<form method="post">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="product-tables-selection">Table*</label>
							<select class="form-control" name="product-tables-selection" id="product-tables-selection">
								<option value=""></option>
								<?php
								$databaseService = new ApdDatabaseService();
								$productTables   = $databaseService->getProductTables();
								foreach ( $productTables as $tablename ) {
									$tablename = remove_table_prefix( $tablename );
									echo "<option value='$tablename'>$tablename</option>";
								}
								?>
							</select>
						</div>
						<div class="col-md-6">
							<label for="title-column">Column for Title</label>
							<input type="text" class="form-control" id="title-column" name="title-column">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<label for="categories">Category
								<!--								<small>(seperate with comma)</small>-->
							</label>
							<select class="form-control" id="categories" name="categories">>
								<option value=""></option>
								<?php
								$databaseService = new ApdDatabaseService();
								$categories      = $databaseService->getPostCategories();
								foreach ( $categories as $category ) {
									echo "<option value='$category->cat_ID'>$category->cat_name</option>";
								}
								?>
							</select>
							<!--							<input type="text" class="form-control" id="categories" name="categories">-->
						</div>
						<div class="col-md-6"></div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label for="content">Default content</label>
							<textarea class="form-control" rows="20" id="content" name="content"><?= file_get_contents(APD_PLUGIN_PATH . "/inc/tpl/default-post-content.html"); ?></textarea>
						</div>
					</div>
				</div>
				<?php submit_button( 'Generate Posts', 'primary', 'generate-posts' ) ?>
			</form>
		</div>
	</div>
	<span>* required fields</span>
</div>

<?php
//$slug        = 'Worx Landroid S500i';
//$post_id     = 1264;
//$post_status = 'draft';
//$post_type   = 'post';
//$post_parent = 0;
//
//$databaseService->getPostCategories();
//
//
//
////$categories = get_categories( $args );
////krumo( $categories );
//
////$permalink = wp_unique_post_slug( $slug, $post_id, $post_status, $post_type, $post_parent );
////krumo($permalink);
//$permalink = get_permalink( 1413, true );
//krumo( $permalink );
//$permalink = get_permalink( 1413, false );
//krumo( $permalink );
//$permalink = get_post_permalink( 1413, true );
//krumo( $permalink );
//$permalink = get_post_permalink( 1413, false );
//krumo( $permalink );
//?>
<div class="apdsetupmenu">
	<h2>Affiliate Product Database</h2>
	<div class="row">
		<div class="col-md-8">
			<div class="form-description">Please chose a name for the database table to be create and chose a CSV file to upload.</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<form method="post" enctype="multipart/form-data">
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label for="table-name">Tablename</label>
							<input type="text" class="form-control" id="table-name" name="table-name">
						</div>
						<div class="col-md-4">
							<label for="csv-upload">CSV File</label>
							<input type="file" class="form-control-file" id="csv-file" name="csv-file">
						</div>
					</div>
				</div>
				<?php submit_button( 'Upload' ) ?>
				<!--		<button type="submit" class="btn btn-primary">Submit</button>-->
			</form>
		</div>
	</div>

</div>
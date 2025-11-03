<?php
$qry = $conn->query("SELECT * from settings limit 1");
if ($qry->num_rows > 0) {
	foreach ($qry->fetch_array() as $k => $val) {
		$meta[$k] = $val;
	}
}
?>

<div class="card col-lg-12">
	<div class="card-body">
		<form action="" id="manage-settings">
			<div class="form-group">
				<label for="name" class="control-label"><?= tr('systemName') ?></label>
				<input type="text" class="form-control" id="name" name="name" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>" required>
			</div>

			<div class="form-group">
				<label for="" class="control-label"><?= tr('image') ?></label>
				<input type="file" class="form-control" name="img" onchange="displayImg(this,$(this))">
			</div>
			<div class="form-group">
				<img src="<?php echo isset($meta['image']) ? 'assets/img/' . $meta['image'] : '' ?>" alt="" id="cimg">
			</div>
			<div class="form-group">
				<label for="period" class="control-label"><?= tr('period') ?></label>
				<input type="number" class="form-control" id="period" min="0" name="period" value="<?php echo isset($meta['period']) ? $meta['period'] : '' ?>" required>
			</div>
			<center>
				<button class="btn btn-info btn-primary btn-block col-md-2"><?= tr('save') ?></button>
			</center>
		</form>
	</div>
</div>
<style>
	img#cimg {
		max-height: 10vh;
		max-width: 6vw;
	}
</style>

<script>
	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$('#manage-settings').submit(function(e) {
		e.preventDefault()
		// start_load()
		$.ajax({
			url: 'ajax.php?action=save_settings',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			error: err => {
				console.log(err)
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast('Data successfully saved.', 'success')
					setTimeout(function() {
						location.reload()
					}, 1000)
				}
			}
		})

	})
</script>
<style>

</style>
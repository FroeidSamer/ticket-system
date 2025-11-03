<div class="col-lg-12">
	<div class="row">
		<!-- FORM Panel -->
		<div class="col-md-12">
			<form action="" id="manage-uploads">
				<div class="card">
					<div class="card-header">
						uploads Form
					</div>
					<div class="card-body">
						<div id="msg"></div>
						<input type="hidden" name="id">
						<div class="form-group">
							<input type="file" id="chooseFile" multiple="multiple" onchange="displayIMG(this)" accept="image/x-png,image/gif,image/jpeg,video/webm,video/mp4">
							<div id="drop">
								<span id="dname" class="text-center"><?= tr('dropFiles') ?> <br> <?= tr('or') ?> <br> <label for="chooseFile"><strong><?= tr('chooseFile') ?></strong></label></span>
							</div>
						</div>



					</div>
					<div class="imgF" style="display: none " id="img-clone">
						<span class="rem badge badge-primary" onclick="rem_func($(this))"><i class="fa fa-times"></i></span>
					</div>
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> <?= tr('upload') ?></button>
								<button class="btn btn-sm btn-default col-sm-3" type="button" onclick="_reset()"> <?= tr('cancel') ?></button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<!-- FORM Panel -->
	</div>
	<div class="row">
		<!-- Table Panel -->
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div id="uploads">
						<?php
						$uploads = $conn->query("SELECT * from file_uploads order by unix_timestamp(date_uploaded) desc ");
						while ($row = $uploads->fetch_assoc()) :
							$t = explode('.', $row['file_path']);
							$t = $t[1];
						?>
							<div class="img-holder">
								<span>
									<div class="form-check">
										<input class="form-check-input imgs" type="checkbox" value="<?php echo $row['id'] ?>">
									</div>
								</span>
								<p>
									<?php if ($t == 'webm' || $t == 'mp4') : ?>
										<video src="assets/uploads/<?php echo $row['file_path'] ?>"></video>
									<?php else : ?>
										<img src="assets/uploads/<?php echo $row['file_path'] ?>" alt="">
									<?php endif; ?>
								</p>
								<div class="opts">
									<button class="btn btn-rounded btn-sm btn-primary btn-view  mr-4" type="button" data-src="<?php echo $row['file_path'] ?>"><i class="fa fa-eye"></i></button>
									<button class="btn btn-rounded btn-sm btn-danger btn-delete" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></button>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
				</div>
			</div>
		</div>
		<!-- Table Panel -->
	</div>
</div>

<style>
	td {
		vertical-align: middle !important;
	}

	td p {
		margin: unset
	}

	img {
		max-width: 100px;
		max-height: 150px;
	}
</style>
<script>
	function _reset() {
		$('[name="id"]').val('');
		$('#msg').html('')
		$('#manage-uploads').get(0).reset();
	}

	$(document).ready(function() {
		$('#manage-uploads').submit(function(e) {
			e.preventDefault()
			start_load()
			$('#msg').html('')
			$.ajax({
				url: 'ajax.php?action=save_uploads',
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				success: function(resp) {
					if (resp == 1) {
						alert_toast("Data successfully added", 'success')
						setTimeout(function() {
							location.reload()
						}, 1500)

					} else if (resp == 2) {
						$('#msg').html("<div class='alert alert-danger'>Name already exist.</div>")
						end_load()

					}
				}
			})
		})
	})
	$('.btn-view').click(function() {
		viewer_modal($(this).attr('data-src'))
	})
	$('.btn-delete').click(function() {
		_conf("Are you sure to delete this uploads?", "delete_uploads", [$(this).attr('data-id')])
	})

	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	function delete_uploads($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_uploads',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Data successfully deleted", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>
<style>
	#drop {
		min-height: 15vh;
		max-height: 30vh;
		overflow: auto;
		width: calc(100%);
		border: 5px solid #929292;
		margin: 10px;
		border-style: dashed;
		padding: 10px;
		display: flex;
		align-items: center;
		flex-wrap: wrap;
	}

	#uploads {
		min-height: 15vh;
		width: calc(100%);
		margin: 10px;
		padding: 10px;
		display: flex;
		align-items: center;
		flex-wrap: wrap;
	}

	#uploads .img-holder {
		position: relative;
		margin: 1em;
		cursor: pointer;
		min-width: 22vw;
		min-height: 39vh;
		background: black;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	#uploads .img-holder:hover {
		background: #0095ff1f;
	}

	#uploads .img-holder .form-check {
		display: none;
	}

	#uploads .img-holder.checked .form-check {
		display: block;
	}

	#uploads .img-holder.checked {
		background: #0095ff1f;
	}

	#uploads .img-holder img,
	#uploads .img-holder video {
		max-height: 39vh;
		max-width: 22vw;
		margin: .5em;
		background: black
	}

	#uploads .img-holder span {
		position: absolute;
		top: -.5em;
		left: -.5em;
	}

	#dname {
		margin: auto
	}

	img.imgDropped,
	video.imgDropped {
		max-height: 16vh;
		max-width: 7vw;
		margin: 1em;
		background: black
	}

	.imgF {
		border: 1px solid #0000ffa1;
		border-style: dashed;
		position: relative;
		margin: 1em;
	}

	span.rem.badge.badge-primary {
		position: absolute;
		top: -.5em;
		left: -.5em;
		cursor: pointer;
	}

	label[for="chooseFile"] {
		color: #0000ff94;
		cursor: pointer;
	}

	label[for="chooseFile"]:hover {
		color: #0000ffba;
	}

	.opts {
		position: absolute;
		top: 0;
		right: 0;
		background: #00000094;
		width: calc(100%);
		height: calc(100%);
		display: flex;
		justify-content: center;
		align-items: center;
		justify-items: center;
		display: flex;
		opacity: 0;
		transition: all .5s ease;
	}

	.img-holder:hover .opts {
		opacity: 1;

	}

	button.btn.btn-sm.btn-rounded.btn-sm.btn-dark {
		margin: auto;
	}
</style>
<script>
	if (window.FileReader) {
		var drop;
		addEventHandler(window, 'load', function() {
			var status = document.getElementById('status');
			drop = document.getElementById('drop');
			var dname = document.getElementById('dname');
			var list = document.getElementById('list');

			function cancel(e) {
				if (e.preventDefault) {
					e.preventDefault();
				}
				return false;
			}

			// Tells the browser that we *can* drop on this target
			addEventHandler(drop, 'dragover', cancel);
			addEventHandler(drop, 'dragenter', cancel);

			addEventHandler(drop, 'drop', function(e) {
				e = e || window.event; // get window.event if e argument missing (in IE)   
				if (e.preventDefault) {
					e.preventDefault();
				} // stops the browser from redirecting off to the image.
				$('#dname').remove();
				var dt = e.dataTransfer;
				var files = dt.files;
				for (var i = 0; i < files.length; i++) {
					var file = files[i];
					var reader = new FileReader();

					var t = file.type;
					var _types = ['video/webm', 'video/mp4', 'image/x-png', 'image/png', 'image/gif', 'image/jpeg', 'image/jpg'];
					if (_types.indexOf(t) == -1)
						continue;

					reader.readAsDataURL(file);
					addEventHandler(reader, 'loadend', function(e, file) {
						console.log(this.result)

						var bin = this.result;
						var imgF = document.getElementById('img-clone');
						imgF = imgF.cloneNode(true);
						imgF.removeAttribute('id')
						imgF.removeAttribute('style')
						if (t == "video/webm" || t == "video/mp4") {
							var img = document.createElement("video");
						} else {
							var img = document.createElement("img");
						}
						var fileinput = document.createElement("input");
						var fileinputName = document.createElement("input");
						fileinput.setAttribute('type', 'hidden')
						fileinputName.setAttribute('type', 'hidden')
						fileinput.setAttribute('name', 'img[]')
						fileinputName.setAttribute('name', 'imgName[]')
						fileinput.value = bin
						fileinputName.value = file.name
						img.classList.add("imgDropped")
						img.src = bin;
						imgF.appendChild(fileinput);
						imgF.appendChild(fileinputName);
						imgF.appendChild(img);
						drop.appendChild(imgF)
					}.bindToEventHandler(file));
				}
				return false;

			});

			Function.prototype.bindToEventHandler = function bindToEventHandler() {
				var handler = this;
				var boundParameters = Array.prototype.slice.call(arguments);
				return function(e) {
					e = e || window.event; // get window.event if e argument missing (in IE)   
					boundParameters.unshift(e);
					handler.apply(this, boundParameters);
				}
			};
		});
	} else {
		document.getElementById('status').innerHTML = 'Your browser does not support the HTML5 FileReader.';
	}

	function addEventHandler(obj, evt, handler) {
		if (obj.addEventListener) {
			// W3C method
			obj.addEventListener(evt, handler, false);
		} else if (obj.attachEvent) {
			// IE method.
			obj.attachEvent('on' + evt, handler);
		} else {
			// Old school method.
			obj['on' + evt] = handler;
		}
	}

	function displayIMG(input) {

		if (input.files) {
			if ($('#dname').length > 0)
				$('#dname').remove();

			Object.keys(input.files).map(function(k) {
				var reader = new FileReader();
				var t = input.files[k].type;
				var _types = ['video/webm', 'video/mp4', 'image/x-png', 'image/png', 'image/gif', 'image/jpeg', 'image/jpg'];
				if (_types.indexOf(t) == -1)
					return false;
				reader.onload = function(e) {
					// $('#cimg').attr('src', e.target.result);

					var bin = e.target.result;
					var fname = input.files[k].name;
					var imgF = document.getElementById('img-clone');
					imgF = imgF.cloneNode(true);
					imgF.removeAttribute('id')
					imgF.removeAttribute('style')
					if (t == "video/webm" || t == "video/mp4") {
						var img = document.createElement("video");
					} else {
						var img = document.createElement("img");
					}
					var fileinput = document.createElement("input");
					var fileinputName = document.createElement("input");
					fileinput.setAttribute('type', 'hidden')
					fileinputName.setAttribute('type', 'hidden')
					fileinput.setAttribute('name', 'img[]')
					fileinputName.setAttribute('name', 'imgName[]')
					fileinput.value = bin
					fileinputName.value = fname
					img.classList.add("imgDropped")
					img.src = bin;
					imgF.appendChild(fileinput);
					imgF.appendChild(fileinputName);
					imgF.appendChild(img);
					drop.appendChild(imgF)
				}
				reader.readAsDataURL(input.files[k]);
			})

			rem_func()

		}
	}

	function rem_func(_this) {
		_this.closest('.imgF').remove()
		if ($('#drop .imgF').length <= 0) {
			$('#drop').append('<span id="dname" class="text-center">Drop Files Here <br> or <br> <label for="chooseFile"><strong>Choose File</strong></label></span>')
		}
	}
</script>
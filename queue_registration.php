<style>
	.left-side {
		position: absolute;
		width: calc(100%);
		height: calc(100%);
		left: 0;
		top: 0;
		background: url(admin/assets/img/engraving.png);
		background-repeat: no-repeat;
		background-size: cover;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	a.btn.btn-sm.btn-success {
		z-index: 99999;
		position: fixed;
		left: 1rem;
	}

	.close-btn {
		z-index: 99999;
		position: fixed;
		right: 1rem;
		display: flex;
		justify-content: flex-end;
	}

	.close-btn button {
		background: transparent;
		border: none;
		width: 50px;
		height: calc(5%);
		color: lavender;
	}

	#printDiv {
		margin: unset;
		width: 280px;
	}

	#printDiv h2 {
		text-align: center;
		font-weight: 900;
	}

	#printDiv h4 {
		text-align: center;
		font-weight: 900;
	}

	.modal-content {
		background-color: #fefefe;
		margin: 10% auto;
		padding: 20px;
		border: 1px solid #888;
		width: fit-content;
	}

	.card {
		border-radius: 25px;
		background: rgba(255, 255, 255, 0.2);
		border-radius: 16px;
		box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
		backdrop-filter: blur(1.2px);
		-webkit-backdrop-filter: blur(1.2px);
		border: 1px solid rgba(255, 255, 255, 1);
		max-height: 90vh;
		overflow-y: auto;
	}

	.m-btn {
		color: white;
		background-color: #1592d1;
		border-radius: 15px;
		padding: 15px;
		font-size: 1.3rem;
		min-width: 300px;
		width: 100%;
		margin-bottom: 10px;
	}

	.company {
		display: flex;
		flex-direction: column;
		align-items: center;
		z-index: 99999;
		margin-bottom: 15px;
	}

	#company_image {
		width: 100px;
		height: 100px;
		border-radius: 15px;
	}

	#company_title {
		font-weight: 900;
		font-size: 20px;
		color: #1592d1;
	}

	#ticket {
		display: flex;
		align-items: center;
		flex-direction: column;
	}

	/* A/B Selection Boxes */
	#ab-selection {
		display: flex;
		gap: 15px;
		justify-content: center;
		margin-bottom: 20px;
		padding: 10px;
	}

	.ab-box {
		width: 120px;
		height: 120px;
		border-radius: 15px;
		color: white;
		font-size: 3rem;
		font-weight: 900;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: all 0.3s ease;
		border: 3px solid transparent;
	}

	.ab-box[data-selection="A"] {
		background-color: #28a745;
	}

	.ab-box[data-selection="B"] {
		background-color: #dc3545;
	}

	.ab-box:hover {
		transform: scale(1.05);
		opacity: 0.9;
	}

	.ab-box[data-selection="A"]:hover {
		box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
	}

	.ab-box[data-selection="B"]:hover {
		box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
	}

	.ab-box.selected {
		border: 3px solid #fff;
		box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
	}

	/* Horizontal button container */
	#t-container,
	#x-container {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
		justify-content: center;
		gap: 15px;
		width: 100%;
	}

	#t-container .m-btn,
	#x-container .transaction-x {
		min-width: 250px;
		max-width: 300px;
		flex: 0 1 auto;
	}
</style>

<a href="index.php" class="btn btn-sm btn-success" style="background: #1592d1; color: #fff;"><i class="fa fa-home"></i> Home</a>
<div class="close-btn">
	<button id="close-btn">x</button>
</div>

<div class="left-side">
	<div class="col-md-10">
		<div class="card">
			<div class="card-body">
				<div class="container-fluid">
					<div class="form-group text-center">
						<div class="company">
							<img src="<?php echo isset($_SESSION['setting_image']) ? 'admin/assets/img/' . $_SESSION['setting_image'] : 'admin/assets/img/logo.jpg' ?>" alt="" id="company_image">
							<p id="company_title"><?php echo isset($_SESSION['setting_name']) ?  $_SESSION['setting_name'] : 'Transaction Queuing System' ?></p>
						</div>

						<!-- A/B Selection -->
						<div id="ab-selection" style="display: none;">
							<div class="ab-box" data-selection="B" style="background-color: #dc3545 !important; color: white !important;">B</div>
							<div class="ab-box" data-selection="A" style="background-color: #28a745 !important; color: white !important;">A</div>
						</div>

						<div id="t-container">
							<?php
							$trans = $conn->query("SELECT * FROM transactions where status = 1 AND active = 'on' order by name asc");
							while ($row = $trans->fetch_assoc()) :
							?>
								<button type="button" class="btn m-btn transaction-c"
									data-tid="<?= $row['id'] ?>"
									data-symbol="<?= $row['symbol'] ?>"
									data-numberfrom="<?php echo $row['numberFrom'] ?>"
									data-numberto="<?php echo $row['numberTo'] ?>"
									data-show-ab="<?php echo isset($row['show_ab_selection']) ? $row['show_ab_selection'] : 'off' ?>"><?= $row['name'] ?></button>
							<?php endwhile; ?>
						</div>

						<div id="x-container" style="display: none;">
							<?php
							$trans = $conn->query("SELECT * FROM transaction_windows where status = 1 order by name asc");
							while ($row = $trans->fetch_assoc()) :
							?>
								<button type="button" class="btn transaction-x" data-tid="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></button>
							<?php endwhile; ?>
						</div>
					</div>
					<div id="printDiv" style="display: none;"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="admin/assets/js/printThis/printThis.js"></script>
<script>
	var selectedAB = null;
	var currentTransactionData = null;

	$('#close-btn').click(function() {
		uni_modal('Exit App', 'exit_dialog.php')
	})

	// Handle A/B box selection
	$('.ab-box').click(function() {
		$('.ab-box').removeClass('selected');
		$(this).addClass('selected');
		selectedAB = $(this).data('selection');

		// Proceed with queue registration
		if (currentTransactionData) {
			processQueue(currentTransactionData);
		}
	});

	// Transaction button click - show A/B selection if enabled
	$('.m-btn').click(function(e) {
		e.preventDefault();
		var button = $(this);

		currentTransactionData = {
			transaction_id: button.data('tid'),
			transaction_symbol: button.data('symbol'),
			transaction_numberfrom: button.data('numberfrom'),
			transaction_numberto: button.data('numberto'),
		};

		// Use the raw data attribute here to avoid jQuery's camelCase conversion
		if (button.attr('data-show-ab') == 'on') {
			// Show A/B selection boxes
			$('#ab-selection').slideDown();
			$('.ab-box').removeClass('selected');
			selectedAB = null;
		} else {
			// If A/B selection is disabled, process queue directly
			processQueue(currentTransactionData);
		}
	});

	// Process queue with selected A/B option
	function processQueue(data) {
		// Only check for A/B selection if the UI is visible
		if ($('#ab-selection').is(':visible') && !selectedAB) {
			alert_toast("Please select A or B", 'warning');
			return;
		}

		// Add the A/B selection to the data if it was selected
		data.selection = selectedAB || '';

		$.ajax({
			url: 'admin/ajax.php?action=save_queue',
			method: 'POST',
			data: data,
			error: function(err) {
				console.log(err);
				alert_toast("An error occurred", 'danger');
				end_load();
			},
			success: function(resp) {
				console.log(resp);
				if (resp > 0) {
					end_load();
					alert_toast("Queue Registered Successfully (Selection: " + selectedAB + ")", 'success');
					PrintDiv(resp);

					// Hide A/B selection and reset
					$('#ab-selection').slideUp();
					selectedAB = null;
					currentTransactionData = null;
				}
			}
		});
	}

	// Get trans sound
	$(document).ready(function() {
		$('.transaction-x').each(function() {
			var card = $(this);
			var tid = card.data('tid');

			var previousResponse = {
				status: '',
				data: {
					queue_no: '',
					date_created: '',
					recall: ''
				}
			};

			var renderServe = setInterval(function() {
				$.ajax({
					url: 'admin/ajax.php?action=get_queue_sound',
					method: "POST",
					data: {
						id: tid
					},
					success: function(resp) {
						try {
							parsedResp = JSON.parse(resp);
						} catch (error) {
							return;
						}
						resp = JSON.parse(resp);
						if (resp.status == 1) {
							if (
								(resp.data.queue_no !== previousResponse.data.queue_no &&
									resp.data.date_created !== previousResponse.data.date_created) || resp.data.recall !== previousResponse.data.recall
							) {
								let start = 'البطاقة رقم ';
								let symbol = resp.data.tsymbol;
								let num = resp.data.queue_no;
								let to = ' إلى ';
								let wnum = resp.data.wname;
								let str = start + symbol + ' ' + num;
								fetch('tts/tts.php', {
										method: 'POST',
										headers: {
											'Content-Type': 'application/x-www-form-urlencoded'
										},
										body: 'text=' + encodeURIComponent(str)
									})
									.catch(error => {
										console.error('AJAX request failed:', error);
									});
							}
							previousResponse = resp;
						}
					}
				});
			}, 2000);
		});
	});

	function PrintDiv(res) {
		$.ajax({
			url: 'queue_print.php',
			method: 'POST',
			data: {
				id: res
			},
			success: function(response) {
				document.getElementById("printDiv").innerHTML = response;
				$('#printDiv').printThis({
					importCSS: true,
					loadCSS: "",
					removeInline: true,
				});
			},
			error: function(error) {
				console.log(error);
			}
		});
	}
</script>
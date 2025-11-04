<style>
	body {
		background-color: #fff;
	}

	.left-side {
		position: absolute;
		width: 100%;
		height: calc(100%);
		left: 0;
		top: 0;
		background: #ffffffc7;
		display: flex;
		justify-content: center;
		align-items: center;
		background: url(admin/assets/img/green-engraving.png);
		background-repeat: no-repeat;

		background-size: cover;
	}




	a.btn.btn-sm.btn-success {
		z-index: 99999;
		position: fixed;
		left: 1rem;
	}

	.card {
		background-color: transparent;
		background-clip: border-box;
		border: none;
		border-radius: 0;
		align-items: center;
	}

	.company {
		background-color: #fff;
		margin-bottom: 50px;
	}

	#company_image {
		width: 150px;
		height: 150px;
	}

	#sec-name {
		color: #fff;
		font-size: 40px;
		font-weight: 900;
		text-shadow: -3px 3px black;
		width: fit-content;
		min-width: 200px;
		min-height: 80px;
		background-color: #0022b0;
		text-align: center;
		padding: 10px 30px;
		border-radius: 10px 0 10px 0;
	}

	#squeue {
		color: #fff;
		font-size: 100px;
		font-weight: 900;
		text-shadow: -3px 3px black;
		width: fit-content;
		min-width: 300px;
		min-height: 80px;
		background-color: #1592d1;
		text-align: center;
		padding: 10px 50px;
		border-radius: 10px 0 10px 0;
	}
</style>
<?php include "admin/db_connect.php" ?>
<?php
$tname = $conn->query("SELECT * FROM transaction_windows where id =" . $_GET['id'])->fetch_array()['name'];
function nserving()
{
	include "admin/db_connect.php";

	$query = $conn->query("SELECT q.*,t.name as wname FROM queue_list q inner join transaction_windows t on t.id = q.window_id where date(q.date_created) = '" . date('Y-m-d') . "' and q.transaction_id = '" . $_GET['id'] . "' and q.status = 1 order by q.id desc limit 1  ");
	if ($query->num_rows > 0) {
		foreach ($query->fetch_array() as $key => $value) {
			if (!is_numeric($key))
				$data[$key] = $value;
		}
		return json_encode(array('status' => 1, "data" => $data));
	} else {
		return json_encode(array('status' => 0));
	}
	$conn->close();
}
?>
<a href="index.php" class="btn btn-sm btn-success" style="background: #1592d1; color: #fff;"><i class="fa fa-home"></i> Home</a>
<div class="left-side">
	<div class="col-md-10">

		<div class="card">
			<div class="company">
				<img src="<?php echo isset($_SESSION['setting_image']) ? 'admin/assets/img/' . $_SESSION['setting_image'] : 'admin/assets/img/logo.jpg' ?>" alt="" id="company_image">
			</div>
			<p id="sec-name"><?php echo strtoupper($tname) ?></p>
			<p id="squeue"></p>
		</div>


	</div>
</div>

<script>
	$(document).ready(function() {
		var queue = '';
		var renderServe = setInterval(function() {
			$.ajax({
				url: 'admin/ajax.php?action=get_window_queue',
				method: "POST",
				data: {
					id: '<?php echo $_GET['id'] ?>'
				},
				success: function(resp) {
					resp = JSON.parse(resp)
					if (resp.status == 1) {
						$('#squeue').html(resp.data.tsymbol + resp.data.queue_no)
					}
				}
			})

		}, 1500);
		var renderTranss = setInterval(function() {
			location.reload()
		}, 60000);
	})
</script>
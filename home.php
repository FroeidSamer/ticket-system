<?php include 'admin/db_connect.php' ?>
<style>
	body {
		background: url(admin/assets/img/main-bg.jpg);
		background-repeat: no-repeat;
		background-size: cover;
	}

	.card {
		border-radius: 1.25rem;
	}

	.first-card {
		background-color: #1592d1;
	}

	.second-card {
		border-radius: 25px;
		background: rgba(255, 255, 255, 0.2);
		box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
		backdrop-filter: blur(1.2px);
		-webkit-backdrop-filter: blur(1.2px);
		border: 1px solid rgba(255, 255, 255, 1);
	}

	.m-btn {
		background-color: darkslategray;
		color: #fff;
		padding: 10px;
		border-radius: 25px;
	}
</style>
<div class="container">
	<div class="col-lg-12">
		<div class="card first-card">
			<div class="card-body">
				<h3 class="text-center text-white"><b><?= isset($_SESSION['setting_name']) ? $_SESSION['setting_name'] : 'Transaction Queuing System'  ?></b> <a href="./admin/login.php"><button class="btn btn btn-secondary btn-sm" style="background: #2f4f4f; color: #fff;"><?= tr('login') ?></button></a></h3>
			</div>
		</div>
		<div class=" card second-card mt-4">
			<div class="card-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<a href="index.php?page=queue_registration" class="btn btn m-btn  btn-primary btn-sm col-md-4 float-right"><?= tr('registerQueue') ?> <i class="fa fa-angle-right">
								</i></a>
						</div>
					</div>
					<hr>
					<h4 class="text-center"><?= tr('selectDisplay') ?></h4>
					<hr class="divider">
					<div class="row">
						<div class="col-md-4 mt-4">
							<a href="index.php?page=all" class="btn btn btn-primary m-btn  btn-block "><?= tr('all') ?> <i class="fa fa-angle-right">
								</i></a>
						</div>
						<div class="col-md-4 mt-4">
							<a href="index.php?page=waiting" class="btn btn btn-primary m-btn  btn-block "><?= tr('waiting') ?> <i class="fa fa-angle-right">
								</i></a>
						</div>
						<?php
						$trans = $conn->query("SELECT * FROM transaction_windows where status = 1 order by transaction_id asc");
						while ($row = $trans->fetch_assoc()) :
						?>
							<div class="col-md-4 mt-4">
								<a href="index.php?page=display&id=<?php echo $row['id'] ?>" class="btn btn m-btn btn-primary btn-block "><?php echo ucwords($row['name']); ?> <i class="fa fa-angle-right">
									</i></a>
							</div>
						<?php endwhile; ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
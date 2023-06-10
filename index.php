<?php
require('top.inc.php');

isAdmin();
?>
<div class="content pb-0">
	<div class="orders">
		<div class="row">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-body">
						<h4 class="box-title">DASHBOARD </h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="container">
	<div class="row">
		<div class="col-6">
			<?php require('service/charts/chart1/canvas.php'); ?>
		</div>
		<div class="col-6">
			<?php require('service/charts/chart2/canvas.php'); ?>
		</div>
	</div>
</div>


<?php
require('footer.inc.php');
?>
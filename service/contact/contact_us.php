<?php
ob_start();
require('../../top.inc.php');
isAdmin();
if (isset($_GET['id']) && isset($_GET['table'])) {
	$id = $_GET['id'];
	$table = $_GET['table'];

	// Validate and sanitize the table name to prevent SQL injection
	$allowedTables = ['contact_us'];
	if (in_array($table, $allowedTables)) {
		// Perform the deletion query

		$query = "DELETE FROM $table WHERE id = '$id'";

		$result = mysqli_query($con, $query);

		if ($result) {
			// Deletion was successful, redirect back to the current page
			header("Location: contact_us.php");
			exit();
		} else {
			// Deletion failed
			echo "Error deleting the row: " . mysqli_error($con);
		}
	} else {
		// Invalid table name
		echo "Invalid table name.";
	}
}

$sql = "select * from contact_us order by id asc";
$res = mysqli_query($con, $sql);
?>
<div class="content pb-0" style="position: relative; min-height: 77vh; overflow-y: auto;">
	<div class="orders">
		<div class="row">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-body">
						<h4 class="box-title">CONTACT US </h4>
					</div>
					<div class="card-body--">
						<div class="table-stats order-table ov-h">
							<table class="table ">
								<thead>
									<tr>
										<th class="serial">#</th>
										<th>Name</th>
										<th>Email</th>
										<th>Mobile NO.</th>
										<th>Title</th>
										<th>Message</th>
										<th>Date</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 1;
									while ($row = mysqli_fetch_assoc($res)) { ?>
										<tr>
											<td class="serial"><?php echo $i ?></td>
											<td><?php echo $row['name'] ?></td>
											<td><?php echo $row['email'] ?></td>
											<td><?php echo $row['mobile'] ?></td>
											<td><?php echo $row['comment'] ?></td>
											<td><?php echo $row['subject'] ?></td>
											<td><?php echo $row['added_on'] ?></td>
											<td>
												<?php
												echo "<span class='badge badge-delete'><a href='contact_us.php?table=contact_us&id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></span>";
												?>
											</td>
										</tr>
									<?php $i++;
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
require('../../footer.inc.php');
ob_end_flush();
?>
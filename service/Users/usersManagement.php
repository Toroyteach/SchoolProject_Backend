<?php
require('../../top.inc.php');
isAdmin();
if (isset($_GET['type']) && $_GET['type'] != '') {
	$type = get_safe_value($con, $_GET['type']);
	if ($type == 'status') {
		$operation = get_safe_value($con, $_GET['operation']);
		$id = get_safe_value($con, $_GET['id']);
		if ($operation == 'active') {
			$status = '1';
		} else {
			$status = '0';
		}
		$update_status_sql = "update tbl_member set status='$status' where id='$id'";
		mysqli_query($con, $update_status_sql);

		header("Location: usersManagement.php");
		exit();
	}

	if ($type == 'delete') {

		$id = get_safe_value($con, $_GET['id']);
		$delete_sql = "delete from tbl_member where id='$id'";
		mysqli_query($con, $delete_sql);

		header("Location: usersManagement.php");
		exit();
	}
}

$sql = "select * from tbl_member order by created_at desc";
$res = mysqli_query($con, $sql);

?>

<div class="content pb-0" style="position: relative; min-height: 77vh;">
	<div class="orders">
		<div class="row">
			<div class="col-xl-12">
				<div class="card" style="position:relative; max-height: 60vh; overflow-y: auto;">
					<div class="card-body">
						<h4 class="box-title">Manage Users </h4>
						<!-- <h4 class="box-link"><a href="Users.php" class="btn btn-success">ADD User</a> </h4> -->
					</div>
					<div class="card-body--">
						<div class="table-stats order-table ov-h">
							<table class="table ">
								<thead>
									<tr>
										<th class="serial">#</th>
										<th width="10%">User ID</th>
										<th width="15%">Username</th>
										<th width="20%">Password</th>
										<th width="20%">Email</th>
										<th width="10%">Phone Number</th>
										<th width="26%"></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 1;
									while ($row = mysqli_fetch_assoc($res)) { ?>
										<tr>
											<td class="serial"><?php echo $i ?></td>
											<td><?php echo $row['id'] ?></td>
											<td><?php echo $row['username'] ?></td>
											<td><?php echo $row['password'] ?></td>
											<td><?php echo $row['email'] ?></td>
											<td><?php echo $row['phone'] ?></td>

											<td>
												<?php
												if ($row['status'] == 1) {
													echo "<span class='badge badge-complete'><a href='?type=status&operation=deactive&id=" . $row['id'] . "'>Active</a></span>&nbsp;";
												} else {
													echo "<span class='badge badge-pending'><a href='?type=status&operation=active&id=" . $row['id'] . "'>In-Active</a></span>&nbsp;";
												}
												echo "<span class='badge badge-edit'><a href='Users.php?id=" . $row['id'] . "'>Edit</a></span>&nbsp;";

												echo "<span class='badge badge-delete'><a href='?type=delete&id=" . $row['id'] . "'onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></span>";

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
?>
<?php
require('../../top.inc.php');
isAdmin();
$username = '';
$password = '';
$email = '';
$phone = '';

$dateTime = new DateTime();
$date = $dateTime->format('Y-m-d H:i:s');

$msg = '';
if (isset($_GET['id']) && $_GET['id'] != '') {
	$image_required = '';
	$id = get_safe_value($con, $_GET['id']);
	$res = mysqli_query($con, "SELECT * FROM tbl_member WHERE id='$id'");
	$check = mysqli_num_rows($res);
	if ($check > 0) {
		$row = mysqli_fetch_assoc($res);
		$username = $row['username'];
		$email = $row['email'];
		$phone = $row['phone'];
		$password = $row['password'];
	} else {
		header('location:usersManagement.php');
		die();
	}
}

if (isset($_POST['submit'])) {
	$username = get_safe_value($con, $_POST['username']);
	$email = get_safe_value($con, $_POST['email']);
	$phone = get_safe_value($con, $_POST['phone']);
	$password = get_safe_value($con, md5($_POST['password']));

	$res = mysqli_query($con, "SELECT * FROM tbl_member WHERE username='$username' OR phone='$phone'");
	$check = mysqli_num_rows($res);

	if ($check > 0) {

		if (isset($_GET['id']) && $_GET['id'] != '') {
			$getData = mysqli_fetch_assoc($res);
			if ($id == $getData['id']) {
			} else {
				$msg = "User Data already exist";
			}
		} else {
			$msg = "User Data already exist";
		}
	}


	if ($msg == '') {
		if (isset($_GET['id']) && $_GET['id'] != '') {
			$update_sql = "UPDATE tbl_member SET username='$username',email='$email',password='$password',phone='$phone' WHERE id='$id'";
			mysqli_query($con, $update_sql);
		} else {
			mysqli_query($con, "INSERT INTO tbl_member( username, password, email, phone, created_at) VALUES ('$username','$password','$email','$phone', '$date')");
		}

		header('location:usersManagement.php');
		die();
	}
}
?>
<div class="content pb-0">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header"><strong>MANAGE USER</strong><small> </small></div>
					<form method="post" enctype="multipart/form-data">
						<div class="card-body card-block">


							<div class="form-group">
								<label for="username" class=" form-control-label">Username</label>
								<input type="text" name="username" placeholder="Enter username" class="form-control" required value="<?php echo $username ?>">
							</div>
							<div class="form-group">
								<label for="password" class=" form-control-label">Password</label>
								<input type="password" name="password" placeholder="Enter password" class="form-control">
							</div>

							<div class="form-group">
								<label for="password" class=" form-control-label">Email</label>
								<input type="email" name="email" placeholder="Enter email" class="form-control" required value="<?php echo $email ?>">
							</div>
							<div class="form-group">
								<label for="categories" class=" form-control-label">Mobile</label>
								<input type="text" name="phone" placeholder="Enter Phone Number" class="form-control" required value="<?php echo $phone ?>">
							</div>


							<button id="payment-button" name="submit" type="submit" class="btn btn-lg btn-info btn-block">
								<span id="payment-button-amount">SUBMIT</span>
							</button>
							<div class="field_error"><?php echo $msg ?></div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>



<?php
require('../../footer.inc.php');
?>
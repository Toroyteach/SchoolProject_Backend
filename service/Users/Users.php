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

if (isset($_POST['submitTest'])) {
	$testMessage = get_safe_value($con, $_POST['testMessage']);

	
}
?>
<div class="content pb-0" style="position: relative; max-height: 77vh; overflow-y: auto;">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-7">
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


							<button id="payment-button" name="submit" type="submit" class="btn btn-lg btn-info">
								<span id="payment-button-amount">SUBMIT</span>
							</button>
							<div class="field_error"><?php echo $msg ?></div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-5">
				<div class="card">
					<div class="card-header"><strong>Test User Device Push Response</strong><small> </small></div>
					<form method="post">
						<div class="card-body card-block">


							<div class="form-group">
								<label for="username" class=" form-control-label">Type Message</label>
								<textarea name="testMessage" cols="40" rows="4" required></textarea>
							</div>

							<button id="test-button" name="submitTest" type="submit" class="btn btn-lg btn-info">
								<span id="payment-button-amount">Send</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="container mt-5">
		<h2> User Preferences</h2>
		<hr>

		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#locations">Location</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#currentData">Options Selected</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#dailyData">Notifications Sent</a>
			</li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content mt-3" style="position:relative; max-height: 50vh; overflow-y: auto;">
			<div class="tab-pane container active" id="locations">
				<?php
				// Fetch and display user locations from the database
				$id = get_safe_value($con, $_GET['id']);
				$stmt = $con->prepare("SELECT tbl_location.*, tbl_member.username FROM tbl_location INNER JOIN tbl_member ON tbl_member.id = tbl_location.member_id WHERE member_id = ?");
				$stmt->bind_param("i", $id);
				$stmt->execute();
				$result = $stmt->get_result();
				$locations = $result->fetch_all(MYSQLI_ASSOC);


				if ($result->num_rows > 0) {
					echo "<table class='table table-bordered'>";
					echo "<thead><tr><th>Id</th><th>Location</th><th>Username</th><th>Longitude</th><th>Latitude</th><th>Created</th></tr></thead>";
					echo "<tbody>";
					$id = 1;
					foreach ($locations as $location) {
						$locationId = $location['location_id'];
						$locationName = $location['location'];
						$username = $location['username'];
						$longitude = $location['longitude'];
						$latitude = $location['latitude'];
						$createdDate = $location['created_at'];
						$dataId = $location['location_id'];

						echo "<tr>";
						echo "<td>$id</td>";
						echo "<td>$locationName</td>";
						echo "<td>$username</td>";
						echo "<td>$longitude</td>";
						echo "<td>$latitude</td>";
						echo "<td>$createdDate</td>";
						echo "</tr>";

						$id++;
					}
					echo "</tbody></table>";
				} else {
					echo "<p>No locations to show.</p>";
				}
				?>
			</div>
			<div class="tab-pane container fade" id="currentData">
				<?php
				// Fetch and display weather data from the database
				$id = get_safe_value($con, $_GET['id']);
				$stmt = $con->prepare("SELECT tbl_user_push_preferences.*, tbl_push_options.option_name FROM tbl_user_push_preferences INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_user_push_preferences.option_id WHERE user_id = ? ORDER BY tbl_user_push_preferences.created_at DESC");
				$stmt->bind_param("i", $id);
				$stmt->execute();
				$result = $stmt->get_result();
				$preferenceData = $result->fetch_all(MYSQLI_ASSOC);

				if ($result->num_rows > 0) {
					echo "<table class='table table-bordered'>";
					echo "<thead><tr><th>Id</th><th>Alert Option</th><th>Min Value</th><th>Max Value</th><th>User Message</th></tr></thead>";
					echo "<tbody>";
					$id = 1;
					foreach ($preferenceData as $data) {
						$alertOption = $data['option_name'];
						$minValue = $data['min_value'];
						$maxValue = $data['max_value'];
						$userMessage = $data['user_message'];

						echo "<tr>";
						echo "<td>$id</td>";
						echo "<td>$alertOption</td>";
						echo "<td>$maxValue</td>";
						echo "<td>$minValue</td>";
						echo "<td>$userMessage</td>";
						echo "</tr>";

						$id++;
					}
					echo "</tbody></table>";
				} else {
					echo "<p>No Current Weather Data to Show.</p>";
				}
				?>
			</div>
			<div class="tab-pane container fade" id="dailyData">
				<?php
				// Fetch and display weather data from the database
				$id = get_safe_value($con, $_GET['id']);
				$stmt = $con->prepare("SELECT tbl_firebase_notification_history.*, tbl_push_options.option_name, tbl_user_push_preferences.user_message FROM tbl_firebase_notification_history INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_firebase_notification_history.option_id INNER JOIN tbl_user_push_preferences ON tbl_user_push_preferences.id = tbl_firebase_notification_history.preference_id WHERE member_id = ? ORDER BY tbl_firebase_notification_history.created_at DESC");
				$stmt->bind_param("i", $id);
				$stmt->execute();
				$result = $stmt->get_result();
				$weatherData = $result->fetch_all(MYSQLI_ASSOC);

				if ($result->num_rows > 0) {
					echo "<table class='table table-bordered'>";
					echo "<thead><tr><th>Id</th><th>Alert Option</th><th>Date Sent</th><th>Read At</th><th>User Message</th></tr></thead>";
					echo "<tbody>";
					$id = 1;
					foreach ($weatherData as $data) {
						$alertOption = $data['option_name'];
						$dateSent = $data['notification_timestamp'];
						$readAt = $data['read_at'];
						$usermessage = $data['user_message'];

						echo "<tr>";
						echo "<td>$id</td>";
						echo "<td>$alertOption</td>";
						echo "<td>$dateSent</td>";
						echo "<td>$readAt</td>";
						echo "<td>$usermessage</td>";
						echo "</tr>";


						$id++;
					}
					echo "</tbody></table>";
				} else {
					echo "<p>No Daily Weather Data to Show.</p>";
				}
				?>
			</div>
		</div>


	</div>
</div>



<?php
require('../../footer.inc.php');
?>
<?php

require('../../top.inc.php');
isAdmin();

// Handle the Delete
if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = $_GET['table'];

    // Validate and sanitize the table name to prevent SQL injection
    $allowedTables = ['tbl_push_options', 'tbl_user_push_preferences', 'tbl_firebase_notification_history'];
    if (in_array($table, $allowedTables)) {
        // Perform the deletion query

        if ($table == 'tbl_push_options') {

            $query = "DELETE FROM $table WHERE option_id = '$id'";
        } elseif ($table == 'tbl_user_push_preferences') {

            $query = "DELETE FROM $table WHERE id = '$id'";
        } elseif ($table == 'tbl_firebase_notification_history') {

            $query = "DELETE FROM $table WHERE notification_id = '$id'";
        }

        $result = mysqli_query($con, $query);

        if ($result) {
            // Deletion was successful, redirect back to the current page
            header("Location: pushNotificationManagement.php");
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
?>

<div class="container mt-5" style="position: relative; top: 50px; min-height: 77vh; overflow-y: auto;">
    <h2>Push Notification Management</h2>
    <hr>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#pushNotification">Push Notification Options</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#memberPushNotification">Member Push Notification Request</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#pushNotificationHistoru">Push Sent Notification History</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content mt-3">
        <div class="tab-pane container active" id="pushNotification">
            <h4 class="box-link"><a href="createPushNotifications.php" class="btn btn-success">Add Push Options</a> </h4>
            <?php
            // Fetch and display user locations from the database
            $stmt = $con->prepare("SELECT * FROM tbl_push_options");
            $stmt->execute();
            $result = $stmt->get_result();
            $pushOption = $result->fetch_all(MYSQLI_ASSOC);

            if (count($pushOption) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Id</th><th>Option Name</th><th>Created</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                $id = 1;
                foreach ($pushOption as $option) {
                    $optionName = $option['option_name'];
                    $createdDate = $option['created_at'];
                    $dataId = $option['option_id'];

                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$optionName</td>";
                    echo "<td>$createdDate</td>";
                    echo "<td><a href='pushNotificationManagement.php?table=tbl_push_options&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                    echo "</tr>";

                    $id++;
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No Notifications Options to show.</p>";
            }
            ?>
        </div>
        <div class="tab-pane container fade" id="memberPushNotification">
            <?php
            // Fetch and display user locations from the database
            $stmt = $con->prepare("SELECT tbl_user_push_preferences.*, tbl_member.username, tbl_push_options.option_name FROM tbl_user_push_preferences INNER JOIN tbl_member ON tbl_member.id = tbl_user_push_preferences.user_id INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_user_push_preferences.option_id");
            $stmt->execute();
            $result = $stmt->get_result();
            $pushOption = $result->fetch_all(MYSQLI_ASSOC);

            if (count($pushOption) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Id</th><th>Username</th><th>Option Name</th><th>Min Value</th><th>Max Value</th><th>Created</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                $id = 1;
                foreach ($pushOption as $option) {
                    $optionName = $option['option_name'];
                    $username = $option['username'];
                    $minValue = $option['min_value'];
                    $maxValue = $option['max_value'];
                    $username = $option['username'];
                    $createdDate = $option['created_at'];
                    $dataId = $option['id'];

                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$username</td>";
                    echo "<td>$optionName</td>";
                    echo "<td>$minValue</td>";
                    echo "<td>$maxValue</td>";
                    echo "<td>$createdDate</td>";
                    echo "<td><a href='pushNotificationManagement.php?table=tbl_user_push_preferences&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                    echo "</tr>";

                    $id++;
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No Member Preference to show.</p>";
            }
            ?>
        </div>
        <div class="tab-pane container fade" id="pushNotificationHistoru">
            <?php
            // Fetch and display user locations from the database
            $stmt = $con->prepare("SELECT tbl_firebase_notification_history.*, tbl_member.username, tbl_push_options.option_name FROM tbl_firebase_notification_history INNER JOIN tbl_member ON tbl_member.id = tbl_firebase_notification_history.member_id INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_firebase_notification_history.option_id");
            $stmt->execute();
            $result = $stmt->get_result();
            $pushHistoryOption = $result->fetch_all(MYSQLI_ASSOC);

            if (count($pushHistoryOption) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Id</th><th>Username</th><th>Option Name</th><th>Notification Time</th><th>Created</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                $id = 1;
                foreach ($pushHistoryOption as $option) {

                    $username = $option['username'];
                    $optionN = $option['option_name'];
                    $notificationStamp = $option["notification_timestamp"];
                    $createdDate = $option['created_at'];
                    $dataId = $option['notification_id'];

                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$username</td>";
                    echo "<td>$optionN</td>";
                    echo "<td>$notificationStamp</td>";
                    echo "<td>$createdDate</td>";
                    echo "<td><a href='pushNotificationManagement.php?table=tbl_firebase_notification_history&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                    echo "</tr>";

                    $id++;
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No Push Notification History to Show.</p>";
            }
            ?>
        </div>
    </div>

</div>

<?php
require('../../footer.inc.php');
?>
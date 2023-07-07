<?php
require('../../top.inc.php');
isAdmin();

$dateTime = new DateTime();
$date = $dateTime->format('Y-m-d H:i:s');

$msg = '';

if (isset($_POST['submitPushOption'])) {
    $optionName = get_safe_value($con, $_POST['option_name']);

    if (mysqli_query($con, "INSERT INTO tbl_push_options( option_name, created_at) VALUES ('$optionName','$date')")) {

        header('location: createPushNotifications.php');
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
}

if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = $_GET['table'];

    // Validate and sanitize the table name to prevent SQL injection
    $allowedTables = ['tbl_push_options'];
    if (in_array($table, $allowedTables)) {
        // Perform the deletion query

        if ($table == 'tbl_push_options') {

            $query = "DELETE FROM $table WHERE option_id = '$id'";
        }

        $result = mysqli_query($con, $query);

        if ($result) {
            // Deletion was successful, redirect back to the current page
            header("Location: createPushNotifications.php");
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
<div class="content pb-0" style="position: relative; max-height: 77vh; overflow-y: auto;">
    <div class="animated fadeIn">
        <div class="card-header"><strong>CREATE PUSH OPTIONS</strong><small> </small></div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <form method="post">
                        <div class="card-body card-block">

                            <div class="form-group">
                                <label for="category" class=" form-control-label">Option Name</label>
                                <input type="text" name="option_name" id="option_name" placeholder="Enter Option Name" class="form-control" required>
                            </div>

                            <button id="payment-button" name="submitPushOption" type="submit" class="btn btn-lg btn-info">
                                <span id="payment-button-amount">SUBMIT</span>
                            </button>
                            <div class="field_error"><?php echo $msg ?></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-6">
                <div class="tab-pane container active" id="pushNotification">
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
                            echo "<td><a href='createPushNotifications.php?table=tbl_push_options&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                            echo "</tr>";

                            $id++;
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No Notifications Options to show.</p>";
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>


</div>
</div>



<?php
require('../../footer.inc.php');
?>
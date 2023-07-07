<?php
ob_start();
require('../../top.inc.php');
isAdmin();

// Handle the Delete
if(isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = $_GET['table'];

    // Validate and sanitize the table name to prevent SQL injection
    $allowedTables = ['tbl_daily_weather_data', 'tbl_hourly_weather_data', 'tbl_current_weather_data', 'tbt_location'];
    if(in_array($table, $allowedTables)) {
        // Perform the deletion query
        
        if($table == 'tbl_daily_weather_data'){

            $query = "DELETE FROM $table WHERE weather_id = '$id'";

        } elseif($table == 'tbl_hourly_weather_data') {

            $query = "DELETE FROM $table WHERE weather_id = '$id'";

        } elseif($table == 'tbl_current_weather_data') {

            $query = "DELETE FROM $table WHERE weather_id = '$id'";

        } elseif($table == 'tbt_location') {

            $query = "DELETE FROM $table WHERE location_id = '$id'";

        }
        
        // exit();
        $result = mysqli_query($con, $query);

        if($result) {
            // Deletion was successful, redirect back to the current page
            header("Location: weatherManagement.php");
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

<div class="container mt-5" style="position: relative; top: 50px; min-height: 77vh;">
    <h2>Weather Data Management</h2>
    <hr>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#locations">Locations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#currentData">Current Weather Data</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#hourlyData">Hourly Weather Data</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#dailyData">Daily Weather Data</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content mt-3" style="position:relative; max-height: 60vh; overflow-y: auto;">
        <div class="tab-pane container active" id="locations">
            <?php
            // Fetch and display user locations from the database
            $stmt = $con->prepare("SELECT tbl_location.*, tbl_member.username FROM tbl_location INNER JOIN tbl_member ON tbl_member.id = tbl_location.member_id");
            $stmt->execute();
            $result = $stmt->get_result();
            $locations = $result->fetch_all(MYSQLI_ASSOC);

            if (count($locations) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Id</th><th>Location</th><th>Username</th><th>Longitude</th><th>Latitude</th><th>Created</th><th>Action</th></tr></thead>";
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
                    echo "<td><a href='weatherManagement.php?table=tbl_location&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
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
            $stmt = $con->prepare("SELECT tbl_current_weather_data.*, tbl_member.username, tbl_location.location FROM tbl_current_weather_data INNER JOIN tbl_member ON tbl_member.id = tbl_current_weather_data.user_id INNER JOIN tbl_location ON tbl_location.location_id = tbl_current_weather_data.location_id");
            $stmt->execute();
            $result = $stmt->get_result();
            $weatherData = $result->fetch_all(MYSQLI_ASSOC);

            if (count($weatherData) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Id</th><th>Location</th><th>Location</th><th>Temperature</th><th>Wind Speed</th><th>Rainfall</th><th>UV Index</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                $id = 1;
                foreach ($weatherData as $data) {
                    $locationName = $data['location'];
                    $username = $data['username'];
                    $temperature = $data['temperature'];
                    $humidity = $data['wind_speed'];
                    $rainfall = $data['rainfall'];
                    $uvIndex = $data['uvi'];
                    $dataId = $data['weather_id'];

                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$locationName</td>";
                    echo "<td>$username</td>";
                    echo "<td>$temperature</td>";
                    echo "<td>$humidity</td>";
                    echo "<td>$rainfall</td>";
                    echo "<td>$uvIndex</td>";
                    echo "<td><a href='weatherManagement.php?table=tbl_current_weather_data&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                    echo "</tr>";

                    $id++;
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No Current Weather Data to Show.</p>";
            }
            ?>
        </div>
        <div class="tab-pane container fade" id="hourlyData">
        <?php
            // Fetch and display weather data from the database
            $stmt = $con->prepare("SELECT tbl_hourly_weather_data.*, tbl_member.username, tbl_location.location FROM tbl_hourly_weather_data INNER JOIN tbl_member ON tbl_member.id = tbl_hourly_weather_data.user_id INNER JOIN tbl_location ON tbl_location.location_id = tbl_hourly_weather_data.location_id");
            $stmt->execute();
            $result = $stmt->get_result();
            $weatherData = $result->fetch_all(MYSQLI_ASSOC);

            if (count($weatherData) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Id</th><th>Location</th><th>Username</th><th>Temperature</th><th>Wind Speed</th><th>Rainfall</th><th>UV Index</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                $id = 1;
                foreach ($weatherData as $data) {
                    $locationName = $data['location'];
                    $username = $data['username'];
                    $temperature = $data['temperature'];
                    $humidity = $data['wind_speed'];
                    $rainfall = $data['rainfall'];
                    $uvIndex = $data['uvi'];
                    $dataId = $data['weather_id'];

                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$locationName</td>";
                    echo "<td>$username</td>";
                    echo "<td>$temperature</td>";
                    echo "<td>$humidity</td>";
                    echo "<td>$rainfall</td>";
                    echo "<td>$uvIndex</td>";
                    echo "<td><a href='weatherManagement.php?table=tbl_hourly_weather_data&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                    echo "</tr>";

                    $id++;
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No Hourly Weather Data to Show.</p>";
            }
            ?>
        </div>
        <div class="tab-pane container fade" id="dailyData">
        <?php
            // Fetch and display weather data from the database
            $stmt = $con->prepare("SELECT tbl_daily_weather_data.*, tbl_member.username, tbl_location.location FROM tbl_daily_weather_data INNER JOIN tbl_member ON tbl_member.id = tbl_daily_weather_data.user_id INNER JOIN tbl_location ON tbl_location.location_id = tbl_daily_weather_data.location_id");
            $stmt->execute();
            $result = $stmt->get_result();
            $weatherData = $result->fetch_all(MYSQLI_ASSOC);

            if (count($weatherData) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Id</th><th>Location</th><th>Temperature</th><th>Wind Speed</th><th>Rainfall</th><th>UV Index</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                $id = 1;
                foreach ($weatherData as $data) {
                    $locationName = $data['location'];
                    $username = $data['username'];
                    $temperature = $data['temperature'];
                    $humidity = $data['wind_speed'];
                    $rainfall = $data['rainfall'];
                    $uvIndex = $data['uvi'];
                    $dataId = $data['weather_id'];

                    echo "<tr>";
                    echo "<td>$id</td>";
                    echo "<td>$locationName</td>";
                    echo "<td>$temperature</td>";
                    echo "<td>$humidity</td>";
                    echo "<td>$rainfall</td>";
                    echo "<td>$uvIndex</td>";
                    echo "<td><a href='weatherManagement.php?table=tbl_daily_weather_data&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
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

<?php
require('../../footer.inc.php');
ob_end_flush();
?>
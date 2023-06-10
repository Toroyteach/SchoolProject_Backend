<?php

//Database connection Data
$servername = "localhost";
$database = "ecom";
$username = "root";
$password = "";


//Api details
$apiKey = "d626ab9680a021943d5dd2f6982aba01";
$apiEndpoint = "https://api.openweathermap.org/data/3.0/onecall";

// Create connection

$con = mysqli_connect($servername, $username, $password, $database);

if (!$con->connect_error) {

    //call the functions here
    fetchAndStoreWeatherData($con);

    mysqli_close($con);
} else {

    die("Connection failed: " . $con->connect_error);
}




function fetchAndStoreWeatherData($con)
{
    // Set up API credentials and endpoint
    global $apiKey, $apiEndpoint;

    try {
        // Fetch user locations from the database
        $stmt = $con->prepare("SELECT * FROM tbl_location");
        $stmt->execute();
        $result = $stmt->get_result();
        $locations = $result->fetch_all(MYSQLI_ASSOC);

        // Iterate over locations and fetch weather data
        foreach ($locations as $location) {
            $memberId = $location["member_id"];
            $locationId = $location["location_id"];

            $lat = $location["latitude"];
            $lon = $location["longitude"];
            $units = "metric";

            // Build the API request URL
            $requestUrl = "$apiEndpoint?lat=" . urlencode($lat) . "&lon=" . urlencode($lon) . "&units=" . urlencode($units) . "&appid=$apiKey";

            // Fetch weather data from the API
            $response = file_get_contents($requestUrl);
            $weatherData = json_decode($response, true);

            // Extract Current weather Data
            $temperature_c = $weatherData["current"]["temp"] ?? null;
            $rainfall_c = $weatherData["current"]["rain"]["1h"] ?? null;
            $uvIndex_c = $weatherData["current"]["uvi"] ?? null;
            $pop_c = null;
            $windSpeed_c = $weatherData["current"]["wind_speed"] ?? null;
            $dateStamp_c = $weatherData["current"]["dt"] ?? null;

            $stmt = $con->prepare("INSERT INTO tbl_current_weather_data (user_id, location_id, temperature, wind_speed, rainfall, uvi, pop, date_stamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssddddds", $memberId, $locationId, $temperature_c, $windSpeed_c, $rainfall_c, $uvIndex_c, $pop_c, $dateStamp_c);
            $stmt->execute();

            //Extract Hourly weather Data
            foreach ($weatherData['hourly'] as $locationH) {
                $temperature_h = $locationH["temp"];
                $rainfall_h = $locationH["rain"]["1h"] ?? null;
                $uvIndex_h = $locationH["uvi"] ?? null;
                $pop_h = $locationH["pop"] ?? null;
                $windSpeed_h = $locationH["wind_speed"] ?? null;
                $timeStamp_h = $locationH["dt"] ?? null;

                $stmt = $con->prepare("INSERT INTO tbl_hourly_weather_data (user_id, location_id, temperature, wind_speed, rainfall, uvi, pop, time_stamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssddddds", $memberId, $locationId, $temperature_h, $windSpeed_h, $rainfall_h, $uvIndex_h, $pop_h, $timeStamp_h);
                $stmt->execute();
            }



            //Extract Daily weather Data
            foreach ($weatherData['daily'] as $locationD) {
                $temperature_d = $locationD["temp"]["day"];
                $rainfall_d = $locationD["rain"] ?? null;
                $uvIndex_d = $locationD["uvi"] ?? null;
                $pop_d = $locationD["pop"] ?? null;
                $windSpeed_d = $locationD["wind_speed"] ?? null;
                $dateStamp_d = $locationD["dt"] ?? null;

                $stmt = $con->prepare("INSERT INTO tbl_daily_weather_data (user_id, location_id, temperature, wind_speed, rainfall, uvi, pop, date_stamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssddddds", $memberId, $locationId, $temperature_d, $windSpeed_d, $rainfall_d, $uvIndex_d, $pop_d, $dateStamp_d);
                $stmt->execute();
            }


            //Extract Weather Alerts if any
            if (array_key_exists('alerts', $weatherData)) {

                $alertName = $weatherData["event"];
                $startTime = $weatherData["start"];
                $endTime = $weatherData["end"];
                $description = $weatherData["description"];

                $stmt = $con->prepare("INSERT INTO tbl_weather_alerts (user_id, location_id, alert_name, start_time, end_time, description) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $memberId, $locationId, $alertName, $startTime, $endTime, $description);
                $stmt->execute();
            }
        }

        echo 'success';

        exit();
    } catch (Exception $e) {
        // Handle any errors and display appropriate error message
        die("Error: " . $e->getMessage());
    }
}
<?php

require('../../top.inc.php');
function fetchAndStoreWeatherData($con)
{
    // Set up API credentials and endpoint
    $apiKey = "d626ab9680a021943d5dd2f6982aba01";
    $apiEndpoint = "https://api.openweathermap.org/data/2.5/weather";

    try {
        // Fetch user locations from the database
        $stmt = $con->prepare("SELECT * FROM tbl_location");
        $stmt->execute();
        $result = $stmt->get_result();
        $locations = $result->fetch_all(MYSQLI_ASSOC);

        // Iterate over locations and fetch weather data
        foreach ($locations as $location) {
            $memberId = $location["member_id"];
            $locationName = $location["location"];
            $locationId = $location["location_id"];

            // Build the API request URL
            $requestUrl = "$apiEndpoint?q=" . urlencode($locationName) . "&appid=$apiKey";

            // Fetch weather data from the API
            $response = file_get_contents($requestUrl);
            $weatherData = json_decode($response, true);

            // Extract relevant data from the API response
            $temperature = $weatherData["main"]["temp"];
            $humidity = $weatherData["main"]["humidity"];
            $rainfall = $weatherData["rain"]["1h"] ?? null;
            $uvIndex = $weatherData["uvIndex"] ?? null;
            $weatherAlert = $weatherData["alerts"][0]["description"] ?? null;
            $soilMoisture = null;// Fetch soil moisture data from your source
            $soilFertility = null;// Fetch soil fertility data from your source

            // Store the extracted data in the database
            $stmt = $con->prepare("INSERT INTO tbl_weather_data (member_id, location_id, temperature, humidity, rainfall, uv_index, weather_alert, soil_moisture, soil_fertility) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$memberId, $locationId, $temperature, $humidity, $rainfall, $uvIndex, $weatherAlert, $soilMoisture, $soilFertility]);
        }

        // Redirect back to the first page after data storage
        header("Location: weatherManagement.php");
        exit();
    } catch (Exception $e) {
        // Handle any errors and display appropriate error message
        die("Error: " . $e->getMessage());
    }
}

// Check if the button is clicked
if (isset($_POST["fetchButton"])) {
    // Call the function to fetch and store weather data
    fetchAndStoreWeatherData($con);
}
?>

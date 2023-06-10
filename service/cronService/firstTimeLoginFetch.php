<?php

$apiKey = "d626ab9680a021943d5dd2f6982aba01";
$apiEndpoint = "https://api.openweathermap.org/data/3.0/onecall";

function fetchAndStoreWeatherDataFirstTimeUser($con, $id)
{
    // Set up API credentials and endpoint
    global $apiKey, $apiEndpoint;

    try {
        // Fetch user locations from the database
        $stmt = $con->prepare("SELECT * FROM tbl_location WHERE location_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $results = $stmt->get_result();

        if ($results->num_rows > 0) {

            $row = $results->fetch_assoc();
    
            // Iterate over locations and fetch weather data
            $memberId = $row["member_id"];
            $locationId = $row["location_id"];
    
            $lat = $row["latitude"];
            $lon = $row["longitude"];
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
            $weatherName = $weatherData["current"]["weather"][0]["main"] ?? null;
            $weatherDesc = $weatherData["current"]["weather"][0]["description"] ?? null;
            $weatherIconc = $weatherData["current"]["weather"][0]["icon"] ?? null;
    
            $stmt = $con->prepare("INSERT INTO tbl_current_weather_data (user_id, location_id, temperature, wind_speed, rainfall, uvi, pop, weather, description, icon, date_stamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdddddssss", $memberId, $locationId, $temperature_c, $windSpeed_c, $rainfall_c, $uvIndex_c, $pop_c ,$weatherName, $weatherDesc, $weatherIconc, $dateStamp_c);
            $stmt->execute();
    
            //Extract Hourly weather Data
            foreach ($weatherData['hourly'] as $locationH) {
                $temperature_h = $locationH["temp"];
                $rainfall_h = $locationH["rain"]["1h"] ?? null;
                $uvIndex_h = $locationH["uvi"] ?? null;
                $pop_h = $locationH["pop"] ?? null;
                $windSpeed_h = $locationH["wind_speed"] ?? null;
                $timeStamp_h = $locationH["dt"] ?? null;
                $weatherName_h = $locationH["weather"][0]["main"] ?? null;
                $weatherDesc_h = $locationH["weather"][0]["description"] ?? null;
                $weatherIcon_h = $locationH["weather"][0]["icon"] ?? null;
    
                $stmt = $con->prepare("INSERT INTO tbl_hourly_weather_data (user_id, location_id, temperature, wind_speed, rainfall, uvi, pop, weather, description, icon, time_stamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdddddssss", $memberId, $locationId, $temperature_h, $windSpeed_h, $rainfall_h, $uvIndex_h, $pop_h, $weatherName_h, $weatherDesc_h, $weatherIcon_h, $timeStamp_h);
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
                $weatherName_d = $locationD["weather"][0]["main"] ?? null;
                $weatherDesc_d = $locationD["weather"][0]["description"] ?? null;
                $weatherIcon_d = $locationD["weather"][0]["icon"] ?? null;
    
                $stmt = $con->prepare("INSERT INTO tbl_daily_weather_data (user_id, location_id, temperature, wind_speed, rainfall, uvi, pop, weather, description, icon, date_stamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdddddssss", $memberId, $locationId, $temperature_d, $windSpeed_d, $rainfall_d, $uvIndex_d, $pop_d, $weatherName_d, $weatherDesc_d, $weatherIcon_d, $dateStamp_d);
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

        $stmt->close();
        return;
    } catch (Exception $e) {
        // Handle any errors and display appropriate error message
        die("Error: " . $e->getMessage());
    }
}


//Get the users town name from another source if GPS fails
function get_town_name($lat, $lng)
{
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng&zoom=18&addressdetails=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['address']['town'])) {
        return $result['address']['town'];
    } elseif (isset($result['address']['city'])) {
        return $result['address']['city'];
    } else {
        return '';
    }
}

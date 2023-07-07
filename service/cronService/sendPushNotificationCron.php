<?php

//Database connection Data
$servername = "localhost";
$database = "ecom";
$username = "root";
$password = "";


//Api details
$serverFirebaseKey = "AAAA4tWhEuA:APA91bFTORyDHEtSO-PUp8jKrMkzFED11HfcXvHGQUhkhzRuxkraClGUu1ylbMiDvGqCfkuwVMKKjUYgE0QXW0MmWCV0zkIZ5T8_S35ztSWPwhIuqhaJUXfWrCITdk-q5u4pBe4D2ahf";

// Create connection

$con = mysqli_connect($servername, $username, $password, $database);
//$con = mysqli_connect("localhost", "toroytea_admin", "gV1LXV+EXW}$", "toroytea_ecom");

if (!$con->connect_error) {

    //call the functions here
    fetchDatatoSendPush();

    mysqli_close($con);
} else {

    die("Connection failed: " . $con->connect_error);
}


function fetchDatatoSendPush()
{
    try {


        $userData = getMembersCron();

        if (!empty($userData)) {

            foreach ($userData as $dataItem) {


                $dataObj = array(
                    "device_token" => $dataItem["device_token"],
                    "user_message" => $dataItem["user_message"],
                    "notification_option" => $dataItem["push_preference_name"],
                    "push_preference_id" => $dataItem["push_preference_id"],
                    "member_id" => $dataItem["member_id"],
                    "push_option_id" => $dataItem["push_option_id"]
                );

                $result = sendPushNotification($dataObj);

                sleep(5);
            }
        }
    } catch (Exception $e) {
        // Handle any errors and display appropriate error message
        die("Error: " . $e->getMessage());
    }
}

function sendPushNotification($dataobj)
{
    global $serverFirebaseKey;
    // Device token of the recipient device
    $deviceToken = $dataobj["device_token"];

    $title = $dataobj["notification_option"] . " Notification";

    // Create the notification payload
    $payload = [
        'to' => $deviceToken,
        'notification' => [
            'title' => $title,
            'body' => $dataobj["user_message"],
            'sound' => 'default',
        ],
    ];

    // Convert the payload to JSON
    $payloadJson = json_encode($payload);

    // Set the headers for the HTTP request
    $headers = [
        'Content-Type: application/json',
        'Authorization: key=' . $serverFirebaseKey,
    ];

    // Send the HTTP request to FCM server
    $ch = curl_init('https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
    $result = curl_exec($ch);
    curl_close($ch);

    // Process the result
    if ($result === false) {
        // Failed to send the notification
        return false;
    } else {
        // The notification was sent successfully
        insertDataToFirebaseNotificationHistory($dataobj["push_preference_id"], $dataobj["member_id"], $dataobj["push_option_id"]);
        return true;
    }
}

function getMembersCron()
{

    global $con;

    $userId = 53;

    $firstQuery = "
        SELECT tbl_user_push_preferences.*, tbl_member.device_token, tbl_member.status, tbl_push_options.option_slug, tbl_push_options.option_name, tbl_push_options.option_id
        FROM tbl_user_push_preferences
        INNER JOIN tbl_member ON tbl_member.id = tbl_user_push_preferences.user_id
        INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_user_push_preferences.option_id
        WHERE tbl_member.status = 1
    ";

    $stmt = $con->prepare($firstQuery);
    //$stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $pushPreference = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data = array(
                "id" => $row['id'],
                "user_id" => $row['user_id'],
                "option_id" => $row['option_id'],
                "notification_id" => $row['notification_id'],
                "max_value" => $row['max_value'],
                "min_value" => $row['min_value'],
                "user_message" => $row['user_message'],
                "device_token" =>$row['device_token'],
                "option_name" =>$row['option_name']
            );
            //array_push($pushPreference, $data);

            // Perform additional query using the data
            $attributeRow = $row['option_slug'];
            $dailyQuery = "SELECT * FROM tbl_daily_weather_data WHERE $attributeRow BETWEEN ? AND ? AND weather_id = ( SELECT MAX(weather_id) FROM tbl_daily_weather_data WHERE user_id = ?)";
            $dailyStmt = $con->prepare($dailyQuery);
            $dailyStmt->bind_param("ddi", $data['min_value'], $data['max_value'], $userId);
            $dailyStmt->execute();
            $dailyResult = $dailyStmt->get_result();

            if ($dailyResult->num_rows > 0) {
                $dailyWeatherData = array();
                while ($dailyRow = $dailyResult->fetch_assoc()) {
                    // Process and store the daily weather data
                    $dailyData = array(
                        "weather_id" => $dailyRow['weather_id'],
                        "push_preference_id" => $data['id'],
                        "device_token" => $data['device_token'],
                        "user_message" => $data['user_message'],
                        "push_preference_name" => $data['option_name'],
                        "push_option_id" => $data['option_id'],
                        "member_id" => $data['user_id']
                    );
                    array_push($pushPreference, $dailyData);
                }

                // Add daily weather data to the main data array
                $data['daily_weather'] = $dailyWeatherData;
            }

        }
    }
    return $pushPreference;
}

function insertDataToFirebaseNotificationHistory($preference_id, $member_id, $option_id)
{
    global $con;

    $dateTime = new DateTime();
    // Format date and time
    $date = $dateTime->format('Y-m-d H:i:s');

    $stmt = $con->prepare("INSERT INTO tbl_firebase_notification_history (preference_id, member_id, option_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $preference_id, $member_id, $option_id);
    $stmt->execute();
}

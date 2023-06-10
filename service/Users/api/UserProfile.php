<?php
class UserProfile
{
    // Connection
    private $conn;
    // Table
    private $db_table = "tbl_member";
    //
    public $id;
    public $user_id;
    public $option_id;
    public $notification_id;
    public $max_value;
    public $min_value;
    public $created_at;
    public $user_message;
    public $option_name;

    // Db connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Method to get a single user custom alert
    public function getSingleUser($id)
    {
        // $sqlQuery = "SELECT  " . $this->db_table . ".*, tbl_push_options.option_name FROM " . $this->db_table . " INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_user_push_preferences.option_id WHERE id = ?";
        // $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->bind_param("i", $id);
        // $stmt->execute();
        // $result = $stmt->get_result();

        // if ($result->num_rows === 1) {
        //     $row = $result->fetch_assoc();
        //     $this->id = $row['id'];
        //     $this->user_id = $row['user_id'];
        //     $this->option_id = $row['option_id'];
        //     $this->notification_id = $row['notification_id'];
        //     $this->max_value = $row['max_value'];
        //     $this->min_value = $row['min_value'];
        //     $this->created_at = $row['created_at'];
        //     $this->user_message = $row['user_message'];
        //     $this->option_name = $row['option_name'];
        //     return true;
        // } else {
        //     return false;
        // }
    }

    public function updateUserData($id, $username, $password, $email, $phone)
    {
        $device_token = "";
        $status = "";
        $sqlQuery = "UPDATE " . $this->db_table . " SET username = ?, password = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("ssssi", $username, $password, $email, $phone, $id);

        if ($stmt->execute()) {

            $stmt = $this->conn->prepare("SELECT id, username, email, phone, device_token, status FROM tbl_member WHERE id = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $stmt->bind_result($id, $username, $email, $phone, $device_token, $status);
            $stmt->fetch();

            $user = array(
                'id' => $id,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'device_token' => $device_token,
                'status' => $status
            );

            $stmt->close();

            $response['error'] = false;
            $response['message'] = 'User Updated Successfully';
            $response['user'] = $user;

            return $response;
        } else {
            return false;
        }
    }

    public function getUserNoticeCount($id)
    {
        $sqlQuery = "SELECT COUNT(*) AS row_count FROM tbl_user_push_preferences WHERE user_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $userNoticeCount = array();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            $userNoticeCount['createdNotice'] = $row["row_count"];
        } else {
            $userNoticeCount = array(
                'createdNotice' => 0,
            );
        }

        $sqlQuery2 = "SELECT COUNT(*) AS row_count FROM tbl_firebase_notification_history WHERE member_id = ?";
        $stmt = $this->conn->prepare($sqlQuery2);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            $userNoticeCount['receivedNotice'] = $row["row_count"];
        } else {
            $userNoticeCount['receivedNotice'] = 0;
        }

        $userNoticeCount['status'] = true;

        return $userNoticeCount;
    }

    public function updateStatus($id, $status)
    {

        $sqlQuery = "UPDATE " . $this->db_table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("ii", $status, $id);

        if ($stmt->execute()) {

            $stmt = $this->conn->prepare("SELECT status FROM tbl_member WHERE id = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $stmt->bind_result($status);
            $stmt->fetch();

            $user = array(
                'status' => $status
            );

            $stmt->close();

            $response['error'] = false;
            $response['message'] = 'User Updated Successfully';
            $response['user'] = $user;

            return $response;
        } else {
            return false;
        }
    }

    public function updateLocation($id, $longitude, $latitude)
    {
        $locationName = $this->getLocationNameFromeXternalService($latitude, $longitude);

        $sqlQuery = "UPDATE tbl_location SET longitude = ?, latitude = ?, location = ? WHERE member_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("ddsi", $longitude, $latitude, $locationName, $id);

        if ($stmt->execute()) {

            //delete other data from user preference and user push history
            //User push prefrences
            $sqlQuery = "DELETE FROM tbl_user_push_preferences WHERE user_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {

                $stmt->close();

                $response['error'] = false;
                $response['message'] = 'User Location Updated Successfully';
                $response["data"] = $locationName;

                return $response;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function updatePassword($id, $password)
    {

        $sqlQuery = "UPDATE " . $this->db_table . " SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $password, $id);

        if ($stmt->execute()) {

            return true;
        } else {
            return false;
        }
    }

    public function deleteUserData($id)
    {
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {

            $stmt->close();

            $response['error'] = false;
            $response['message'] = 'User Deleted Successfully';

            return $response;
        } else {
            return false;
        }
    }


    public function getLocationNameFromeXternalService($lat, $lng)
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

    public function getUserActiveData($id)
    {
        $dataArray = array();

        $queryCurrentData = "SELECT tbl_current_weather_data.*, tbl_location.location FROM tbl_current_weather_data INNER JOIN tbl_location ON tbl_location.location_id = tbl_current_weather_data.location_id WHERE user_id = ?";
        $stmt = $this->conn->prepare($queryCurrentData);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        //if ($stmt->num_rows < 0) {

            $currentWeatherDatas = array();

            while ($row = $result->fetch_assoc()) {
                $currentWeatherData = array(
                    "date_stamp" => $row['date_stamp'],
                    "temperature" => $row['temperature'],
                    "uvi" => $row['uvi'],
                    "wind_speed" => $row['wind_speed'],
                    "rainfall" => $row['rainfall'],
                    "pop" => $row['pop'],
                    "weather" => $row['weather'],
                    "description" => $row['description'],
                    "icon" => $row['icon'],
                    "location" => $row['location']
                );
                $currentWeatherDatas[] = $currentWeatherData;
            }

            $dataArray["current"] = $currentWeatherDatas;

        //}

        $queryHourlyData = "SELECT tbl_hourly_weather_data.*, tbl_location.location FROM tbl_hourly_weather_data INNER JOIN tbl_location ON tbl_location.location_id = tbl_hourly_weather_data.location_id WHERE user_id = ?";
        $stmt = $this->conn->prepare($queryHourlyData);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        //if ($stmt->num_rows > 0) {

            $hourlyWeatherDatas = array();

            while ($row = $result->fetch_assoc()) {
                $hourlyWeatherData = array(
                    "time_stamp" => $row['time_stamp'],
                    "temperature" => $row['temperature'],
                    "uvi" => $row['uvi'],
                    "wind_speed" => $row['wind_speed'],
                    "rainfall" => $row['rainfall'],
                    "pop" => $row['pop'],
                    "weather" => $row['weather'],
                    "description" => $row['description'],
                    "icon" => $row['icon'],
                    "location" => $row['location']
                );
                $hourlyWeatherDatas[] = $hourlyWeatherData;
            }

            $dataArray["hourly"] = $hourlyWeatherDatas;

        //}

        $queryHourlyData = "SELECT tbl_daily_weather_data.*, tbl_location.location FROM tbl_daily_weather_data INNER JOIN tbl_location ON tbl_location.location_id = tbl_daily_weather_data.location_id WHERE user_id = ?";
        $stmt = $this->conn->prepare($queryHourlyData);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        //if ($stmt->num_rows > 0) {

            $dailyWeatherDatas = array();

            while ($row = $result->fetch_assoc()) {
                $dailyWeatherData = array(
                    "date_stamp" => $row['date_stamp'],
                    "temperature" => $row['temperature'],
                    "uvi" => $row['uvi'],
                    "wind_speed" => $row['wind_speed'],
                    "rainfall" => $row['rainfall'],
                    "pop" => $row['pop'],
                    "weather" => $row['weather'],
                    "description" => $row['description'],
                    "icon" => $row['icon'],
                    "location" => $row['location']
                );
                $dailyWeatherDatas[] = $dailyWeatherData;
            }

            $dataArray["daily"] = $dailyWeatherDatas;

        //}

        $response = array(
            "data" => $dataArray,
            "status" => true,
        );

        return $response;
    }
}

<?php
class UserCustomAlert
{
    // Connection
    private $conn;
    // Table
    private $db_table = "tbl_user_push_preferences";
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

    public function getAllUserCustomAlerts()
    {
        $sqlQuery = "SELECT  " . $this->db_table . ".*, tbl_push_options.option_name FROM " . $this->db_table . " INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_user_push_preferences.option_id";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        return $stmt->get_result();
    }

    // Method to get a single user custom alert
    public function getSingleAlert($id)
    {
        $sqlQuery = "SELECT  " . $this->db_table . ".*, tbl_push_options.option_name FROM " . $this->db_table . " INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_user_push_preferences.option_id WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->option_id = $row['option_id'];
            $this->notification_id = $row['notification_id'];
            $this->max_value = $row['max_value'];
            $this->min_value = $row['min_value'];
            $this->created_at = $row['created_at'];
            $this->user_message = $row['user_message'];
            $this->option_name = $row['option_name'];
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method to add a new user custom alert
    public function addUserCustomAlert($user_id, $option_name, $notification_id, $max_value, $min_value, $user_message)
    {

        $option_id  = null;
        $other_option_data  = null;
        $created_at = null;


        $sqlQuery = "SELECT * FROM tbl_push_options WHERE option_name = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $option_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {

            $stmt->bind_result($option_id, $option_name, $other_option_data, $created_at);
            $stmt->fetch();

            $stmt = $this->conn->prepare("INSERT INTO " . $this->db_table . " (user_id, option_id, notification_id, max_value, min_value, user_message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisdds", $user_id, $option_id, $notification_id, $max_value, $min_value, $user_message);

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        }
    }

    // Method to update a user custom alert
    public function updateUserCustomAlert($id, $option_id, $max_value, $min_value, $user_message)
    {
        $sqlQuery = "UPDATE " . $this->db_table . " SET option_id = ?, max_value = ?, min_value = ?, user_message = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("iiiss", $option_id, $max_value, $min_value, $user_message, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Method to delete a user custom alert
    public function deleteUserCustomAlert($id)
    {
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function getUserNotifications($user_id)
    {
        $sqlQuery = "SELECT tbl_user_push_preferences.*, tbl_push_options.option_name FROM tbl_user_push_preferences INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_user_push_preferences.option_id WHERE user_id = ? ORDER BY tbl_user_push_preferences.created_at DESC";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = array();

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
                $notification = array(
                    "id" => $row['id'],
                    "user_id" => $row['user_id'],
                    "option_id" => $row['option_id'],
                    "notification_id" => $row['notification_id'],
                    "option_name" => $row['option_name'],
                    "max_value" => $row['max_value'],
                    "min_value" => $row['min_value'],
                    "created_at" => $row['created_at'],
                    "user_message" => $row['user_message'],
                );
                $notifications[] = $notification;
            }

            $response = array(
                "data" => $notifications,
                "status" => true,
            );
            $stmt->close();
            return $response;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function getUserNotificationsSent($user_id)
    {

        $sqlQuery = "SELECT tbl_firebase_notification_history.notification_id, tbl_firebase_notification_history.member_id, tbl_firebase_notification_history.option_id, tbl_firebase_notification_history.notification_timestamp, tbl_firebase_notification_history.read_at, tbl_push_options.option_name, tbl_user_push_preferences.user_message, tbl_user_push_preferences.min_value, tbl_user_push_preferences.max_value
        FROM tbl_firebase_notification_history 
        INNER JOIN tbl_push_options ON tbl_push_options.option_id = tbl_firebase_notification_history.option_id 
        INNER JOIN tbl_user_push_preferences ON tbl_user_push_preferences.id = tbl_firebase_notification_history.preference_id 
        WHERE member_id = ?
        ORDER BY tbl_firebase_notification_history.created_at DESC";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = array();

        if ($result->num_rows > 0) {


            while ($row = $result->fetch_assoc()) {
                $notification = array(
                    "id" => $row['notification_id'],
                    "user_id" => $row['member_id'],
                    "option_id" => $row['option_id'],
                    "option_name" => $row['option_name'],
                    "notification_id" => $row['notification_id'],
                    "max_value" => $row['max_value'],
                    "min_value" => $row['min_value'],
                    "date_sent" => $row['notification_timestamp'],
                    "user_message" => $row['user_message'],
                    "read_at" => $row['read_at']
                );
                $notifications[] = $notification;
            }

            $response = array(
                "data" => $notifications,
                "status" => false,
            );
            $stmt->close();
            return $response;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function markNotificationSentAsRead($notificationId)
    {
        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d H:i:s');

        $sqlQuery = "UPDATE tbl_firebase_notification_history SET read_at = ? WHERE notification_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $date, $notificationId);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function deleteNotificationSent($notificationId)
    {
        $sqlQuery = "DELETE FROM tbl_firebase_notification_history WHERE notification_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $notificationId);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function getAlertOptions()
    {
        $sqlQuery = "SELECT * FROM tbl_push_options";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = array();

        while ($row = $result->fetch_assoc()) {
            $notification = array(
                "option_name" => $row['option_name'],
                "option_id" => $row['option_id'],
            );
            $notifications[] = $notification;
        }

        $response = array(
            "data" => $notifications,
            "status" => true,
        );

        $stmt->close();

        return $response;
    }
}

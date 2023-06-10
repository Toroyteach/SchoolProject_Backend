<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../../../connection.inc.php';
include_once '../UserCustomAlert.php';

$item = new UserCustomAlert($con);
$notification_id = isset($_POST['id']) ? $_POST['id'] : die(); // Replace with the actual user ID

$notifications = $item->markNotificationSentAsRead($notification_id);

if ($notifications) {
    http_response_code(200);
    echo json_encode(array("message" => "Notification Marked as Read."));
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Failed to mark notification as Read"));
}

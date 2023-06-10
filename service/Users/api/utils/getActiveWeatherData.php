<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../../../connection.inc.php';
include_once '../UserProfile.php';

$item = new UserProfile($con);
$item->id = isset($_GET['id']) ? $_GET['id'] : die();

$response = $item->getUserActiveData($item->id);

// Delete the user custom alert
if ($response) {
    // Alert deleted successfully
    http_response_code(200);
    echo json_encode(array($response));
} else {
    // Failed to delete the alert
    http_response_code(500);
    echo json_encode(array("message" => "Failed to get Active Weather Data."));
}

<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../UserCropDistribution.php';
    
    $item = new UserCropDistribution($con);

    $user_id = isset($_GET['id']) ? $_GET['id'] : die(); // Replace with the actual user ID

    $notifications = $item->getUserDashboardDataArray($user_id);

    if (!empty($notifications)) {
        http_response_code(200);
        echo json_encode($notifications);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No notifications found for the user."));
    }

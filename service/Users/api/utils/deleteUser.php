<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    require_once '../../../../connection.inc.php';
    include_once '../UserProfile.php';
    
    $item = new UserProfile($con);
    $item->id = isset($_GET['id']) ? $_GET['id'] : die();
    
    $options = $item->deleteUserData($item->id);
    
    if ($options) {
        http_response_code(200);
        echo json_encode(array("message" => "User Data Deleted Successfully"));
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Failed to delete user Data"));
    }
    
?>
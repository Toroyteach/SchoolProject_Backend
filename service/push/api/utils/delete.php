<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    require_once '../../../../connection.inc.php';
    include_once '../UserCustomAlert.php';
    
    $item = new UserCustomAlert($con);
    $item->id = isset($_GET['id']) ? $_GET['id'] : die();
    
    // Delete the user custom alert
    if ($item->deleteUserCustomAlert($item->id)) {
        // Alert deleted successfully
        http_response_code(200);
        echo json_encode(array("message" => "User custom alert deleted."));
    } else {
        // Failed to delete the alert
        http_response_code(500);
        echo json_encode(array("message" => "Failed to delete user custom alert."));
    }
    
?>
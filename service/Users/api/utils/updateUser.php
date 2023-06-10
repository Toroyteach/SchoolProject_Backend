<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../UserProfile.php';
    
    $item = new UserProfile($con);
    $item->id = isset($_GET['id']) ? $_GET['id'] : die();

    // Check if the request contains the necessary data for updating a user
    if (isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['phone'])) {
        // Assign the data from the request to variables
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $userData = $item->updateUserData($item->id, $username, $password, $email, $phone);
    
        // Add the new user custom alert
        if (!empty($userData)) {
            // Alert created successfully
            http_response_code(201);
            echo json_encode($userData);
        } else {
            // Failed to create the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to Update user."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for updating user."));
    }
?>
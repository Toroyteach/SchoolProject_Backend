<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../../../Users/api/UserProfile.php';

    $item = new UserProfile($con);

    // Check if the request contains the necessary data for creating a new alert
    if (isset($_POST['name'], $_POST['email'], $_POST['mobile'], $_POST['comment'], $_POST['subject'])) {
        // Assign the data from the request to variables
        $name = $_POST['name'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $comment = $_POST['comment'];
        $subject = $_POST['subject'];
    
        // Add the new user custom alert
        if ($item->createFeedBack($name, $email, $mobile, $comment, $subject)) {
            // Alert created successfully
            http_response_code(201);
            echo json_encode(array("message" => "User FeedBack created."));
        } else {
            // Failed to create the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create user feedback."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for creating feedback."));
    }
?>
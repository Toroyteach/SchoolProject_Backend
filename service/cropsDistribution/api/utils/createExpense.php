<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../UserCropDistribution.php';
    
    $item = new UserCropDistribution($con);

    // Check if the request contains the necessary data for creating a new alert
    if (isset($_POST['expense_date'], $_POST['expense_description'], $_POST['amount'], $_POST['user_id'], $_POST['crop_id'])) {
        // Assign the data from the request to variables
        $crop_id = $_POST['crop_id'];
        $expense_date = $_POST['expense_date'];
        $expense_description = $_POST['expense_description'];
        $amount = $_POST['amount'];
        $user_id = $_POST['user_id'];
    
        // Add the new user custom alert
        if ($item->createExpense($crop_id, $expense_date, $expense_description, $amount, $user_id)) {
            // Alert created successfully
            http_response_code(201);
            echo json_encode(array("message" => "User Crop Expense Created Successfully."));
        } else {
            // Failed to create the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create Crop Expense Item."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for creating Crop Expense."));
    }
?>
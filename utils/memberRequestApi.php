<?php

require_once '../connection.inc.php';

require_once '../utils/globalVariables.php';

$response = array();

$dateTime = new DateTime();

// Format date and time
$date = $dateTime->format('Y-m-d H:i:s');


if (isset($_GET['apicall'])) {

    switch ($_GET['apicall']) {

        case 'getWeatherInfo':

            break;

        case 'setPushNotificationRequest':


            break;

        case 'setUssdPin':


            break;

        default:
            $response['error'] = true;
            $response['message'] = 'Invalid Operation Called';
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid API Call';
}

echo json_encode($response);

function isTheseParametersAvailable($params)
{

    foreach ($params as $param) {
        if (!isset($_POST[$param])) {
            return false;
        }
    }
    return true;
}

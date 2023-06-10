<?php

require_once '../connection.inc.php';

require_once '../utils/globalVariables.php';

require_once '../service/cronService/firstTimeLoginFetch.php';

$response = array();

$dateTime = new DateTime();

// Format date and time
$date = $dateTime->format('Y-m-d H:i:s');


if (isset($_GET['apicall'])) {

    switch ($_GET['apicall']) {

        case 'signup':
            
            if (isTheseParametersAvailable(array('username', 'email', 'password', 'phone', 'latitude', 'longitude', 'location_name', 'device_token'))) {
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = md5($_POST['password']);
                $phone = $_POST['phone'];
                $device_token = $_POST['device_token'];
                $longitude = $_POST['longitude'];
                $latitude = $_POST['latitude'];
                $location = $_POST['location_name'];

                //Check if user exists then return error message
                $stmt = $con->prepare("SELECT id FROM tbl_member WHERE username = ? OR email = ? OR phone = ? OR device_token = ?");
                $stmt->bind_param("ssss", $username, $email, $phone, $device_token);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $response['error'] = true;
                    $response['message'] = 'User already registered';
                    $stmt->close();
                } else {

                    $stmt = $con->prepare("INSERT INTO tbl_member (username, email, password, phone, created_at, device_token) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $username, $email, $password, $phone, $date, $device_token );

                    if ($stmt->execute()) {

                        //if the GPS couldnt get location name gery from third party service to get correct name
                        if($location === "NotSet"){
                            $location = get_town_name($latitude, $longitude);
                        }
                        $member_id = $stmt->insert_id; 

                        $stmt = $con->prepare("INSERT INTO tbl_location (member_id, location, longitude, latitude) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("isdd", $member_id, $location, $longitude, $latitude);
                        
                        if($stmt->execute()){
                            $locationId = $stmt->insert_id; 

                            //Perfimr quick get reeust from te new user location data 
                            fetchAndStoreWeatherDataFirstTimeUser($con, $locationId);

                            $stmt = $con->prepare("SELECT tbl_member.id, tbl_member.username, tbl_member.email, tbl_member.phone, tbl_member.status, tbl_location.location FROM tbl_member INNER JOIN tbl_location ON tbl_location.member_id = tbl_member.id WHERE username = ?");
                            $stmt->bind_param("s", $username);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                
                                $user = array(
                                    'id' => $row['id'],
                                    'username' => $row['username'],
                                    'email' => $row['email'],
                                    'phone' => $row['phone'],
                                    'location_name' => $row['location'],
                                    'device_token' => 'tkn_device',
                                    'status' => $row['status']
                                );
        
                                $stmt->close();
        
                                $response['error'] = false;
                                $response['message'] = 'User registered successfully';
                                $response['user'] = $user;
                            }


                        } else {

                            $response['error'] = true;
                            $response['message'] = 'An error occurred trying to Create new user Location.';

                        }


                    } else {

                        $response['error'] = true;
                        $response['message'] = 'An error occurred trying to Create new user.';
                    }
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'required parameters are not available';
            }

            break;

        case 'login':

            if (isTheseParametersAvailable(array('username', 'password'))) {

                $username = $_POST['username'];
                $password = md5($_POST['password']);

                $stmt = $con->prepare("SELECT id, username, email, phone, status, device_token FROM tbl_member WHERE username = ? AND password = ?");
                $stmt->bind_param("ss", $username, $password);

                $stmt->execute();

                $stmt->store_result();

                if ($stmt->num_rows > 0) {

                    $stmt->bind_result($id, $username, $email, $phone, $status, $device_token);
                    $stmt->fetch();

                    $user = array(
                        'id' => $id,
                        'username' => $username,
                        'email' => $email,
                        'phone' => $phone,
                        'status' => $status,
                        'device_token' => $device_token
                    );

                    $response['error'] = false;
                    $response['message'] = 'Login successfull';
                    $response['user'] = $user;
                } else {
                    $response['error'] = false;
                    $response['message'] = 'Invalid username or password';
                }
            }
            break;

        case 'updateUser':

            if (isTheseParametersAvailable(array('username', 'email', 'phone', 'password'))) {

                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = md5($_POST['password']);
                $phone = $_POST['phone'];

                $stmt = $con->prepare("SELECT id, username, email, phone FROM tbl_member WHERE username = ? AND password = ?");

                $stmt->bind_param("sss", $id, $username, $password);

                $stmt->execute();

                $stmt->store_result();

                if ($stmt->num_rows > 0) {

                    $stmt = $con->prepare("UPDATE tbl_member SET username = ?, email = ?, password = ?, phone = ? WHERE id = ?");

                    $stmt->bind_param("sssss", $username, $email, $password, $phone, $id);

                    if ($stmt->execute()) {
                        $stmt = $con->prepare("SELECT id, username, email, phone FROM tbl_member WHERE id = ?");
                        $stmt->bind_param("s", $id);
                        $stmt->execute();
                        $stmt->bind_result($id, $username, $email, $phone);
                        $stmt->fetch();

                        $user = array(
                            'id' => $id,
                            'username' => $username,
                            'email' => $email,
                            'phone' => $phone
                        );

                        $stmt->close();

                        $response['error'] = false;
                        $response['message'] = 'User Updated Successfully';
                        $response['user'] = $user;
                    } else {

                        $response['error'] = true;
                        $response['message'] = 'An error occurred trying to Update User Details.';
                    }
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Invalid username or password';
                }
            }
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

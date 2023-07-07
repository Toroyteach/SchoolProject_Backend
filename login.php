<?php
require('connection.inc.php');
require('functions.inc.php');
$msg = '';
if (isset($_POST['submit'])) {
   $username = get_safe_value($con, $_POST['username']);
   $password = get_safe_value($con, $_POST['password']);
   $sql = "SELECT * FROM admin_users WHERE username='$username' AND password='$password'";
   $res = mysqli_query($con, $sql);
   $count = mysqli_num_rows($res);
   if ($count > 0) {
      $row = mysqli_fetch_assoc($res);
      if ($row['status'] == '0') {
         $msg = "Account deactivated";
      } else {
         $_SESSION['ADMIN_LOGIN'] = 'yes';
         $_SESSION['ADMIN_ID'] = $row['id'];
         $_SESSION['ADMIN_USERNAME'] = $username;
         $_SESSION['ADMIN_ROLE'] = $row['role'];
         header('location:index.php');
         die();
      }
   } else {
      $msg = "PLEASE ENTER CORRECT LOGIN DETAILS";
   }
}
?>
<!doctype html>
<html class="no-js" lang="">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>LOGIN PAGE</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-dark">
   <div class="loginContainer" style="min-height: 90vh">
      <div class="container">
         <div class="login-content" style="position:relative; top: 200px;">
         <div style="align-items: center;">
            <h1>Administrator Login</h1>
         </div>
            <div class="login-form">
               <form method="post">
                  <div class="form-group">
                     <label><b>USERNAME</b></label>
                     <input type="text" name="username" class="form-control" placeholder=" " required>
                  </div>
                  <div class="form-group">
                     <label><b>PASSWORD</b></label>
                     <input type="password" name="password" class="form-control" placeholder=" " required>
                  </div>
                  <button type="submit" name="submit" class="btn btn-success btn-flat m-b-30 m-t-30">SIGN IN</button>
               </form>
               <div class="field_error"><?php echo $msg ?></div>
            </div>
         </div>
      </div>
   </div>
   <script src="assets/js/vendor/jquery-2.1.4.min.js" type="text/javascript"></script>
   <script src="assets/js/popper.min.js" type="text/javascript"></script>
   <script src="assets/js/plugins.js" type="text/javascript"></script>
   <script src="assets/js/main.js" type="text/javascript"></script>
</body>

</html>
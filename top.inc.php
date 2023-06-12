<?php
require('connection.inc.php');
require('functions.inc.php');

if (!isset($_SESSION['ADMIN_LOGIN']) && $_SESSION['ADMIN_LOGIN'] === '') {
   $redirectURL = $baseUrl.'login.php';

   header("Location: $redirectURL");

   die();
}

?>
<!doctype html>
<html class="no-js" lang="">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>ADMIN DASHBOARD PAGE</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/font-awesome.min.css">

   <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/style.css">
   <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>

<body>
   <aside id="left-panel" class="left-panel">
      <nav class="navbar navbar-expand-sm navbar-default">
         <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
               <li class="menu-title">MENU</li>
               <?php if ($_SESSION['ADMIN_ROLE'] != 1) { ?>
                  <li class="menu-item-has-children dropdown">
                     <a href="<?php echo $baseUrl; ?>index.php"> DASHBOARD </a>
                  </li>

                  <li class="menu-item-has-children dropdown">
                     <a href="<?php echo $baseUrl; ?>service/Users/usersManagement.php"> USER MANAGEMENT </a>
                  </li>

                  <li class="menu-item-has-children dropdown">
                     <a href="<?php echo $baseUrl; ?>service/weather/weatherManagement.php"> WEATHER DATA </a>
                  </li>

                  <li class="menu-item-has-children dropdown">
                     <a href="<?php echo $baseUrl; ?>service/push/pushNotificationManagement.php"> PUSH NOTIFICATIONS </a>
                  </li>

                  <li class="menu-item-has-children dropdown">
                     <a href="<?php echo $baseUrl; ?>service/contact/contact_us.php"> CONTACT US</a>
                  </li>
               <?php } ?>
            </ul>
         </div>
      </nav>
   </aside>
   <div id="right-panel" class="right-panel">
      <header id="header" class="header">
         <div class="top-left">
            <div class="navbar-header">
               <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
            </div>
         </div>
         <div class="top-right">
            <div class="header-menu">
               <div class="user-area dropdown float-right">
                  <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">WELCOME <?php echo $_SESSION['ADMIN_USERNAME'] ?></a>
                  <div class="user-menu dropdown-menu">
                     <a class="nav-link" href="<?php echo $baseUrl; ?>logout.php"><i class="fa fa-power-off"></i>LOGOUT</a>
                  </div>
               </div>
            </div>
         </div>
      </header>
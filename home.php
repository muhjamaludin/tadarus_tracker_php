<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

include("config.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <style type="text/css">
  body {
    font: 14px sans-serif;
    text-align: center;
  }

  .wrapper {
    width: 650px;
    margin: 0 auto;
  }
  </style>
</head>

<body>
  <div class="wrapper">
    <nav class="navbar navbar-default">
      <div class="container">
        <a class="navbar-brand active" href="<?= BASE_URL ?>/home.php">Home</a>
        <ul class="nav navbar-nav">
          <li><a href="<?= BASE_URL ?>/tadarus">Tadarus</a></li>
          <!-- <li><a href="">Link</a></li> -->
        </ul>
      </div>
    </nav>
    <div class="page-header">
      <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p>
      <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
      <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>

  </div>
</body>

</html>
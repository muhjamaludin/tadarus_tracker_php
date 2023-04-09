<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

// Include config file
require_once "../config.php";

// Check existence of id parameter before processing further
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {

  // Prepare a select statement
  $sql = "SELECT * FROM tadarus_projects WHERE id = ?";

  if ($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_id);

    // Set parameters
    $param_id = trim($_GET["id"]);

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
      $result = mysqli_stmt_get_result($stmt);

      if (mysqli_num_rows($result) == 1) {
        /* Fetch result row as an associative array. Since the result set
        contains only one row, we don't need to use while loop */
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        // Retrieve individual field value
        $name = $row["name"];
        $status = $row["status"];
        $start = $row["start"];
        $target = $row["target"];
        $end = $row["end"];
      } else {
        // URL doesn't contain valid id parameter. Redirect to error page
        header("location: error.php");
        exit();
      }

    } else {
      echo "Oops! Something went wrong. Please try again later.";
    }
  }

  // Close statement
  mysqli_stmt_close($stmt);

  // Close connection
  mysqli_close($link);
} else {
  // URL doesn't contain id parameter. Redirect to error page
  header("location: error.php");
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>View Record</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <style type="text/css">
  .wrapper {
    width: 500px;
    margin: 0 auto;
  }
  </style>
</head>

<body onload="readDateDetail()">
  <div class="wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="page-header">
            <h1>View Record</h1>
          </div>
          <div class="form-group">
            <label>Name :</label>
            <span class="form-control-static"><?php echo $row["name"]; ?></span>
          </div>
          <div class="form-group">
            <label>Status :</label>
            <span class="form-control-static"><?php echo $row["status"]; ?></span>
          </div>
          <div class="form-group">
            <label>Start Date :</label>
            <span class="form-control-static"><?php echo date("l, d F Y", strtotime($row["start"])); ?></span>
          </div>
          <div class="form-group">
            <label>Target Date :</label>
            <span class="form-control-static"><?php echo date("l, d F Y", strtotime($row["target"])); ?></span>
          </div>
          <div class="form-group">
            <label>End Date :</label>
            <span class="form-control-static"><?php echo date("l, d F Y", strtotime($row["end"])); ?></span>
          </div>
          <p><a href="<?= BASE_URL ?>/projects/index.php" class="btn btn-primary">Back</a></p>
        </div>
      </div>
    </div>
  </div>

  <script src="calendar.js"></script>
</body>

</html>
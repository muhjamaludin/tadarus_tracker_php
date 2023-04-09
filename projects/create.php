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

// Define variables and initialize with empty values
$name = $status = $start = $end = $target = "";
$name_err = $status_err = $start_err = $target_err = $end_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate name
  $input_name = trim($_POST["name"]);
  if (empty($input_name)) {
    $name_err = "Please enter a name project.";
  } elseif (!ctype_alpha($input_name)) {
    $name_err = "Please enter a valid string name.";
  } elseif (strlen($input_name) > 48) {
    $name_err = "Max 48 char length";
  } else {
    $name = $input_name;
  }

  // Validate status
  $input_status = trim($_POST["status"]);
  if (empty($input_status)) {
    $status_err = "Please enter a status project.";
  } elseif (!preg_match('/^[a-zA-Z ]+$/', $input_status)) {
    $status_err = "Please enter a valid status.";
  } elseif (strlen($input_status) > 18) {
    $name_err = "Max 18 char length";
  } else {
    $status = $input_status;
  }

  $regex_date = "/^\d{4}-\d{2}-\d{2}$/";

  // Validate start date
  $input_start = trim($_POST["start"]);
  if (empty($input_start)) {
    $start_err = "Please enter start date";
  } elseif (!preg_match($regex_date, $input_start)) {
    $start_err = "Invalid format date";
  } else {
    $start = $input_start;
  }

  // Validate target date
  $input_target = trim($_POST["target"]);
  $start_timestamp = strtotime($start);
  $target_timestamp = strtotime($input_target);
  if (empty($input_target)) {
    $target_err = "Please enter target date";
  } elseif ($target_timestamp <= $start_timestamp) {
    $target_err = "Target date must be greater than start date";
  } elseif (!preg_match($regex_date, $input_target)) {
    $target_err = "Invalid format date";
  } else {
    $target = $input_target;
  }

  // validate end date
  $input_end = trim($_POST['end'] ?? "");
  if ($input_end != "") {
    if (!preg_match($regex_date, $input_end)) {
      $end_err = "Invalid format date";
    }
  } else {
    $end = $input_end;
  }

  // Check input errors before inserting in database
  if (empty($name_err) && empty($status_err) && empty($start_err) && empty($target_err)) {
    // Prepare an insert statement
    $sql = "INSERT INTO tadarus_projects (name, status, start, end, target) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "sssss", $param_name, $param_status, $param_start, $param_end, $param_target);

      // Set parameters
      $param_name = $name;
      $param_status = $status;
      $param_start = $start;
      $param_end = $end;
      $param_target = $target;

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        // Records created successfully. Redirect to landing page
        header("location: index.php");
        exit();
      } else {
        echo "Something went wrong. Please try again later.";
      }
    }

    // Close statement
    mysqli_stmt_close($stmt);
  }

  // Close connection
  mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Record</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <style type="text/css">
  .wrapper {
    width: 500px;
    margin: 0 auto;
  }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="page-header">
            <h2>Tambah Record</h2>
          </div>
          <p>Silahkan isi form di bawah ini kemudian submit untuk menambahkan data tadarus project ke dalam database.
          </p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
              <label>Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo $name ?>">
              <span class="help-block"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($status_err)) ? 'has-error' : ''; ?>">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="">Choose Status</option>
                <option value="todo">Todo</option>
                <option value="on going">On Going</option>
                <option value="khatam">Khatam</option>
                <option value="overdue">Overdue</option>
                <option value="overdue and khatam">Overdue & Khatam</option>
              </select>
              <span class="help-block"><?php echo $status_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($start_err)) ? 'has-error' : ''; ?>">
              <label>Start Date</label>
              <input type="date" name="start" class="form-control" value="<?php echo $start; ?>">
              <span class="help-block"><?php echo $start_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($target_err)) ? 'has-error' : ''; ?>">
              <label>Target Date</label>
              <input type="date" name="target" class="form-control" value="<?php echo $target; ?>">
              <span class="help-block"><?php echo $target_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="<?= BASE_URL ?>/projects/index.php" class="btn btn-default">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
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

// list of surah and ayat from json
$surah_string = file_get_contents("quranlist.json");
$data_surah_ayat = json_decode($surah_string, true);
array_unshift($data_surah_ayat, array("number" => 0, "surah" => "choose surah", "ayat" => 1));

// Define variables and initialize with empty values
$juz = $surah = $ayat = $date = $time = $datetime = $hijri = "";
$juz_err = $surah_err = $ayat_err = $datetime_err = "";

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"])) {
  // Get hidden input value
  $id = $_POST["id"];

  // Validate juz
  $input_juz = trim($_POST["juz"]);
  if (empty($input_juz)) {
    $juz_err = "Please enter a juz.";
  } elseif (!ctype_digit($input_juz)) {
    $juz_err = "Please enter a positive integer value.";
  } else if ($input_juz < 1 || $input_juz > 30) {
    $juz_err = "Invalid juz input, out of range";
  } else {
    $juz = $input_juz;
  }

  // Validate surah
  $input_surah = trim($_POST["surah"]);
  $clear_surah = explode("-", $input_surah);
  $input_surah = $clear_surah[0];
  if (empty($input_surah)) {
    $surah_err = "Please enter a surah.";
  } else if ($input_surah < 1 || $input_surah > 114) {
    $surah_err = "Invalid input surah";
  } else {
    $surah = $input_surah;
  }

  // Validate ayat
  $input_ayat = trim($_POST["ayat"]);
  $max_ayat = $clear_surah[1];
  if (empty($input_ayat)) {
    $ayat_err = "Please enter an ayat.";
  } else if (!ctype_digit($input_ayat)) {
    $ayat_err = "Please enter a positive integer value.";
  } else if ($input_ayat < 0 || $input_ayat > $max_ayat) {
    $ayat_err = "Invalid input ayat on surah number " . $surah;
  } else {
    $ayat = $input_ayat;
  }

  // Validate datetime
  $input_datetime = trim($_POST["datetime"]);
  if (empty($input_datetime)) {
    $datetime_err = "Please enter a date.";
  } else {
    $datetime = $input_datetime;
    $input_datetime = explode("T", $input_datetime);
    $date = $input_datetime[0];
    $time = $input_datetime[1];
  }

  // Validate hijri date
  $input_hijri = trim($_POST['hijri']);
  if (empty($input_hijri)) {
    $datetime_err = "Error converting date to hijri";
  } else {
    $hijri = $input_hijri;
  }

  // Check input errors before inserting in database
  if (empty($juz_err) && empty($surah_err) && empty($ayat_err) && empty($datetime_err)) {
    // set update datetime
    date_default_timezone_set("Asia/Bangkok");
    $dateTimeStamp = date("Y-m-d H:i:s");

    // Prepare an update statement
    $sql = "UPDATE tadaruses SET juz=?, surah=?, ayat=?, date=?, time=?, hijriah=?, 
                updatedAt='$dateTimeStamp' WHERE id=?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ssssssi", $param_juz, $param_surah, $param_ayat, $param_date, $param_time, $param_hijri, $param_id);

      // Set parameters
      $param_juz = $juz;
      $param_surah = $surah;
      $param_ayat = $ayat;
      $param_date = $date;
      $param_time = $time;
      $param_hijri = $hijri;
      $param_id = $id;

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        // Records updated successfully. Redirect to landing page
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
} else {
  // Check existence of id parameter before processing further
  if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Get URL parameter
    $id = trim($_GET["id"]);

    // Prepare a select statement
    $sql = "SELECT * FROM tadaruses WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "i", $param_id);

      // Set parameters
      $param_id = $id;

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
          /* Fetch result row as an associative array. Since the result set
          contains only one row, we don't need to use while loop */
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

          // Retrieve individual field value
          $juz = $row["juz"];
          $surah = $row["surah"];
          $ayat = $row["ayat"];
          $date = $row["date"];
          $time = $row["time"];
          $hijri = $row["hijriah"];
          $datetime = $date . "T" . $time;
        } else {
          // URL doesn't contain valid id. Redirect to error page
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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Update Record</title>
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
            <h2>Update Record</h2>
          </div>
          <p>Please edit the input values and submit to update the record.</p>
          <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
            <div class="form-group <?php echo (!empty($date_err)) ? 'has-error' : ''; ?>">
              <label>Tanggal & Waktu</label>
              <input type="datetime-local" name="datetime" class="form-control" value="<?php echo $datetime; ?>">
              <span class="help-block"><?php echo $datetime_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($juz_err)) ? 'has-error' : ''; ?>">
              <label>Juz</label>
              <input type="number" name="juz" class="form-control" value="<?php echo $juz ?>">
              <span class="help-block"><?php echo $juz_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($surah_err)) ? 'has-error' : ''; ?>">
              <label>Surah</label>
              <select name="surah" class="form-control">
                <?php foreach ($data_surah_ayat as $dsurah) {
                  echo '<option value="' . $dsurah['number'] . '-' . $dsurah['ayat'] . '" ' . ($dsurah["number"] == $surah ? "selected" : "") . '>' . $dsurah['surah'] . '</option>';
                } ?>
              </select>
              <span class="help-block"><?php echo $surah_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($ayat_err)) ? 'has-error' : ''; ?>">
              <label>Ayat</label>
              <input type="number" name="ayat" class="form-control" value="<?php echo $ayat; ?>">
              <span class="help-block"><?php echo $ayat_err; ?></span>
            </div>
            <input type="hidden" name="hijri" id="hijri" value="<?php echo $hijri ?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="<?= BASE_URL ?>/tadarus/index.php" class="btn btn-default">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
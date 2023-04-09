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

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
  <style type="text/css">
  .wrapper {
    width: 750px;
    margin: 0 auto;
  }

  .page-header h2 {
    margin-top: 0;
  }

  table tr td:last-child a {
    margin-right: 15px;
  }

  /* color status */
  .todo {
    background-color: #0d6efd;
  }

  .khatam {
    background-color: #20c997;
  }

  .on.going {
    background-color: #ffc107;
  }

  .overdue {
    background-color: #dc3545;
  }

  .overdue.khatam {
    background-color: #d63384;
  }

  .pengaturan {
    display: flex;
    justify-content: space-evenly;
  }
  </style>
  <script type="text/javascript">
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });
  </script>
</head>

<body>
  <div class="wrapper">
    <div class="container-fluid">
      <nav class="navbar navbar-default">
        <div class="container">
          <a class="navbar-brand" href="<?= BASE_URL ?>/home.php">Home</a>
          <ul class="nav navbar-nav">
            <li><a href="<?= BASE_URL ?>/tadarus">Tadarus</a></li>
            <li class="active"><a href="<?= BASE_URL ?>/projects">Projects</a></li>
          </ul>
        </div>
      </nav>
      <div class="row">
        <div class="col-ms-12">
          <div class="page-header clearfix">
            <h2 class="pull-left">Tadarus Projects</h2>
            <a href="<?= BASE_URL ?>/projects/create.php" class="btn btn-success pull-right">Tambah Baru</a>
          </div>
          <?php
          // Attempt select query execution
          $sql = "SELECT * FROM tadarus_projects ORDER BY id DESC";
          if ($result = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($result) > 0) {
              echo "<table class='table table-bordered table-striped'>";
              echo "<thead>";
              echo "<tr>";
              echo "<th>#</th>";
              echo "<th>Name</th>";
              echo "<th>Status</th>";
              echo "<th>Start Date</th>";
              echo "<th>Target Date</th>";
              echo "<th>End Date</th>";
              echo "<th>Pengaturan</th>";
              echo "</tr>";
              echo "</thead>";
              echo "<tbody>";
              $i = 0;
              while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $i + 1 . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td class='" . $row['status'] . "'>" . $row['status'] . "</td>";
                echo "<td>" . $row['start'] . "</td>";
                echo "<td>" . $row['target'] . "</td>";
                echo "<td>" . ($row['end'] == "0000-00-00" ? "-" : $row['end']) . "</td>";
                echo "<td class='pengaturan'>";
                echo "<a href='" . BASE_URL . "/projects/read.php?id=" . $row['id'] . "' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                echo "<a href='" . BASE_URL . "/projects/update.php?id=" . $row['id'] . "' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                echo "<a href='" . BASE_URL . "/projects/delete.php?id=" . $row['id']
                  . "' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                echo "</td>";
                echo "</tr>";
                $i++;
              }
              echo "</tbody>";
              echo "</table>"; // Free result set
              mysqli_free_result($result);
            } else {
              echo "<p class='lead'><em>No records were found.</em></p>";
            }
          } else {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
          } // Close connection
          mysqli_close($link); ?>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
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

$limit = 10;
$page = 1;
// check if pagination is active
if (isset($_GET['limit'])) {
  $limit = $_GET['limit'];
}
if (isset($_GET['page'])) {
  $page = $_GET['page'];
}

// get total field
$total_field = 0;
$sql = "SELECT COUNT(*) AS total FROM tadarus_projects";
if ($result = mysqli_query($link, $sql)) {
  if (mysqli_num_rows($result) > 0) {
    $response = mysqli_fetch_assoc($result);
    $total_field = $response['total'];
    // Free result set
    mysqli_free_result($result);
  }
} else {
  echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
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

  .total {
    color: grey;
    font-size: 16px;
  }

  #limit {
    width: 4em;
  }

  /* center a div */
  .parent {
    display: flex;
    align-items: center;
  }

  .child {
    margin: 0 auto;
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
          $sql = "SELECT * FROM tadarus_projects ORDER BY id DESC LIMIT " . $limit . " OFFSET " . ($page - 1) * $limit;
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
                echo "<td>" . (($page - 1) * $limit) + $i + 1 . "</td>";
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

      <!-- pagination -->
      <div class="row">
        <div aria-label="Page navigation" class="col-xs-9">
          <ul class="pagination child">
            <li class="<?= $page == 1 ? 'disabled' : '' ?>">
              <a href="<?= BASE_URL . "/projects/index.php?page=" . ($page - 1) . "&limit=" . $limit ?>"
                aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <?php
            for ($i = 1; $i <= ceil($total_field / $limit); $i++) {
              echo "<li><a href='" . BASE_URL . "/projects/index.php?page=" . $i . "&limit=" . $limit . "'>" . $i . "</a></li>";
            }
            ?>
            <li class="<?= $page == ceil($total_field / $limit) ? 'disabled' : '' ?>">
              <a href="<?= BASE_URL . "/projects/index.php?page=" . ($page + 1) . "&limit=" . $limit ?>"
                aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </div>
        <div class="col-xs-3 row parent">
          <select class="form-control col-xs-5" id="limit" class="child" onchange="changeLimit()">
            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
          </select>
          <span class="total col-xs-7 child">Total: <?= $total_field ?></span>
        </div>
      </div>
    </div>
  </div>

  <script>
  function changeLimit() {
    const limitValue = document.getElementById("limit").value;
    window.location.href = window.location.pathname + "?limit=" + limitValue;
  }
  </script>
  </div>
  </div>
</body>

</html>
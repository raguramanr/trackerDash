<!DOCTYPE html>
<?php
include 'template.php';
echo "<html>";
echo $headInclude;
echo $topBar;
echo $leftMenu;


#########################################################################################
#                                                                                       #
#  Function to return data from database                                                #
#                                                                                       #
#########################################################################################
function getDetail($conn, $sql) {
    $result = $conn->query($sql);
       if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              $value = $row['value'];
          }
       } else {
        $errmsg = "connection failed.";
        $value = 0;
    }
    return $value;
}

#########################################################################################
#                                                                                       #
#  Function to display all EXOS CRs from Database				        #
#                                                                                       #
#########################################################################################
function listTotalCRs($releaseName) {
include 'template.php';
include 'db_connect.php';
      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
	    echo "$releaseName - All Open CRs";
            echo "<small></small>";
          echo "</h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";

        #<!-- Main content -->
        echo "<section class=\"content\">";
          echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Total CRs - </h3>";
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"example1\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Global State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";


                      $sql = "select bugNumber, severity, priority, creator, globalState from bugDescriptions where releaseDetected='$releaseName' order by bugNumber ASC";
                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>".$row[bugNumber]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "</tr>";
                         }
                        } else {
                          echo "0 results";
                        }

                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Global State</th>";
                      echo "</tr>";
                    echo "</tfoot>";
                  echo "</table>";
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</div><!-- /.col -->";
          echo "</div><!-- /.row -->";
        echo "</section><!-- /.content -->";
      echo "</div><!-- /.content-wrapper -->";
    echo "</div><!-- ./wrapper -->";

  echo $scriptInclude;
  echo "</body>";
echo "</html>";
}


#########################################################################################
#                                                                                       #
# Code to catch the click option and display the table accordingly		        #
#                                                                                       #
#########################################################################################
if ($_GET[action] == "listTotalCRs") {
  listTotalCRs("$_GET[releaseName]");
} else { 
}

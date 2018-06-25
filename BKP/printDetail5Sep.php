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
#  Function to display all EXOS CRs for the given Release Name/ID                       #
#                                                                                       #
#########################################################################################
function printTotalCRs($releaseName, $targetRelease) {
include 'template.php';
include 'db_connect.php';
      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "Printing $releaseName - All CRs";
            echo "<small></small>";
          echo "</h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";

	$countTotal = getDetail($conn, "select count(bugNumber) as value from (SELECT bugNumber,ldapManagerName from bugDescriptions
                         LEFT JOIN releaseTracking USING(bugNumber)
                         LEFT JOIN users ON bugDescriptions.creator=users.username
                         WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                         AND (ldapManagerName like '%Velusamy%'      ||
                              ldapManagerName like '%Ramkumar%'      ||
                              ldapManagerName like '%Parthasarathy%' ||
                              ldapManagerName like '%Palanivel%'     ||
                              ldapManagerName like '%Raguraman%')  
                         GROUP by bugNumber) as t1");

        #<!-- Main content -->
        echo "<section class=\"content\">";
          echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Total CRs - $countTotal </h3>";
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"example1\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";


                     $sql = "SELECT bugNumber, summary, severity, priority, creator, releaseDetected, globalState, releaseState from bugDescriptions
                         LEFT JOIN releaseTracking USING(bugNumber)
                         LEFT JOIN users ON bugDescriptions.creator=users.username
                         WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                         AND (ldapManagerName like '%Velusamy%'      ||
                              ldapManagerName like '%Ramkumar%'      ||
                              ldapManagerName like '%Parthasarathy%' ||
                              ldapManagerName like '%Palanivel%'     ||
                              ldapManagerName like '%Raguraman%') GROUP by bugNumber";

                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "</tr>";
                         }
                        } else {
                          echo "0 results";
                        }

                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
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
#  Function to display all EXOS Assigned CRs for the given Release Name/ID 		#
#                                                                                       #
#########################################################################################
function printOpenCRs($releaseName, $targetRelease) {
include 'template.php';
include 'db_connect.php';
      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
	    echo "Printing $releaseName - All Open CRs";
            echo "<small></small>";
          echo "</h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";

	
	$countAssigned = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE (((((globalState='Assigned' || globalState='Idle') AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState))) 
                                   || (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName') 
                                   || (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease'))
                            OR    (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review') AND targetReleaseId='$targetRelease')))
                            AND (ldapManagerName like '%Velusamy%'      ||
                                 ldapManagerName like '%Ramkumar%'      ||
                                 ldapManagerName like '%Parthasarathy%' ||
                                 ldapManagerName like '%Palanivel%'     ||
                                 ldapManagerName like '%Raguraman%')");

        #<!-- Main content -->
        echo "<section class=\"content\">";
          echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Total CRs - $countAssigned </h3>";
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"example1\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";


		     $sql = "SELECT bugNumber, summary, severity, priority, creator, releaseDetected, globalState, releaseState from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE (((((globalState='Assigned' || globalState='Idle') AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState)))
                                   || (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName')
                                   || (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease'))
                            OR    (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review') AND targetReleaseId='$targetRelease')))
                            AND (ldapManagerName like '%Velusamy%'      ||
                                 ldapManagerName like '%Ramkumar%'      ||
                                 ldapManagerName like '%Parthasarathy%' ||
                                 ldapManagerName like '%Palanivel%'     ||
                                 ldapManagerName like '%Raguraman%')";
                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "</tr>";
                         }
                        } else {
                          echo "0 results";
                        }

                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
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
#  Function to display all EXOS Closed CRs for the given Release Name/ID		#
#                                                                                       #
#########################################################################################
function printClosedCRs($releaseName, $targetRelease) {
include 'template.php';
include 'db_connect.php';
      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "Printing $releaseName - All Closed CRs";
            echo "<small></small>";
          echo "</h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";

	$countClosed = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
                          WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released') AND targetReleaseId='$targetRelease')
                          OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') AND releaseDetected='$releaseName'))
                          AND (ldapManagerName like '%Velusamy%'      ||
                               ldapManagerName like '%Ramkumar%'      ||
                               ldapManagerName like '%Parthasarathy%' ||
                               ldapManagerName like '%Palanivel%'     ||
                               ldapManagerName like '%Raguraman%')");

        #<!-- Main content -->
        echo "<section class=\"content\">";
          echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Total CRs - $countClosed  </h3>";
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"example1\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";


                     $sql = "SELECT bugNumber, summary, severity, priority, creator, releaseDetected, globalState, releaseState from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
                          WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released') AND targetReleaseId='$targetRelease')
                          OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') AND releaseDetected='$releaseName'))
                          AND (ldapManagerName like '%Velusamy%'      ||
                               ldapManagerName like '%Ramkumar%'      ||
                               ldapManagerName like '%Parthasarathy%' ||
                               ldapManagerName like '%Palanivel%'     ||
                               ldapManagerName like '%Raguraman%')";

                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "</tr>";
                         }
                        } else {
                          echo "0 results";
                        }

                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
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
if ($_GET[action] == "printOpenCRs") {
  printOpenCRs($_GET[releaseName],$_GET[targetRelease]);
} elseif ($_GET[action] == "printClosedCRs") {
  printClosedCRs($_GET[releaseName],$_GET[targetRelease]);
} elseif ($_GET[action] == "printTotalCRs") {
  printTotalCRs($_GET[releaseName],$_GET[targetRelease]);
} else { 
}

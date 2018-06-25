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

       if ($_GET[showDetail] == "release") {	
	$countTotal = getDetail($conn, "select count(bugNumber) as value from (SELECT bugNumber,ldapManagerName from bugDescriptions
                         LEFT JOIN releaseTracking USING(bugNumber)
                         LEFT JOIN users ON bugDescriptions.creator=users.username
                         WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                         GROUP by bugNumber) as t1");
        } else {
         $countTotal = getDetail($conn, "select count(bugNumber) as value from (SELECT bugNumber,ldapManagerName from bugDescriptions
                         LEFT JOIN releaseTracking USING(bugNumber)
                         LEFT JOIN users ON bugDescriptions.creator=users.username
                         WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                         AND (ManagerName like 'rvelusamy'      ||
                              ManagerName like 'gramkumar'      ||
                              ManagerName like 'uparthasarathy' ||
                              ManagerName like 'spalanivel'     ||
                              ManagerName like 'ragrajan')
                         GROUP by bugNumber) as t1");
	}

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
                        echo "<th>Manager</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

	       if ($_GET[showDetail] == "release") {
                     $sql = "SELECT bugNumber, summary, severity, priority, creator, managerName, releaseDetected, globalState, releaseState from bugDescriptions
                         LEFT JOIN releaseTracking USING(bugNumber)
                         LEFT JOIN users ON bugDescriptions.creator=users.username
                         WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
			 GROUP by bugNumber";
		} else {
                     $sql = "SELECT bugNumber, summary, severity, priority, creator, managerName, releaseDetected, globalState, releaseState from bugDescriptions
                         LEFT JOIN releaseTracking USING(bugNumber)
                         LEFT JOIN users ON bugDescriptions.creator=users.username
                         WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                         AND (ManagerName like 'rvelusamy'      ||
                              ManagerName like 'gramkumar'      ||
                              ManagerName like 'uparthasarathy' ||
                              ManagerName like 'spalanivel'     ||
                              ManagerName like 'ragrajan')
			 GROUP by bugNumber";
		}

                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[managerName]."</td>";
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
                        echo "<th>Manager</th>";
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

	
       if ($_GET[showDetail] == "release") {
	$countAssigned = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE (((((globalState='Assigned' || globalState='Idle') AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState)))
                                   || (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName')
                                   || (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease'))
                            OR    (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review') AND targetReleaseId='$targetRelease')))");
	} else {
         $countAssigned = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE (((((globalState='Assigned' || globalState='Idle') AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState)))
                                   || (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName')
                                   || (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease'))
                            OR    (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review') AND targetReleaseId='$targetRelease')))
                            AND (ManagerName like 'rvelusamy'      ||
                              ManagerName like 'gramkumar'      ||
                              ManagerName like 'uparthasarathy' ||
                              ManagerName like 'spalanivel'     ||
                              ManagerName like 'ragrajan')");
	}


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
                        echo "<th>Manager</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";


	       if ($_GET[showDetail] == "release") {
		     $sql = "SELECT bugNumber, summary, severity, priority, creator, managerName, releaseDetected, globalState, releaseState from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE (((((globalState='Assigned' || globalState='Idle') AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState)))
                                   || (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName')
                                   || (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease'))
                            OR    (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review') AND targetReleaseId='$targetRelease')))";
		} else {
                     $sql = "SELECT bugNumber, summary, severity, priority, creator, managerName, releaseDetected, globalState, releaseState from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE (((((globalState='Assigned' || globalState='Idle') AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState)))
                                   || (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName')
                                   || (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease'))
                            OR    (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review') AND targetReleaseId='$targetRelease')))
                            AND (ManagerName like 'rvelusamy'      ||
                              ManagerName like 'gramkumar'      ||
                              ManagerName like 'uparthasarathy' ||
                              ManagerName like 'spalanivel'     ||
                              ManagerName like 'ragrajan')";
		}


                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[managerName]."</td>";
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
                        echo "<th>Manager</th>";
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

       if ($_GET[showDetail] == "release") {
	$countClosed = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
                          WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released') AND targetReleaseId='$targetRelease')
                          OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') AND releaseDetected='$releaseName'))");
	} else {
         $countClosed = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
                          WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released') AND targetReleaseId='$targetRelease')
                          OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') AND releaseDetected='$releaseName'))
                          AND (ManagerName like 'rvelusamy'      ||
                              ManagerName like 'gramkumar'      ||
                              ManagerName like 'uparthasarathy' ||
                              ManagerName like 'spalanivel'     ||
                              ManagerName like 'ragrajan')");
	}

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
                        echo "<th>Manager</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";


	       if ($_GET[showDetail] == "release") {
                     $sql = "SELECT bugNumber, summary, severity, priority, creator, managerName, releaseDetected, globalState, releaseState from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
                          WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released') AND targetReleaseId='$targetRelease')
                          OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') AND releaseDetected='$releaseName'))";
		} else {
                     $sql = "SELECT bugNumber, summary, severity, priority, creator, managerName, releaseDetected, globalState, releaseState from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
                          WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released') AND targetReleaseId='$targetRelease')
                          OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') AND releaseDetected='$releaseName'))
                          AND (ManagerName like 'rvelusamy'      ||
                              ManagerName like 'gramkumar'      ||
                              ManagerName like 'uparthasarathy' ||
                              ManagerName like 'spalanivel'     ||
                              ManagerName like 'ragrajan')";
		}

                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[managerName]."</td>";
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
                        echo "<th>Manager</th>";
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
#  Function to display EXOS CRs for given Manager and the state				#
#                                                                                       #
#########################################################################################
function printMgrCRs($releaseName, $targetRelease, $mgrName, $crState) {
include 'template.php';
include 'db_connect.php';
      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "Printing $releaseName $crState CRs for $mgrName";
            echo "<small></small>";
          echo "</h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";


	if ($crState=="Pending") {
          $totalCount = getDetail($conn, "SELECT count(bugNumber) as value from (select bugNumber from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users as verifierManager ON releaseTracking.assignedTo=verifierManager.username
                          LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.username 
                          WHERE (((releaseState='Verify Fix' || releaseState='Verify Task Complete') AND targetReleaseId='$targetRelease')
                          OR    ((globalState='Feedback Needed' || globalState =  'Verify Duplicate' || globalState =  'Verify No Change' || globalState='Verify Fix') 
			  AND (releaseDetected='$releaseName' || targetReleaseId='$targetRelease')))
                          AND (verifierManager.managerName like '%$mgrName%' || creatorManager.managerName like '%$mgrName%') group by bugNumber) as tmpt1");

          $sql = "SELECT bugNumber, summary, severity, priority, creator, verifier, releaseDetected, globalState, releaseState from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users as verifierManager ON releaseTracking.assignedTo=verifierManager.username
                          LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.username 
                          WHERE (((releaseState='Verify Fix' || releaseState='Verify Task Complete') AND targetReleaseId='$targetRelease')
                          OR    ((globalState='Feedback Needed' || globalState =  'Verify Duplicate' || globalState =  'Verify No Change' || globalState='Verify Fix') 
                          AND (releaseDetected='$releaseName' || targetReleaseId='$targetRelease')))
                          AND (verifierManager.managerName like '%$mgrName%' || creatorManager.managerName like '%$mgrName%') group by bugNumber";
	} elseif ($crState=="All"){
	  $totalCount = getDetail($conn, "SELECT count(bugNumber) as value from (SELECT bugNumber,ldapManagerName  from bugDescriptions 
						LEFT JOIN releaseTracking USING(bugNumber) 
						LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease') 
						AND ManagerName like '%$mgrName%' GROUP BY bugNumber) as t1");

	  $sql = "SELECT bugNumber, summary, severity, priority, creator, verifier, releaseDetected, globalState, releaseState from bugDescriptions 
                                                LEFT JOIN releaseTracking USING(bugNumber) 
                                                LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease') 
                                                AND ManagerName like '%$mgrName%' GROUP BY bugNumber";
	} elseif ($crState=="Open"){
          $totalCount = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                                                LEFT JOIN releaseTracking USING(bugNumber)
                                                LEFT JOIN users ON bugDescriptions.creator=users.username
						WHERE (((((globalState='Assigned' || globalState='Idle')
						AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState)))
						OR (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName')
						OR (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease')) 
						OR (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review')
						AND targetReleaseId='$targetRelease'))) AND ManagerName like '%$mgrName%' GROUP BY ManagerName");

          $sql = "SELECT bugNumber, summary, severity, priority, creator, verifier, releaseDetected, globalState, releaseState from bugDescriptions
                                                LEFT JOIN releaseTracking USING(bugNumber)
                                                LEFT JOIN users ON bugDescriptions.creator=users.username
                                                WHERE (((((globalState='Assigned' || globalState='Idle')
                                                AND releaseDetected='$releaseName' AND (isNull(releaseTracking.releaseState)))
                                                OR (globalState='Assigned Review' || globalState='Study' AND releaseDetected='$releaseName')
                                                OR (globalState='Assigned' AND releaseDetected='$releaseName' AND releaseTracking.targetReleaseId <> '$targetRelease')) 
                                                OR (((releaseState='Assigned' AND globalState='Assigned') || releaseState='Build Pending' || releaseState='Committing' || releaseState='Assigned Review')
                                                AND targetReleaseId='$targetRelease'))) AND ManagerName like '%$mgrName%' GROUP BY bugNumber";
	} elseif ($crState=="Closed"){
          $totalCount = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                                                LEFT JOIN releaseTracking USING(bugNumber)
                                                LEFT JOIN users ON bugDescriptions.creator=users.username
                                                WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released')
						AND targetReleaseId='$targetRelease') OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') 
						AND releaseDetected='$releaseName')) 
						AND managerName like '%$mgrName%' GROUP BY managerName");

          $sql = "SELECT bugNumber, summary, severity, priority, creator, verifier, releaseDetected, globalState, releaseState from bugDescriptions
                                                LEFT JOIN releaseTracking USING(bugNumber)
                                                LEFT JOIN users ON bugDescriptions.creator=users.username
                                                WHERE (((releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released')
                                                AND targetReleaseId='$targetRelease') OR ((globalState =  'Closed' || globalState =  'Duplicate' || globalState =  'No Change' || globalState =  'Deferred') 
                                                AND releaseDetected='$releaseName')) 
                                                AND managerName like '%$mgrName%' GROUP BY bugNumber";
	} else {

	}

        #<!-- Main content -->
        echo "<section class=\"content\">";
          echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Total CRs - $totalCount </h3>";
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
                        echo "<th>Verifier</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                      $result = $conn->query($sql);
                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[verifier]."</td>";
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
                        echo "<th>Verifier</th>";
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
#  Function to display all RDI CRs							#
#                                                                                       #
#########################################################################################
function printRDI($releaseName, $targetRelease) {
include 'template.php';
include 'db_connect.php';
      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "Printing $releaseName - RDI CRs";
            echo "<small></small>";
          echo "</h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";

	$rdiValue = 100;

        #<!-- Main content -->
        echo "<section class=\"content\">";
          echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
              echo "<div class=\"box\">";
                #echo "<div class=\"box-header\">";
                  #echo "<h3 class=\"box-title\">RDI CRs</h3>";
                #echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"example1\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>GA-Blocking</th>";
                        echo "<th>Test-Blocking</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Created By</th>";
                        echo "<th>Manager</th>";
                        echo "<th>Assigned To</th>";
                        echo "<th>Manager</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Release State</th>";
                        echo "<th>Release Name</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

	             #Query-1 - EXOS CRs without Target Release
		     $sql = "SELECT bugDescriptions.bugNumber as bugDescriptions_bugNumber,
			    bugDescriptions.severity as bugDescriptions_severity,
			    bugDescriptions.priority as bugDescriptions_priority,
			    bugDescriptions.gaBlocking as bugDescriptions_gaBlocking,
			    GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseGaBlocking, ')' SEPARATOR '<BR>') AS releaseTracking_releaseGaBlocking , GROUP_CONCAT(DISTINCT bugTestBlocking.testBlocking SEPARATOR ', ') AS bugTestBlocking_testBlocking , If(count(distinct(targetReleaseId))>1, concat(globalState,'*'), globalState) as bugDescriptions_globalState,
			    bugDescriptions.status as bugDescriptions_status,
			    bugDescriptions.creator as bugDescriptions_creator,
			    creatorManagerAlias.userName as bugDescriptions_creatorManager,
			    IF(bugDescriptions.assignedTo != '', bugDescriptions.assignedTo, releaseTracking.assignedTo) as bugDescriptions_assignedTo,
			    IF(bugDescriptions.assignedTo != '', assignedToManagerAlias.userName, assignedToManagerRTAlias.userName) as bugDescriptions_assignedToManager,
			    GROUP_CONCAT(DISTINCT releases.releaseName, ' - ', releaseTracking.assignedTo SEPARATOR '<BR>') AS releaseTracking_assignedTo , bugDescriptions.escalationNumber as bugDescriptions_escalationNumber,
			    bugDescriptions.customerName as bugDescriptions_customerName,
			    bugDescriptions.releaseDetected as bugDescriptions_releaseDetected,
			    releaseTracking.releaseState as releaseTracking_releaseState,
			    GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseState, ')' SEPARATOR '<BR>') AS releases_releaseName ,  bugDescriptions.summary as bugDescriptions_summary
			FROM bugDescriptions
			LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber
			LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId
			LEFT JOIN bugTestBlocking ON bugTestBlocking.bugNumber=bugDescriptions.bugNumber
			LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.userName
			LEFT JOIN users as creatorManagerAlias ON creatorManagerAlias.userName=creatorManager.managerName
			LEFT JOIN users as assignedToManager ON bugDescriptions.assignedTo=assignedToManager.userName
			LEFT JOIN users as assignedToManagerAlias ON assignedToManagerAlias.userName=assignedToManager.managerName
			LEFT JOIN users as assignedToManagerRT ON releaseTracking.assignedTo=assignedToManagerRT.userName
			LEFT JOIN users as assignedToManagerRTAlias ON assignedToManagerRTAlias.userName=assignedToManagerRT.managerName
			WHERE ( bugDescriptions.productFamily = 'xos' )
			   AND ( bugDescriptions.releaseDetected = '$releaseName' )
			   AND ( bugDescriptions.globalState = 'Assigned' )
			   AND ( isNull(releases.releaseName) )
			GROUP BY bugDescriptions.bugNumber";

                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugDescriptions_bugNumber]."</a></td>";
                          echo "<td>".$row[bugDescriptions_summary]."</td>";
                          echo "<td>".$row[bugDescriptions_severity]."</td>";
                          echo "<td>".$row[bugDescriptions_priority]."</td>";
                          echo "<td>".$row[bugDescriptions_gaBlocking]."</td>";
                          echo "<td>".$row[bugTestBlocking_testBlocking]."</td>";
                          echo "<td>".$row[bugDescriptions_globalState]."</td>";
                          echo "<td>".$row[bugDescriptions_creator]."</td>";
                          echo "<td>".$row[bugDescriptions_creatorManager]."</td>";
                          echo "<td>".$row[bugDescriptions_assignedTo]."</td>";
                          echo "<td>".$row[bugDescriptions_assignedToManager]."</td>";
                          echo "<td>".$row[bugDescriptions_releaseDetected]."</td>";
                          echo "<td>".$row[releaseTracking_releaseState]."</td>";
                          echo "<td>".$row[releases_releaseName]."</td>";
                          echo "</tr>";
                         }
                        } else {
                          echo "0 results";
                        }


	             #Query-2 - EXOS CRs with Target Release
                     if ($releaseName == "EXOS 22.6.1") {
                      $preRelease = "EXOS 22.4.1";
                     } elseif ($releaseName == "EXOS 22.5.1") {
                      $preRelease = "EXOS 22.3.1";
                     } elseif ($releaseName == "EXOS 22.4.1") {
                      $preRelease = "EXOS 22.2.1";
                     } elseif ($releaseName == "EXOS 22.3.1") {
                      $preRelease = "EXOS 22.2.1";
                     }

                     $sql = "SELECT bugDescriptions.bugNumber as bugDescriptions_bugNumber,
                         bugDescriptions.severity as bugDescriptions_severity,
                         bugDescriptions.priority as priority,
                         bugDescriptions.gaBlocking as bugDescriptions_gaBlocking,
                         GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseGaBlocking, ')' SEPARATOR '<BR>') AS releaseTracking_releaseGaBlocking , GROUP_CONCAT(DISTINCT bugTestBlocking.testBlocking SEPARATOR ', ') AS bugTestBlocking_testBlocking , If(count(distinct(targetReleaseId))>1, concat(globalState,'*'), globalState) as bugDescriptions_globalState,
                         bugDescriptions.status as bugDescriptions_status,
                         creatorManagerAlias.userName as bugDescriptions_creatorManager,
                         bugDescriptions.creationTimeStamp as bugDescriptions_creationTimeStamp,
                         IF(bugDescriptions.assignedTo != '', bugDescriptions.assignedTo, releaseTracking.assignedTo) as bugDescriptions_assignedTo,
                         IF(bugDescriptions.assignedTo != '', assignedToManagerAlias.userName, assignedToManagerRTAlias.userName) as bugDescriptions_assignedToManager,
                         GROUP_CONCAT(DISTINCT releases.releaseName, ' - ', releaseTracking.assignedTo SEPARATOR '<BR>') AS releaseTracking_assignedTo , bugDescriptions.escalationNumber as bugDescriptions_escalationNumber,
                         bugDescriptions.customerName as bugDescriptions_customerName,
                         bugDescriptions.releaseDetected as bugDescriptions_releaseDetected,
                         releaseTracking.releaseState as releaseTracking_releaseState,
                         GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseState, ')' SEPARATOR '<BR>') AS releases_releaseName , 1 , bugDescriptions.summary as bugDescriptions_summary
                     FROM bugDescriptions
                     LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber
                     LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId
                     LEFT JOIN bugTestBlocking ON bugTestBlocking.bugNumber=bugDescriptions.bugNumber
                     LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.userName
                     LEFT JOIN users as creatorManagerAlias ON creatorManagerAlias.userName=creatorManager.managerName
                     LEFT JOIN users as assignedToManager ON bugDescriptions.assignedTo=assignedToManager.userName
                     LEFT JOIN users as assignedToManagerAlias ON assignedToManagerAlias.userName=assignedToManager.managerName
                     LEFT JOIN users as assignedToManagerRT ON releaseTracking.assignedTo=assignedToManagerRT.userName
                     LEFT JOIN users as assignedToManagerRTAlias ON assignedToManagerRTAlias.userName=assignedToManagerRT.managerName
                     WHERE ( bugDescriptions.productFamily = 'xos' )
                        AND ( bugDescriptions.globalState = 'Assigned'
                        OR bugDescriptions.globalState = 'Assigned Review'
                        OR bugDescriptions.globalState = 'Deferred'
                        OR bugDescriptions.globalState = 'Feedback Needed'
                        OR bugDescriptions.globalState = 'Idle'
                        OR bugDescriptions.globalState = 'Study' )
                        AND ( bugDescriptions.severity <> '5 - New Feature' )
                        AND ( bugDescriptions.priority = '1 - Critical'
                        OR bugDescriptions.priority = '2 - Urgent'
                        OR bugDescriptions.priority = '3 - Important'
                        OR bugDescriptions.priority = '4 - Moderate' )
                        AND ( bugDescriptions.releaseDetected <= '$releaseName' )
                        AND ( bugDescriptions.releaseDetected >= '$preRelease' )
                        AND ( bugDescriptions.releaseDetected <> 'EXOS 16.2.1'
                        AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-falcon'
                        AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-OSPFv3-upgrade'
                        AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-SDK646'
                        AND bugDescriptions.releaseDetected <> 'EXOS 16.2.2'
                        AND bugDescriptions.releaseDetected <> 'EXOS 16.2.2-GA-Jan17'
                        AND bugDescriptions.releaseDetected <> 'EXOS 16.3.1'
                        AND bugDescriptions.releaseDetected <> 'EXOS 16.3.1-linux-318'
                        AND bugDescriptions.releaseDetected <> 'EXOS 17.9.10' )
                        AND NOT ( ( releases.releaseName LIKE '%$releaseName%' )
                        AND ( releaseTracking.releaseState = 'Build Pending'
                        OR releaseTracking.releaseState = 'Committing'
                        OR releaseTracking.releaseState = 'Released'
                        OR releaseTracking.releaseState = 'Task Complete'
                        OR releaseTracking.releaseState = 'Unverified Released'
                        OR releaseTracking.releaseState = 'Verified'
                        OR releaseTracking.releaseState = 'Verify Fix'
                        OR releaseTracking.releaseState = 'Verify Task Complete' ) )
                     GROUP BY bugDescriptions.bugNumber";

                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugDescriptions_bugNumber]."</a></td>";
                          echo "<td>".$row[bugDescriptions_summary]."</td>";
                          echo "<td>".$row[bugDescriptions_severity]."</td>";
                          echo "<td>".$row[bugDescriptions_priority]."</td>";
                          echo "<td>".$row[bugDescriptions_gaBlocking]."</td>";
                          echo "<td>".$row[bugTestBlocking_testBlocking]."</td>";
                          echo "<td>".$row[bugDescriptions_globalState]."</td>";
                          echo "<td>".$row[bugDescriptions_creator]."</td>";
                          echo "<td>".$row[bugDescriptions_creatorManager]."</td>";
                          echo "<td>".$row[bugDescriptions_assignedTo]."</td>";
                          echo "<td>".$row[bugDescriptions_assignedToManager]."</td>";
                          echo "<td>".$row[bugDescriptions_releaseDetected]."</td>";
                          echo "<td>".$row[releaseTracking_releaseState]."</td>";
                          echo "<td>".$row[releases_releaseName]."</td>";
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
                        echo "<th>GA-Blocking</th>";
                        echo "<th>Test-Blocking</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Created By</th>";
                        echo "<th>Manager</th>";
                        echo "<th>Assigned To</th>";
                        echo "<th>Manager</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Release State</th>";
                        echo "<th>Release Name</th>";
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
#  Function to print the  EXOS CR List from Array					#
#                                                                                       #
#########################################################################################
function printCRsArray($arrayName, $crState) {
      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "Printing all $crState CRs";
            echo "<small></small>";
          echo "</h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";

	$crList = unserialize($arrayName);
	$crState="all";

        #<!-- Main content -->
        echo "<section class=\"content\">";
          echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Total CRs - $totalCount </h3>";
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
                        echo "<th>Verifier</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

		      foreach($crList as $row) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[verifier]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "</tr>";
                        }

                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Verifier</th>";
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
} elseif ($_GET[action] == "printCRsArray") {
  session_start();
  $string = $_SESSION['crList'];
  echo "String is $string";
  $arrayName = unserialize($string);
  printCRsArray($arrayName, $_GET[crState]);
} elseif ($_GET[action] == "printClosedCRs") {
  printClosedCRs($_GET[releaseName],$_GET[targetRelease]);
} elseif ($_GET[action] == "printTotalCRs") {
  printTotalCRs($_GET[releaseName],$_GET[targetRelease]);
} elseif ($_GET[action] == "printMgrCRs") {
  printMgrCRs($_GET[releaseName],$_GET[targetRelease],$_GET[mgrName],$_GET[crState]);
} elseif ($_GET[action] == "printRDI") {
  printRDI($_GET[releaseName],$_GET[targetRelease]);
} else { 
}

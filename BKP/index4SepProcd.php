<!DOCTYPE html>
<?php

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
#   <!-- Right side column. Contains the navbar and content of the page -->		#
#       Printing the header text of the page						#
#                                                                                       #
#########################################################################################
function printHeader($releaseName, $targetRelease) {
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1> Release: $releaseName </h1>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=\"index.php\"><i class=\"fa fa-dashboard\"></i> Home</a></li>";
          echo "</ol>";
        echo "</section>";

}

#########################################################################################
#                                                                                       #
#  Procedure to print the RDI BoX							#
#                                                                                       #
#########################################################################################
function printRDI($conn, $releaseName, $targetRelease) {
## Calculating RDI
$sql = "SELECT bugDescriptions.priority as priority, count(bugDescriptions.bugNumber) as priorityCount FROM bugDescriptions  LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber  LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId WHERE ( bugDescriptions.productFamily = 'xos' )    AND ( bugDescriptions.severity <> '5 - New Feature' ) AND ( bugDescriptions.releaseDetected = '$releaseName' )    AND ( bugDescriptions.globalState = 'Assigned' )    AND ( isNull(releases.releaseName) )  GROUP BY priority order by priority ASC";

if ($releaseName == "EXOS 22.6.1") {
 $preRelease = "EXOS 22.4.1";
} elseif ($releaseName == "EXOS 22.5.1") {
 $preRelease = "EXOS 22.3.1";
} elseif ($releaseName == "EXOS 22.4.1") {
 $preRelease = "EXOS 22.2.1";
} elseif ($releaseName == "EXOS 22.3.1") {
 $preRelease = "EXOS 22.2.1";
}


$rdiValue = 0;
$result = $conn->query($sql);
  if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      if($row[priority] == "1 - Critical") {
        $p1Rdi = $row[priorityCount] * 20;
      } elseif ($row[priority] == "2 - Urgent") {
        $p2Rdi = $row[priorityCount] * 15;
      } elseif ($row[priority] == "3 - Important") {
        $p3Rdi = $row[priorityCount] * 5;
      } elseif ($row[priority] == "4 - Moderate") {
        $p4Rdi = $row[priorityCount];
      } else {
      }
     }
   } else {
}

$rdiValue = $p1Rdi + $p2Rdi + $p3Rdi + $p4Rdi;

$sql = "SELECT bugDescriptions.priority as priority, count(bugDescriptions.bugNumber) as priorityCount FROM bugDescriptions LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId WHERE ( bugDescriptions.productFamily = 'xos' ) AND ( bugDescriptions.globalState = 'Assigned' OR bugDescriptions.globalState = 'Assigned Review' OR bugDescriptions.globalState = 'Deferred' OR bugDescriptions.globalState = 'Feedback Needed' OR bugDescriptions.globalState = 'Idle' OR bugDescriptions.globalState = 'Study' ) AND ( bugDescriptions.severity <> '5 - New Feature' ) AND ( bugDescriptions.priority = '1 - Critical' OR bugDescriptions.priority = '2 - Urgent' OR bugDescriptions.priority = '3 - Important' OR bugDescriptions.priority = '4 - Moderate' ) AND ( bugDescriptions.releaseDetected <= '$releaseName' ) AND ( bugDescriptions.releaseDetected >= '$preRelease' ) AND ( bugDescriptions.releaseDetected <> 'EXOS 16.2.1' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-falcon' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-OSPFv3-upgrade' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-SDK646' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.2' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.2-GA-Jan17' AND bugDescriptions.releaseDetected <> 'EXOS 16.3.1' AND bugDescriptions.releaseDetected <> 'EXOS 16.3.1-linux-318' AND bugDescriptions.releaseDetected <> 'EXOS 17.9.10' ) AND NOT ( ( releases.releaseName LIKE '%22.4.1%' ) AND ( releaseTracking.releaseState = 'Build Pending' OR releaseTracking.releaseState = 'Committing' OR releaseTracking.releaseState = 'Released' OR releaseTracking.releaseState = 'Task Complete' OR releaseTracking.releaseState = 'Unverified Released' OR releaseTracking.releaseState = 'Verified' OR releaseTracking.releaseState = 'Verify Fix' OR releaseTracking.releaseState = 'Verify Task Complete' ) ) GROUP BY priority order by priority ASC";

$result = $conn->query($sql);
  if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      if($row[priority] == "1 - Critical") {
        $p1Rdi = $row[priorityCount] * 20;
      } elseif ($row[priority] == "2 - Urgent") {
        $p2Rdi = $row[priorityCount] * 15;
      } elseif ($row[priority] == "3 - Important") {
        $p3Rdi = $row[priorityCount] * 5;
      } elseif ($row[priority] == "4 - Moderate") {
        $p4Rdi = $row[priorityCount];
      } else {
      }
     }
   } else {
}

## Calculating RDI for last three Releases
$rdiValue = $rdiValue + $p1Rdi + $p2Rdi + $p3Rdi + $p4Rdi;

echo "<div class=\"col-lg-3 col-xs-6\">";
 #<!-- small box -->
 echo "<div class=\"small-box bg-aqua\">";
  echo "<div class=\"inner\">";
   echo "<h3>$rdiValue</h3>";
   echo "<p>RDI</p>";
   echo "</div>";
  echo "<div class=\"icon\">";
 echo "<i class=\"ion-ios-pulse\"></i>";
echo "</div>";
 echo "<a href=\"printDetail.php?action=printRDI\" target=\"_blank\" class=\"small-box-footer\">Details <i class=\"fa fa-arrow-circle-right\"></i></a>";
 echo "</div>";
echo "</div><!-- ./col -->";

}

#########################################################################################
#                                                                                       #
#  Procedure to print the Total CRS Box							#
#                                                                                       #
#########################################################################################
function printTotalCRs($conn, $releaseName, $targetRelease) {

$countTotal = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                         LEFT JOIN releaseTracking USING(bugNumber)
                         LEFT JOIN users ON bugDescriptions.creator=users.username
                         WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                         AND (ldapManagerName like '%Velusamy%'      ||
                              ldapManagerName like '%Ramkumar%'      ||
                              ldapManagerName like '%Parthasarathy%' ||
                              ldapManagerName like '%Palanivel%'     ||
                              ldapManagerName like '%Raguraman%')");

            echo "<div class=\"col-lg-3 col-xs-6\">";
              #<!-- small box -->
              echo "<div class=\"small-box bg-yellow\">";
                echo "<div class=\"inner\">";
                  echo "<h3>$countTotal</h3>";
                  echo "<p>Total CRs</p>";
                echo "</div>";
                echo "<div class=\"icon\">";
                  echo "<i class=\"ion ion-stats-bars\"></i>";
                echo "</div>";
                echo "<a href=\"printDetail.php?action=printTotalCRs&releaseName=$releaseName\" target=\"_blank\" class=\"small-box-footer\">Details <i class=\"fa fa-arrow-circle-right\"></i></a>";
              echo "</div>";
            echo "</div><!-- ./col -->";

}

#########################################################################################
#                                                                                       #
#  Procedure to print the Assigned CRS Box                                              #
#                                                                                       #
#########################################################################################
function printOpenCRs($conn, $releaseName, $targetRelease) {

$countAssigned = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE globalState='Assigned' AND (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                            AND (ldapManagerName like '%Velusamy%'      ||
                                 ldapManagerName like '%Ramkumar%'      ||
                                 ldapManagerName like '%Parthasarathy%' ||
                                 ldapManagerName like '%Palanivel%'     ||
                                 ldapManagerName like '%Raguraman%')");

            echo "<div class=\"col-lg-3 col-xs-6\">";
              #<!-- small box -->
              echo "<div class=\"small-box bg-red\">";
                echo "<div class=\"inner\">";
                  echo "<h3>$countAssigned</h3>";
                  echo "<p>Open CRs</p>";
                echo "</div>";
                echo "<div class=\"icon\">";
                  echo "<i class=\"ion ion-pie-graph\"></i>";
                echo "</div>";
                echo "<a href=\"printDetail.php?action=printOpenCRs&releaseName=$releaseName\" target=\"_blank\" class=\"small-box-footer\">Details <i class=\"fa fa-arrow-circle-right\"></i></a>";
              echo "</div>";
            echo "</div><!-- ./col -->";

}


#########################################################################################
#                                                                                       #
#  Procedure to print the Closed CRS Box                                                #
#                                                                                       #
#########################################################################################
function printClosedCRs($conn, $releaseName, $targetRelease) {

$countClosed = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                          LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
                          WHERE releaseState='Verified'
                          AND (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
                          AND (ldapManagerName like '%Velusamy%'      ||
                               ldapManagerName like '%Ramkumar%'      ||
                               ldapManagerName like '%Parthasarathy%' ||
                               ldapManagerName like '%Palanivel%'     ||
                               ldapManagerName like '%Raguraman%')");

            echo "<div class=\"col-lg-3 col-xs-6\">";
              #<!-- small box -->
              echo "<div class=\"small-box bg-green\">";
                echo "<div class=\"inner\">";
                  echo "<h3>$countClosed</h3>";
                  echo "<p>Closed CRs</p>";
                echo "</div>";
                echo "<div class=\"icon\">";
                  echo "<i class=\"ion ion-checkmark\"></i>";
                echo "</div>";
                echo "<a href=\"printDetail.php?action=printClosedCRs&releaseName=$releaseName\" target=\"_blank\" class=\"small-box-footer\">Details <i class=\"fa fa-arrow-circle-right\"></i></a>";
              echo "</div>";
            echo "</div><!-- ./col -->";

}


#########################################################################################
#                                                                                       #
#  Procedure to Print the CR Distribution Table						#
#                                                                                       #
#########################################################################################
function printCRDistributionTable($conn, $releaseName, $targetRelease) {

$mgrName = array
  (
  array("Ramkumar",0,0,0,0),
  array("Raj",0,0,0,0),
  array("Shankar",0,0,0,0),
  array("Raguraman",0,0,0,0),
  array("Uma Parthasarathy",0,0,0,0),
  );

## Loop for populating Total CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value from bugDescriptions LEFT JOIN releaseTracking USING(bugNumber) LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease') AND ldapManagerName like '%$ldapName%' GROUP BY ldapManagerName";
   $mgrName[$row][1] = getDetail($conn, $sql);
  }
}


## Loop for populating Open CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value from bugDescriptions LEFT JOIN releaseTracking USING(bugNumber) LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (globalState='Assigned' || releaseState='Assigned') AND (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease') AND ldapManagerName like '%$ldapName%' GROUP BY ldapManagerName";
   $mgrName[$row][2] = getDetail($conn, $sql);
  }
}

## Loop for populating Closed CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value from bugDescriptions LEFT JOIN releaseTracking USING(bugNumber) LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (releaseState='Verified' || releaseState='Task Complete') AND (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease') AND ldapManagerName like '%$ldapName%' GROUP BY ldapManagerName";
   $mgrName[$row][3] = getDetail($conn, $sql);
  }
}

## Loop for populating Verify-Fix/Feedback Needed CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value FROM      (SELECT bugNumber, targetReleaseId, transitionDate, assignedTo as relassignedTo, releaseState FROM bugShortHistory LEFT JOIN releaseTracking USING(bugNumber) WHERE action='submit' AND targetReleaseId='$targetRelease' AND releaseState='Verify Fix') AS tmpTbl1     LEFT JOIN bugDescriptions USING(bugNumber)       LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.username LEFT JOIN users as verifierManager ON relassignedTo=verifierManager.username WHERE (releaseState='Verify Fix' || globalState='Feedback Needed') AND verifierManager.ldapManagerName like '%$ldapName%' GROUP BY verifierManager.managerName";
   $mgrName[$row][4] = getDetail($conn, $sql);
  }
}


echo "<table class=\"table table-striped\">";
echo "<tr>";
echo "<th>Team</th>";
echo "<th>Total CRs</th>";
echo "<th>Open</th>";
echo "<th>Closed</th>";
echo "<th>Pending</th>";
echo "<th>Progress</th>";
echo "<th style=\"width: 40px\">Percent</th>";
echo "</tr>";

for ($row = 0; $row <= 4; $row++) {
  echo "<tr>";
    for ($col = 0; $col <= 4; $col++) {
      echo "<td>".$mgrName[$row][$col]."</td>";
    }
  echo "<td><div class=\"progress progress-xs\">";
  $progressPercent = ceil(($mgrName[$row][3]/$mgrName[$row][1]) * 100);
  echo "<div class=\"progress-bar progress-bar-success\" style=\"width: $progressPercent% \"></div>";
  echo "</div> </td>";
  echo "<td><span class=\"badge bg-red\">$progressPercent%</span></td>";
  echo "</tr>";
  }
  echo "</table>";
}


#########################################################################################
#                                                                                       #
#  Procedure to Print the Severity Vs Priority Table					#
#                                                                                       #
#########################################################################################
function printSeverityPriorityTable($conn, $releaseName, $targetRelease) {
echo "<table class=\"table table-striped\">";
echo "<tr>";
echo "<th>Priority</th>";
echo "<th>1-Crash</th>";
echo "<th>2-Major</th>";
echo "<th>3-Minor</th>";
echo "<th>4-Trivial</th>";
echo "<th>5-New Feature</th>";
echo "<th>Total</th>";
echo "</tr>";

$sql = "select priority,  coalesce(sum(severity like '1 - Crash'),0)  Crash,  coalesce(sum(severity like '2 - Major'),0) Major,   coalesce(sum(severity like '3 - Minor'),0)  Minor,  coalesce(sum(severity like '4 - Trivial'),0)  Trivial,  coalesce(sum(severity like '5 - New Feature'),0)  NewFeature, count(priority) as totalCount from bugDescriptions   where releaseDetected='$releaseName'  group by priority";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row[priority]."</td>";
    echo "<td>".$row[Crash]."</td>";
    echo "<td>".$row[Major]."</td>";
    echo "<td>".$row[Minor]."</td>";
    echo "<td>".$row[Trivial]."</td>";
    echo "<td>".$row[NewFeature]."</td>";
    echo "<td>".$row[totalCount]."</td>";
    echo "</tr>";
  }
} else {
 echo "<tr><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>";
}
echo "</table>";
}


#########################################################################################
#                                                                                       #
#  Procedure to Print the Top 5 Component Failure					#
#                                                                                       #
#########################################################################################
function printTopComponent($conn, $releaseName, $targetRelease) {

$totCurrentReleaseCRs = getDetail($conn, "select count(*) as value from bugDescriptions where releaseDetected='$releaseName'");

echo "<table class=\"table table-striped\">";
echo "<tr>";
echo "<th>Component</th>";
echo "<th>Total CRs</th>";
echo "<th style=\"width: 40px\">Percent</th>";
echo "</tr>";

$sql = "select component, count(component) as count  from bugDescriptions  where releaseDetected='$releaseName' group by component order by count(component) desc limit 5";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row[component]."</td>";
    echo "<td>".$row[count]."</td>";
    $progressPercent = ceil(($row[count]/$totCurrentReleaseCRs) * 100);
    echo "<td><span class=\"badge bg-red\">$progressPercent%</span></td>";
    echo "</tr>";
  }
} else {
 echo "<tr><td>0</td><td>0</td><td>0</td><td>0</td></tr>";
}
echo "</table>";
}


#########################################################################################
#                                                                                       #
#  Procedure to Print the Top 5 Sub-Component Failure                                   #
#                                                                                       #
#########################################################################################
function printTopSubComponent($conn, $releaseName, $targetRelease) {

$totCurrentReleaseCRs = getDetail($conn, "select count(*) as value from bugDescriptions where releaseDetected='$releaseName'");
	
echo "<table class=\"table table-striped\">";
echo "<tr>";
echo "<th>Component</th>";
echo "<th>Total CRs</th>";
echo "<th style=\"width: 40px\">Percent</th>";
echo "</tr>";

$sql = "select subcomponent, count(subcomponent) as count  from bugDescriptions  where releaseDetected='$releaseName' group by subcomponent order by count(subcomponent) desc limit 5";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row[subcomponent]."</td>";
    echo "<td>".$row[count]."</td>";
    $progressPercent = ceil(($row[count]/$totCurrentReleaseCRs) * 100);
    echo "<td><span class=\"badge bg-red\">$progressPercent%</span></td>";
    echo "</tr>";
  }
} else {
 echo "<tr><td>0</td><td>0</td><td>0</td><td>0</td></tr>";
}
echo "</table>";
}



#########################################################################################
#                                                                                       #
#  Procedure to print the Overall HTML Page						#
#                                                                                       #
#########################################################################################
function main($releaseName) {
include 'template.php';
include 'db_connect.php';
echo "<html>";
echo $headInclude;
echo $topBar;
echo $leftMenu;

$targetRelease = getDetail($conn, "SELECT releaseId as value FROM releases WHERE productId=132 AND releaseName='$releaseName'");

printHeader($releaseName);
        #<!-- Top Boxes for CR overall count -->
        echo "<section class=\"content\">";
          #<!-- Small boxes (Stat box) -->
          echo "<div class=\"row\">";
            printRDI($conn,$releaseName,$targetRelease);
            printTotalCRs($conn,$releaseName,$targetRelease);
            printOpenCRs($conn,$releaseName,$targetRelease);
            printClosedCRs($conn,$releaseName,$targetRelease);
          echo "</div><!-- /.row -->";


          #<!-- Main Graphs and Data coded here after the header cards -->
          #<!-- Printing the CR Distrubtion Table -->
          echo "<div class=\"row\">";
            #<!-- Left col -->";
            echo "<section class=\"col-lg-7 connectedSortable\">";
		#<!--    Right side Frame Starts. -->";
              echo "<div class=\"box box-primary\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">CR Distribution</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
   		  printCRDistributionTable($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";

          #<!-- Printing the Priority Vs Severity Table -->
              echo "<div class=\"box box-info\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Priority Vs Severity</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
	        printSeverityPriorityTable($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</section><!-- /.Left col -->";

          #<!-- Printing the Top 5 Component Table -->
            #<!-- right col (We are only adding the ID to make the widgets sortable)-->
            echo "<section class=\"col-lg-5 connectedSortable\">";
		    #<!-- Left side Frame starts -->";
              echo "<div class=\"box box-danger\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Top 5 Component</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
   		  printTopComponent($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";

          #<!-- Printing the Top 5 Sub Component Table -->
              echo "<div class=\"box box-success\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Top 5 Sub-Component</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
   		  printTopSubComponent($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";

            echo "</section><!-- /.content -->";
      echo "</div><!-- /.content-wrapper -->";
    echo "</div><!-- ./wrapper -->";

echo $scriptInclude;
echo "</body>";
echo "</html>";
}

#########################################################################################
#                                                                                       #
# Default EXOS Set to EXOS 22.4.1, otherwise clicked different link			#
#                                                                                       #
#########################################################################################
if (!$_GET[trackRelease]) {
      main("EXOS 22.4.1");
} else {
      main($_GET[trackRelease]); 
}
?>

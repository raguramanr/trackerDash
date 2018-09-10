<?php
#########################################################################################
#                                                                                       #
#  Global Variable for Today
#                                                                                       #
#########################################################################################
$today = date("Y-m-d");
$GLOBALS['keyMapName'] = array("EXOS 22.1.1"=>"New Feature 22.1", "EXOS 22.2.1"=>"New Feature 22.2", "EXOS 22.3.1"=>"New Feature 22.3", "EXOS 22.3.1 patch"=>"New features 22.3.1 patch", "EXOS 22.4.1"=>"New Feature 22.4", "EXOS 22.5.1"=>"New Feature 22.5", "EXOS 22.6.1"=>"New Feature 22.6", "EXOS 30.1.1"=>"New Feature 30.1", "EXOS 30.2.1"=>"New Feature 30.2", "EXOS 30.3.1"=>"New Feature 30.3");

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
# Function to write and download the file as CSV                                        #
#                                                                                       #
#########################################################################################
function downloadCSV($arrayName) {
$fileName = 'CR-Summary.csv';

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$fileName}");
header("Expires: 0");
header("Pragma: public");

$fh = @fopen( 'php://output', 'w' );

$headerDisplayed = false;

foreach ( $arrayName as $data ) {
    if ( !$headerDisplayed ) {
        fputcsv($fh, array_keys($data));
        $headerDisplayed = true;
    }

    fputcsv($fh, $data);
}
fclose($fh);
exit;
}


#########################################################################################
#                                                                                       #
#   <!-- Right side column. Contains the navbar and content of the page -->		#
#       Printing the header text of the page						#
#                                                                                       #
#########################################################################################
function printHeader($releaseName, $targetRelease, $crList) {
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          #echo "<h1> Release: $releaseName </h1>";
          #echo "<ol class=\"breadcrumb\">";
          #echo "<li><a href=\"downloadExcel.php?getReport=allCRs&releaseName=$releaseName&targetRelease=$targetRelease\"><i class=\"fa fa-dashboard\"></i>Download</a></li>";
          #echo "</ol>";
        echo "</section>";

}

#########################################################################################
#                                                                                       #
# Procedure to print the RDI BoX - Query ID 14283 - Excluded VPEX condition             #
#                                                                                       #
#########################################################################################
function printRDI($conn, $releaseName, $targetRelease) {
$rdiValue = 0;
$p1Rdi = 0;
$p2Rdi = 0;
$p3Rdi = 0;
$p4Rdi = 0;

if ($releaseName == "EXOS 22.3.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.1.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.2.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.3.1'";
} elseif ($releaseName == "EXOS 22.4.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.1.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.2.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.3.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.4.1'";
} elseif ($releaseName == "EXOS 22.5.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.3.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.4.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.5.1'";
} elseif ($releaseName == "EXOS 22.6.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.3.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.4.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.5.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.6.1'";
} elseif ($releaseName == "EXOS 22.7.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.5.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.6.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 30.1.1' 
   		OR bugDescriptions.releaseDetected = 'EXOS 22.7.1'";
} elseif ($releaseName == "EXOS 30.1.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.4.1' 
		OR bugDescriptions.releaseDetected = 'EXOS 22.5.1' 
		OR bugDescriptions.releaseDetected = 'EXOS 22.6.1' 
		OR bugDescriptions.releaseDetected = 'EXOS 30.1.1'";
} elseif ($releaseName == "EXOS 30.2.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.5.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.6.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.7.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.1.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.2.1'";
} elseif ($releaseName == "EXOS 30.3.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 30.1.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.2.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.3.1'";
} elseif ($releaseName == "EXOS 30.4.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 30.2.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.3.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.4.1'";
} elseif ($releaseName == "EXOS 30.5.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 30.3.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.4.1'
                OR bugDescriptions.releaseDetected = 'EXOS 30.5.1'";
}

$sql = "select (bugDescriptions_priority*1) as priority, count(bugDescriptions_priority) as priorityCount from (
select * from ( SELECT bugDescriptions.bugNumber as bugDescriptions_bugNumber,
    bugDescriptions.severity as bugDescriptions_severity,
    bugDescriptions.priority as bugDescriptions_priority,
    If(count(distinct(targetReleaseId))>1, concat(globalState,'*'), globalState) as bugDescriptions_globalState,
    bugDescriptions.creationTimeStamp as bugDescriptions_creationTimeStamp,
    IF(bugDescriptions.assignedTo != '', bugDescriptions.assignedTo, releaseTracking.assignedTo) as bugDescriptions_assignedTo,
    IF(bugDescriptions.assignedTo != '', IF(assignedToManagerAlias.userName != '', assignedToManagerAlias.userName, assignedToManager.managerName ), IF(assignedToManagerRTAlias.userName != '', assignedToManagerRTAlias.userName, assignedToManagerRT.managerName )) as bugDescriptions_assignedToManager,
    bugDescriptions.releaseDetected as bugDescriptions_releaseDetected,
    GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseState, ')' SEPARATOR '<BR>') AS releases_releaseName , bugDescriptions.summary as bugDescriptions_summary 
FROM bugDescriptions 
LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber 
LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId 
LEFT JOIN users as assignedToManager ON bugDescriptions.assignedTo=assignedToManager.userName 
LEFT JOIN users as assignedToManagerAlias ON assignedToManagerAlias.userName=assignedToManager.managerName 
LEFT JOIN users as assignedToManagerRT ON releaseTracking.assignedTo=assignedToManagerRT.userName 
LEFT JOIN users as assignedToManagerRTAlias ON assignedToManagerRTAlias.userName=assignedToManagerRT.managerName 
WHERE ( bugDescriptions.productFamily = 'xos' )
   AND ( bugDescriptions.globalState = 'Assigned' )
   AND ( 
   $sqlCondition
   )
   AND ( bugDescriptions.severity <> '5 - New Feature' ) 
GROUP BY bugDescriptions.bugNumber) t where (releases_releaseName not like '%Verified%' and releases_releaseName not like '%Released%') or isNull(releases_releaseName))
as t1 group by bugDescriptions_priority order by bugDescriptions_priority ASC";

$result = $conn->query($sql);
  if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      if($row[priority] == "1") {
        $p1Rdi = $row[priorityCount] * 20;
      } elseif ($row[priority] == "2") {
        $p2Rdi = $row[priorityCount] * 15;
      } elseif ($row[priority] == "3") {
        $p3Rdi = $row[priorityCount] * 5;
      } elseif ($row[priority] == "4") {
        $p4Rdi = $row[priorityCount];
      } else {
      }
     }
   } else {
}

$rdiValue = $p1Rdi + $p2Rdi + $p3Rdi + $p4Rdi;

echo "<div class=\"col-md-2 col-xs-6\">";
 echo "<div class=\"small-box bg-aqua\">";
  echo "<div class=\"inner\">";
   echo "<h3>$rdiValue</h3>";
   echo "<p>RDI<p>";
   #echo "<p>VPEX - $rdiVPEX </p>";
   echo "</div>";
  echo "<div class=\"icon\">";
  echo "<i class=\"ion-ios-pulse\"></i>";
  echo "</div>";
 echo "</div>";
echo "</div><!-- ./col -->";

}

#########################################################################################
#                                                                                       #
#  Procedure to print Line Chart for Incoming CRs - Fusion Charts                       #
#                                                                                       #
#########################################################################################
function printLineIncomingCR($conn, $releaseName, $targetRelease) {

$sql = "select count(bugNumber) as total, DATE_FORMAT(creationTimeStamp,'%b-%y') as month from bugDescriptions  WHERE releaseDetected='$releaseName' GROUP BY DATE_FORMAT(creationTimeStamp,'%b/%Y') ORDER BY creationTimeStamp asc";
$configs = array();

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
   $configs[] = $row;
  }
} else {
}
 return $configs;
}

#########################################################################################
#                                                                                       #
#  Procedure to print Line Chart for Fixed CRs - Fusion Charts 	                        #
#                                                                                       #
#########################################################################################
function printLineFixedCR($conn, $releaseName, $targetRelease) {

$sql = "select count(bugNumber) as total, DATE_FORMAT(transitionDate,'%b-%y') as month from (SELECT bugNumber, targetReleaseId, transitionDate, releaseState FROM bugShortHistory LEFT JOIN releaseTracking USING(bugNumber) WHERE action='approveFix' AND targetReleaseId='$targetRelease' AND (releaseState='Verified' || releaseState='Task Complete' || releaseState='Released' || releaseState='Unverified Released') group by bugNumber) as t1 LEFT JOIN bugDescriptions USING(bugNumber) group by DATE_FORMAT(transitionDate,'%Y-%m')"; 

$configs = array();

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
   $configs[] = $row;
  }
} else {
}
 return $configs;
}

######################################################################
# Stores the last 7 days in array and returns
######################################################################
function getLastNDays($days, $format = 'Y-m-d'){
    $m = date("m"); $de= date("d"); $y= date("Y");
    $dateArray = array();
    for($i=0; $i<=$days; $i++){
        $dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y));
    }   
    $temp = array_reverse($dateArray);
    $value = array_slice($temp, 0, 7, true);
    return $value;
}   

#########################################################################################
#                                                                                       #
#  Function to return the complete List of specified release CRs as array		#
#                                                                                       #
#########################################################################################
function getCRList($conn, $releaseName, $targetRelease) {

$keyMap = $GLOBALS['keyMapName'];
$metadataKeyMapId = getDetail($conn, "select metadataKeyMapId as value from metadataKeyMap where keyName like '%$keyMap[$releaseName]%'");

$sql = "SELECT bugDescriptions.bugNumber, summary, severity, priority, GROUP_CONCAT(DISTINCT bugTestBlocking.testBlocking SEPARATOR ', ') AS testBlocking,
globalState, component, subComponent, releaseState, releaseDetected, targetReleaseId, creator, verifier,
releaseTracking.assignedTo, creatorManager.ldapManagerName as creatorManager, verifierManager.ldapManagerName as verifierManager,
udf1.features as udfFeature, udf2.passedPreviously as passedPreviously, udf3.lastPassBuild as lastPassBuild,
MAX(IF(metadata.metadataKeyMapId='$metadataKeyMapId', metadata.value, '')) as metaData, MAX(IF(metadataKeyMap.metadataKeyMapId='$metadataKeyMapId', metadataKeyMap.keyName, '')) keyName,
udf4.reportedByCustomer as reportedByCustomer, CAST(bugDescriptions.creationTimeStamp AS DATE) as creationTimeStamp
from bugDescriptions
LEFT JOIN releaseTracking USING(bugNumber)
LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.username
LEFT JOIN users as verifierManager ON releaseTracking.assignedTo=verifierManager.username
LEFT JOIN bugTestBlocking          ON bugTestBlocking.bugNumber=bugDescriptions.bugNumber
LEFT JOIN udfFeatures         udf1 ON udf1.bugNumber=bugDescriptions.bugNumber
LEFT JOIN udfPassedPreviously udf2 ON udf2.bugNumber=bugDescriptions.bugNumber
LEFT JOIN udfLastPassBuild    udf3 ON udf3.bugNumber=bugDescriptions.bugNumber
LEFT JOIN udfReportedByCustomer  udf4 ON udf4.bugNumber=bugDescriptions.bugNumber
LEFT JOIN metadata ON metadata.typeId=bugDescriptions.bugNumber
LEFT JOIN metadataKeyMap ON metadataKeyMap.metadataKeyMapId=metadata.metadataKeyMapId
WHERE (releaseDetected='$releaseName'  || targetReleaseId='$targetRelease')
GROUP BY bugNumber";

$value = array();
$result = $conn->query($sql);

$userCount=0;
if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
 $globalState  = $row["globalState"];
 $releaseState = $row["releaseState"];
 $releaseDetected = $row["releaseDetected"];
 $targetReleaseId = $row["targetReleaseId"];

        if((($globalState=="Assigned" || $globalState=="Assigned Review" || $globalState=="Build Pending" || $globalState=="Committing" || $globalState=="Idle" || $globalState=="Study") && (is_null($targetReleaseId))) || ((($releaseState=="Assigned" && ($globalState!="Feedback Needed" && $globalState!="Verify No Change" && $globalState!="Verify Duplicate")) || $releaseState=="Assigned Review" || $releaseState=="Build Pending" || $releaseState=="Committing") && $targetReleaseId==$targetRelease)) {
                @$row['crState']="open";
        } elseif ((($globalState=="Closed" || $globalState=="Deferred" || $globalState=="Duplicate" || $globalState=="No Change" || $globalState=="Released" || $globalState=="Unverified Released" || $globalState=="Verified" || $globalState=="Task Complete") && $targetReleaseId=="") || (($releaseState=="Verified" || $releaseState=="Released" || $releaseState=="Unverified Released" || $releaseState=="Task Complete") && $targetReleaseId==$targetRelease)) {
                @$row['crState']="closed";
        } elseif ((($globalState=="Feedback Needed" || $globalState=="Verify Duplicate" || $globalState=="Verify Fix" || $globalState=="Verify No Change" || $globalState=="Verify Task Complete") && ($targetReleaseId=="" || $targetReleaseId==$targetRelease)) || (($releaseState=="Verify Fix" || $releaseState=="Verify Task Complete") && $targetReleaseId==$targetRelease)) {
                @$row['crState']="sqaPending";
        } elseif ($targetReleaseId!=$targetRelease && $targetReleaseId!="") {
			if($globalState=="Assigned" || $globalState=="Assigned Review" || $globalState=="Build Pending" || $globalState=="Committing" || $globalState=="Idle" || $globalState=="Study") {
				@$row['crState']="open";
			} elseif ($globalState=="Closed" || $globalState=="Deferred" || $globalState=="Duplicate" || $globalState=="No Change" || $globalState=="Released" || $globalState=="Unverified Released" || $globalState=="Verified" || $globalState=="Task Complete") {
				@$row['crState']="closed";
			} elseif ($globalState=="Feedback Needed" || $globalState=="Verify Duplicate" || $globalState=="Verify Fix" || $globalState=="Verify No Change" || $globalState=="Verify Task Complete") {
                		@$row['crState']="sqaPending";
			} else {
                		@$row['crState']="futureScope";
			}
        } else {
                $myState="Uncategorized";
                @$row['crState']="unCategorized";
        }
      $value[] = $row;
   }
} else {
    echo "0 users found";
}
return $value;
$conn->close();
}



#########################################################################################
#                                                                                       #
# Function to get the SQA Pending CRs including transition dates			#
#                                                                                       #
#########################################################################################
function getSQAPendingList($conn, $releaseName, $targetRelease) {

$sql = "SELECT * from (SELECT bugDescriptions.bugNumber as bugNumber, bugDescriptions.severity as severity, bugDescriptions.priority as priority, bugDescriptions.globalState as globalState, NULL as releaseState, bugDescriptions.creator as creator,ldapManagerName,
       bugDescriptions.assignedTo, DATE_FORMAT(MAX(bugShortHistory.transitionDate),'%e/%b/%Y') AS transitiondate,
       DATE(NOW()) as today, DATEDIFF(NOW(), MAX(bugShortHistory.transitionDate)) AS numDays,
bugRelationships.bugNumber as relatedCRID, bugDescriptions1.globalState as relatedCRGlobalState, releaseTracking1.releaseState as relatedCRState
FROM bugDescriptions
LEFT JOIN bugShortHistory USING(bugNumber)
LEFT JOIN releaseTracking USING(bugNumber)
LEFT JOIN users ON bugDescriptions.creator=users.username
LEFT JOIN bugRelationships ON bugDescriptions.bugNumber=bugRelationships.relatedTo
LEFT join bugDescriptions bugDescriptions1 on bugDescriptions1.bugNumber=bugRelationships.bugNumber
LEFT join releaseTracking releaseTracking1 on releaseTracking1.bugNumber=bugRelationships.bugNumber AND releaseTracking1.targetReleaseId='$targetRelease'
WHERE (bugDescriptions.globalState='Feedback Needed' || bugDescriptions.globalState='Verify Duplicate' || bugDescriptions.globalState='Verify No Change')
AND (bugDescriptions.releaseDetected='$releaseName' || releaseTracking.targetReleaseId=$targetRelease)
AND (ldapManagerName like '%Velusamy%'      ||
     ldapManagerName like '%Ramkumar%'      ||
     ldapManagerName like '%Parthasarathy%' ||
     ldapManagerName like '%Thuravupala%'   ||
     ldapManagerName like '%Palanivel%'     ||
     ldapManagerName like '%Raguraman%')
GROUP BY bugDescriptions.bugNumber
UNION
SELECT bugNumber, severity, priority, globalState, releaseState, creator, verifierManager.ldapManagerName as ldapManagerName, relassignedTo as assignedTo,  DATE_FORMAT(MAX(transitionDate),'%e/%b/%Y') AS transitiondate,
       DATE(NOW()) as today, DATEDIFF(NOW(), MAX(transitionDate)) AS numDays,
       NULL as relatedCRID, NULL as relatedCRGlobalState, NULL as relatedCRState
       FROM
         (SELECT bugNumber, targetReleaseId, transitionDate, assignedTo as relassignedTo, releaseState
               FROM bugShortHistory
               LEFT JOIN releaseTracking USING(bugNumber)
               WHERE targetReleaseId=$targetRelease
               AND (releaseState='Verify Fix' || releaseState='Verify Task Complete')) AS tmpTbl1
LEFT JOIN bugDescriptions USING(bugNumber)
LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.username
LEFT JOIN users as verifierManager ON relassignedTo=verifierManager.username
WHERE (releaseState='Verify Fix' || releaseState='Verify Task Complete')
AND (verifierManager.ldapManagerName like '%Velusamy%'      ||
     verifierManager.ldapManagerName like '%Ramkumar%'      ||
     verifierManager.ldapManagerName like '%Parthasarathy%' ||
     verifierManager.ldapManagerName like '%Thuravupala%'   ||
     verifierManager.ldapManagerName like '%Palanivel%'     ||
     verifierManager.ldapManagerName like '%Raguraman%')    
GROUP BY bugNumber) as tmp
order by ldapManagerName asc, globalState asc, numDays desc";

$value = array();
$result = $conn->query($sql);

$userCount=0;
if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      $value[] = $row;
     }
} else {
    echo "0 records found";
}
return $value;
$conn->close();
}

#########################################################################################
#                                                                                       #
# Function to add specified days to the given date					#
#                                                                                       #
#########################################################################################
function addDayswithdate($date,$days){
    $date = strtotime("+".$days." days", strtotime($date));
    return  date("d/M/Y", $date);
}


#########################################################################################
#                                                                                       #
# Function to print Priority Distribution Pie Chart Data for specified release CRs      #
#                                                                                       #
#########################################################################################
function printCQGassignedToDistribution($cqgList, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='Assigned CRs' showLabels='1' showValues='1' subcaption='' palettecolors='#4D4D4D,#5DA5DA,#FAA43A,#60BD68,#F17CB0,#B2912F,#B276B2,#DECF3F,#F15854,#F31B1B,#F39C12,#5298C6,#00a65a,#B969CB,#605CA8,#00C0EF,#3C3C3C,#7722CC,#7F5454,#547F7F,#0075c2,#0000FF' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='1' decimals='1' captionfontsize='14' subcaptionfontsize='14' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";

foreach ($cqgList as $key=>$value) {
        $strXML .= "<set label='" . $key. "' value='" . $cqgList[$key]. "' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "pie2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}


#########################################################################################
#                                                                                       #
# Function to print CQG RBC Root Cause Distribution for last one year			#
#                                                                                       #
#########################################################################################
function printCQGrcDistribution($cqgList, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);

$month = time();
$months[] = date("M-Y", $month);
for ($i = 1; $i <= 12; $i++) {
  $month = strtotime('last month', $month);
  $months[] = date("M-Y", $month);
}
$reversedMonth = array_reverse($months);

$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='Root Cause Distribution - Last 12 months' showLabels='1' showValues='1' subcaption='' palettecolors='#4D4D4D,#5DA5DA,#FAA43A,#60BD68,#F17CB0,#B2912F,#B276B2,#DECF3F,#F15854,#F31B1B,#F39C12,#5298C6,#00a65a,#B969CB,#605CA8,#00C0EF,#3C3C3C,#7722CC,#7F5454,#547F7F,#0075c2,#0000FF' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='1' decimals='1' captionfontsize='14' subcaptionfontsize='14' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";

foreach($cqgList as $data) {
  if($data['globalState'] == "Closed" && (in_array($data['transitionDate'], $reversedMonth))) {
        @$cqgRCACount[$data['rbcRootCause']]++;
  }
}
arsort($cqgRCACount);

foreach ($cqgRCACount as $key=>$value) {
        $strXML .= "<set label='" . $key. "' value='" . $value. "' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "pie2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}


#########################################################################################
#                                                                                       #
# Function to Find Vs Fix table for CQG CRs	                                        #
#                                                                                       #
#########################################################################################
function printCQGfindVsfix($cqgList, $chartID) {
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");

$month = time();
$months[] = date("M-Y", $month);
for ($i = 1; $i <= 12; $i++) {
  $month = strtotime('last month', $month);
  $months[] = date("M-Y", $month);
}
$reversedMonth = array_reverse($months);


$chart_open = "<chart caption='Last 12 months  - Created Vs Closed' subcaption='' captionfontsize='14' subcaptionfontsize='14' basefontcolor='#333333' yAxisMinValue='0' basefont='Helvetica Neue,Arial' subcaptionfontbold='0' xaxisname='' yaxisname='' showvalues='1' palettecolors='#0075c2,#1aaf5d' bgcolor='#ffffff' showborder='0' showshadow='0' showalternatehgridcolor='0' showcanvasborder='0' showxaxisline='1' xaxislinethickness='1' xaxislinecolor='#999999' canvasbgcolor='#ffffff' legendborderalpha='0' legendshadow='0' divlinealpha='50' divlinecolor='#999999' divlinethickness='1' divlinedashed='1' divlinedashlen='1'>\n";
$category_open = "<categories >\n";
$category_close = "</categories>\n";
$data_set_close= "</dataset>\n";
$chart_close= "</chart>\n";
$dataSet_HDR[Find] = "<dataset seriesName='Created' color='B22222' anchorBorderColor='B22222' anchorBgColor='B22222'>\n";
$dataSet_HDR[Fix] = "<dataset seriesName='Closed' color='006400' anchorBorderColor='006400' anchorBgColor='006400'>\n";


foreach($cqgList as $data) {
        @$cqgCreated[$data['createdDate']]++;
	@$cqgFixed[$data['transitionDate']]++;
}

foreach($reversedMonth as $month) {
        $x_axis_month = $x_axis_month."<category label='$month' />\n";
        $dataFind = $dataFind."<set value='$cqgCreated[$month]' />\n";
        $dataFix  = $dataFix."<set value='$cqgFixed[$month]' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $category_open);
fwrite($fh, $x_axis_month);
fwrite($fh, $category_close);
fwrite($fh, $dataSet_HDR[Find]);
fwrite($fh, $dataFind);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Fix]);
fwrite($fh, $dataFix);
fwrite($fh, $data_set_close);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "msspline",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print the Average closure time for CRs by Priority			#
#                                                                                       #
#########################################################################################
function printCQGClosureTime($cqgList, $chartID) {
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$noMonths = 5;

$month = time();
$months[] = date("M-Y", $month);
for ($i = 1; $i <= $noMonths; $i++) {
  $month = strtotime('last month', $month);
  $months[] = date("M-Y", $month);
}
$reversedMonth = array_reverse($months);


$chart_open = "<chart caption='SLA Compliance per Priority' subcaption='' captionfontsize='14' subcaptionfontsize='14' basefontcolor='#333333' yAxisMinValue='0' basefont='Helvetica Neue,Arial' subcaptionfontbold='0' xaxisname='' yaxisname='' showvalues='1' palettecolors='#D81B60,#605CA8,#00A65A' bgcolor='#ffffff' showborder='0' showshadow='0' showalternatehgridcolor='0' showcanvasborder='0' showxaxisline='1' xaxislinethickness='1' xaxislinecolor='#999999' canvasbgcolor='#ffffff' legendborderalpha='0' legendshadow='0' divlinealpha='50' divlinecolor='#999999' divlinethickness='1' divlinedashed='1' divlinedashlen='1'>\n";
$category_open = "<categories >\n";
$category_close = "</categories>\n";
$data_set_close= "</dataset>\n";
$chart_close= "</chart>\n";
$trendlineopen =   " <trendlines> \n";
$p1Trend  =    "<line startvalue=\"30\" color=\"#D81B60\" valueonright=\"1\" tooltext=\"P1/P2 - Internal SLA \" displayvalue=\"P1/P2 - 30\" />";
$p2Trend  =    "<line startvalue=\"45\" color=\"#605CA8\" valueonright=\"1\" tooltext=\"P3 - Internal SLA \" displayvalue=\"P3 - 45\" />";
$p3Trend  =    "<line startvalue=\"60\" color=\"#00A65A\" valueonright=\"1\" tooltext=\"P4/P5 - Internal SLA \" displayvalue=\"P4/P5 - 60\" />";
$trendlineClose =   " </trendlines> \n";
$dataSet_HDR[p1p2] = "<dataset seriesName='P1/P2' color='D81B60' anchorBorderColor='D81B60' anchorBgColor='D81B60'>\n";
$dataSet_HDR[p3]   = "<dataset seriesName='P3'    color='605CA8' anchorBorderColor='605CA8' anchorBgColor='605CA8'>\n";
$dataSet_HDR[p4p5] = "<dataset seriesName='P4/P5' color='00A65A' anchorBorderColor='00A65A' anchorBgColor='00A65A'>\n";


foreach($cqgList as $data) {
  if($data['globalState'] == "Closed") {
        @$cqgClosed[$data['transitionDate']][$data['priority']] = $cqgClosed[$data['transitionDate']][$data['priority']] + $data['numDays'];
        @$cqgPriCount[$data['transitionDate']][$data['priority']]++;
  }
}

foreach($reversedMonth as $month) {
        $p1p2numDays = $cqgClosed[$month]['1 - Critical']  + $cqgClosed[$month]['2 - Urgent'];
	$p1p2numCRs  = $cqgPriCount[$month]['1 - Critical'] + $cqgPriCount[$month]['2 - Urgent'];
	$p1p2Average = ceil($p1p2numDays / $p1p2numCRs);
   
        $p3Average   = ceil($cqgClosed[$month]['3 - Important'] / $cqgPriCount[$month]['3 - Important']);

        $p4p5numDays = $cqgClosed[$month]['4 - Moderate']  + $cqgClosed[$month]['5 - Low'];
	$p4p5numCRs  = $cqgPriCount[$month]['4 - Moderate'] + $cqgPriCount[$month]['5 - Low'];
	$p4p5Average = ceil($p4p5numDays / $p4p5numCRs);

 
        $x_axis_month = $x_axis_month."<category label='$month' />\n";
        $datap1p2 = $datap1p2."<set value='$p1p2Average' />\n";
        $datap3   = $datap3."<set value='$p3Average' />\n";
        $datap4p5 = $datap4p5."<set value='$p4p5Average' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $category_open);
fwrite($fh, $x_axis_month);
fwrite($fh, $category_close);
fwrite($fh, $trendlineopen);
fwrite($fh, $p1Trend);
fwrite($fh, $p2Trend);
fwrite($fh, $p3Trend);
fwrite($fh, $trendlineClose);
fwrite($fh, $dataSet_HDR[p1p2]);
fwrite($fh, $datap1p2);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[p3]);
fwrite($fh, $datap3);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[p4p5]);
fwrite($fh, $datap4p5);
fwrite($fh, $data_set_close);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "msspline",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print the CQG List                                                        #
#                                                                                       #
#########################################################################################
function printCQGTable($conn) {
include 'template.php';
echo "<html>";
echo $headInclude;
echo $dataTableHeader;
$filterScript = <<<HTML
        <script type="text/javascript" class="init">
        $(document).ready(function() {
                $('#example').DataTable( {
			"order": [[ 10, "asc" ]],
                        "bPaginate": false,
                        "colReorder": true,
                        dom: 'Bfrtip',
                        buttons: [
                                'copyHtml5',
                                'excelHtml5'
                        ]
                } );
        } );
        </script>

        <script type="text/javascript" class="init">
        $(document).ready(function() {
                $('#cqgDistribution').DataTable( {
			"order": [[ 1, "desc" ]],
                        "bPaginate": false,
                        "colReorder": true,
                        dom: 'Bfrtip',
                        buttons: [
                                'copyHtml5',
                                'excelHtml5'
                        ]
                } );
        } );
        </script>

HTML;
echo $filterScript;
echo $topBarwoSearch;
$dummy = 0;

$sql = "SELECT bugDescriptions.bugNumber as bugDescriptions_bugNumber,
       bugDescriptions.severity as bugDescriptions_severity,
       bugDescriptions.priority as bugDescriptions_priority,
       If(count(distinct(targetReleaseId))>1, concat(globalState,'*'), globalState) as bugDescriptions_globalState,
       bugDescriptions.creator as bugDescriptions_creator,
       CAST(bugDescriptions.creationTimeStamp AS DATE) as bugDescriptions_creationTimeStamp,
       DATE_FORMAT(bugDescriptions.creationTimeStamp,'%e/%b/%Y') as bugDescriptions_formattedTimeStamp,
       DATE_FORMAT(bugDescriptions.creationTimeStamp,'%Y-%m-%d') as bugDescriptions_SLADate,
       IF(bugDescriptions.assignedTo != '', bugDescriptions.assignedTo, releaseTracking.assignedTo) as bugDescriptions_assignedTo,
       IF(bugDescriptions.assignedTo != '', assignedToManagerAlias.userName, assignedToManagerRTAlias.userName) as bugDescriptions_assignedToManager,
       bugDescriptions.summary as bugDescriptions_summary,
       DATEDIFF(NOW(), bugDescriptions.creationTimeStamp) AS numDays
   FROM bugDescriptions
   LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber
   LEFT JOIN users as assignedToManager ON bugDescriptions.assignedTo=assignedToManager.userName
   LEFT JOIN users as assignedToManagerAlias ON assignedToManagerAlias.userName=assignedToManager.managerName
   LEFT JOIN users as assignedToManagerRT ON releaseTracking.assignedTo=assignedToManagerRT.userName
   LEFT JOIN users as assignedToManagerRTAlias ON assignedToManagerRTAlias.userName=assignedToManagerRT.managerName
   WHERE ( bugDescriptions.productFamily = 'cqg' )
     AND ( bugDescriptions.globalState = 'Assigned' )
     AND ( bugDescriptions.component = 'EXOS' )
   GROUP BY bugDescriptions.bugNumber
   ORDER BY bugDescriptions_assignedToManager ASC, numDays DESC";

$cqgList = array();
$result = $conn->query($sql);

if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      $cqgList[] = $row;
     }
} else {
    echo "0 records found";
}

$sql = "select bugNumber, max(priority) priority, DATE_FORMAT(max(CreatedDate),'%b-%Y') as createdDate, DATE_FORMAT(max(ClosedDate),'%b-%Y') as transitionDate, globalState, DATEDIFF(MAX(ClosedDate), MAX(CreatedDate)) AS numDays, rbcRootCause
        FROM (
         select bugShortHistory.bugNumber, bugDescriptions.priority, bugDescriptions.globalState as globalState, udf1.rbcRootCause as rbcRootCause,
                CASE
                 WHEN bugShortHistory.action = 'created' THEN bugShortHistory.transitionDate
                END AS CreatedDate,
                CASE
                 WHEN bugShortHistory.action = 'close' THEN bugShortHistory.transitionDate
                END AS ClosedDate
         FROM bugShortHistory
         LEFT JOIN bugDescriptions USING(bugNumber)
	 LEFT JOIN udfRbcRootCause udf1 ON udf1.bugNumber=bugDescriptions.bugNumber
         WHERE  bugDescriptions.productFamily = 'cqg' AND bugDescriptions.component = 'EXOS'
         AND (bugShortHistory.action = 'close' OR bugShortHistory.action = 'created')
         ) as t1
        WHERE (DATE_FORMAT(CreatedDate,'%Y') >= 2016 || DATE_FORMAT(ClosedDate,'%Y') >= 2016)
        group by bugNumber";

$cqgDateList = array();
$result = $conn->query($sql);

if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      $cqgDateList[] = $row;
     }
} else {
    echo "0 records found";
}

      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "CQG Pending CRs";
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
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"example\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Verifier</th>";
                        echo "<th>Manager</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Created On</th>";
                        echo "<th>Pending Days</th>";
                        echo "<th>Internal SLA</th>";
                        echo "<th>External SLA</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    foreach($cqgList as $row) {

		    	if($row[bugDescriptions_priority] == "1 - Critical" || $row[bugDescriptions_priority] == "2 - Urgent") {
       				$intSLA = 30;
    			} elseif ($row[bugDescriptions_priority] == "3 - Important") {
       				$intSLA = 45;
    			} else {
       				$intSLA = 60;
    			}

			$extSLA = $intSLA + 15;

    			if ($row[numDays] < $intSLA) { 
    			   	$intbgColor = "#D4FFD4"; 
   			 } else {
      			 	$intbgColor = "#EF5A5A";
    			}

    			if ($row[numDays] < $extSLA) {
       				$extbgColor = "#D4FFD4";
    			} else {
       				$extbgColor = "#EF5A5A";
    			}

	 		$intSLADate = addDayswithdate($row[bugDescriptions_SLADate], $intSLA);
	 		$extSLADate = addDayswithdate($row[bugDescriptions_SLADate], $extSLA);
			$intdate = str_replace('/', '-', $intSLADate);
			$hiddenintSLADate = date("Y-m-d", strtotime($intdate));
			$hiddenintSLATime = strtotime($hiddenintSLADate);

			$extdate = str_replace('/', '-', $extSLADate);
                        $hiddenextSLADate = date("Y-m-d", strtotime($extdate));
                        $hiddenextSLATime = strtotime($hiddenextSLADate);

                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugDescriptions_bugNumber] target=\"_blank\">".$row[bugDescriptions_bugNumber]."</a></td>";
                          echo "<td>".$row[bugDescriptions_severity]."</td>";
                          echo "<td>".$row[bugDescriptions_priority]."</td>";
                          echo "<td>".$row[bugDescriptions_globalState]."</td>";
                          echo "<td>".$row[bugDescriptions_creator]."</td>";
                          echo "<td>".$row[bugDescriptions_assignedTo]."</td>";
                          echo "<td>".$row[bugDescriptions_assignedToManager]."</td>";
                          echo "<td>".$row[bugDescriptions_summary]."</td>";
                          echo "<td>".$row[bugDescriptions_formattedTimeStamp]."</td>";
                          echo "<td align=center>".$row[numDays]."</td>";
                          echo "<td align=center bgcolor=".$intbgColor."><span class=hide>".$hiddenintSLATime."</span>".$intSLADate."</td>";
                          echo "<td align=center bgcolor=".$extbgColor."><span class=hide>".$hiddenextSLATime."</span>".$extSLADate."</td>";
                          echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Verifier</th>";
                        echo "<th>Manager</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Created On</th>";
                        echo "<th>Pending Days</th>";
                        echo "<th>Internal SLA</th>";
                        echo "<th>External SLA</th>";
                      echo "</tr>";
	                    echo "</tfoot>";
                  echo "</table>";
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</div><!-- /.col -->";
          echo "</div><!-- /.row -->";


	#<!-- Creating another row and table to print the CQG ordered by number of assignees --> 

          echo "<div class=\"row\">";
            echo "<div class=\"col-md-3\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"cqgDistribution\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>Verifer</th>";
                        echo "<th>Total</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

		    foreach($cqgList as $data) {
    			@$cqgAssignedTo[$data['bugDescriptions_assignedTo']]++;
		    }
		    arsort($cqgAssignedTo);
		    printCQGassignedToDistribution($cqgAssignedTo, cqgPriorityDistribution);
		    printCQGfindVsfix($cqgDateList, cqgfindVsfix);
		    printCQGClosureTime($cqgDateList, cqgClosureTime);
		    printCQGrcDistribution($cqgDateList, cqgrcDistribution);

                    foreach($cqgAssignedTo as $key=>$value) {
			  echo "<tr>";
                          echo "<td width=10%>$key</td>";
                          echo "<td width=5%>$value</td>";
                          echo "</tr>";
		    }

                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>Verifer</th>";
                        echo "<th>Total</th>";
                      echo "</tr>";
                            echo "</tfoot>";
                  echo "</table>";
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</div><!-- /.col -->";

            echo "<div class=\"col-md-9\">";
              echo "<div class=\"box\">";
                echo "<div class=\"box-header\">";
                echo "<h3>CQG Dashboard</h3>";
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table width=100%>";
                      echo "<tr>";
                          echo "<td width=50%>";
                          echo "<div class=\"chart\" id=\"cqgPriorityDistribution\" style=\"height: 280px; position: relative;\"></div>";
                          echo "</td>";

                          echo "<td width=50%>";
                          echo "<div class=\"chart\" id=\"cqgfindVsfix\" style=\"height: 300px; position: relative;\"></div>";
                          echo "</td>";
                      echo "</tr>";

                      echo "<tr>";
                          echo "<td width=50%>";
                          echo "<div class=\"chart\" id=\"cqgClosureTime\" style=\"height: 300px; position: relative;\"></div>";
                          echo "</td>";

                          echo "<td width=50%>";
                          echo "<div class=\"chart\" id=\"cqgrcDistribution\" style=\"height: 300px; position: relative;\"></div>";
                          echo "</td>";
                      echo "</tr>";
                   echo "</tbody>";
                  echo "</table>";
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</div><!-- /.col -->";



          echo "</div><!-- /.row -->";
        echo "</section><!-- /.content -->";
      echo "</div><!-- /.content-wrapper -->";
    echo "</div><!-- ./wrapper -->";

  #echo $scriptInclude;
  echo "</body>";
echo "</html>";

}

#########################################################################################
#                                                                                       #
# Function to write all CR data to file							#
#                                                                                       #
#########################################################################################
function writeArraytoFile($arrayName, $fileName) {
$file = fopen("flexmonster/$fileName.csv","w") or die("Can't open file");

foreach($arrayName as $data) {
   foreach ($data as $key=>$value) {
      $headerArray[] = $key;
    }
    break;
}
fputcsv($file, $headerArray);

foreach ($arrayName as $line) {
    fputcsv($file,$line);
}

fclose($file);
 
}

#########################################################################################
#                                                                                       #
# Function to print Priority Distribution Pie Chart Data for specified release CRs 	#
#                                                                                       #
#########################################################################################
function printPriorityDistribution($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showLabels='1' showValues='1' subcaption='' palettecolors='#F31B1B,#F39C12,#5298C6,#00a65a,#B969CB,#0000FF' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='1' decimals='1' captionfontsize='10' subcaptionfontsize='10' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName) {
	@$priorityList[$data[priority]]++;
  }
}
ksort($priorityList);

foreach ($priorityList as $key=>$value) {
	$strXML .= "<set label='" . $key. "' value='" . $priorityList[$key]. "' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
	FusionCharts.ready(function () {
		var $chartID = new FusionCharts({
			"type": "pie2d",
			"renderAt": "$chartID",
			"width": "100%",
			"height": "100%",
			"dataFormat": "xmlurl",
			"dataSource": "Data/$chartID.xml"
		});
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print new Features Bar Chart Data for specified release CRs      		#
#                                                                                       #
#########################################################################################
function printnewFeature($crList, $releaseName, $targetRelease, $chartID) {
$chart_open = "<chart caption='' subcaption='' xaxisname='' yaxisname='' numberprefix='' palettecolors='#0075c2' bgalpha='0' borderalpha='20' canvasborderalpha='0' useplotgradientcolor='0' plotborderalpha='10' placevaluesinside='1' rotatevalues='1' valuefontcolor='#ffffff' captionpadding='20' showaxislines='1' axislinealpha='25' divlinealpha='10'>\n";
$chart_close= "</chart>\n";

$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$strXML="";
$countDisplay=100;
$keyMap = $GLOBALS['keyMapName'];

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName && $data[keyName] == $keyMap[$releaseName]) {
	@$metaDataListFull[$data[metaData]]++;
  }
}
arsort($metaDataListFull);
$metaDataList = array_slice($metaDataListFull, 0, $countDisplay, true);

foreach ($metaDataList as $key=>$value) {
	$strXML .= "<set label='" . $key. "' value='" . $metaDataList[$key]. "' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "column2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print new Features Column Chart Data with Priority Distrubution		#
#                                                                                       #
#########################################################################################
function printnewFeaturePriority($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showvalues='0' plotfillalpha='95' stack100percent='0' plotgradientcolor='' formatnumberscale='0' showplotborder='0' palettecolors='#DD4B39,#F39C12,#008ee4,#00A65A,#B969CB,#0000FF' canvaspadding='0' bgcolor='FFFFFF' showalternatehgridcolor='0' divlinecolor='CCCCCC' showlegend='0' showcanvasborder='0' legendborderalpha='0' legendshadow='0' interactivelegend='0' showpercentvalues='1' showsum='1' canvasborderalpha='0' showborder='0'>\n";
$category_open = "<categories >\n";
$category_close = "</categories>\n";
$data_set_close= "</dataset>\n";
$chart_close= "</chart>\n";
$dataSet_HDR[Critical] = "<dataset seriesName='1 - Critical' renderas='Area'>\n";
$dataSet_HDR[Urgent] = "<dataset seriesName='2 - Urgent' renderas='Area'>\n";
$dataSet_HDR[Important] = "<dataset seriesName='3 - Important' renderas='Area'>\n";
$dataSet_HDR[Moderate] = "<dataset seriesName='4 - Moderate' renderas='Area'>\n";
$dataSet_HDR[Low] = "<dataset seriesName='5 - Low' renderas='Area'>\n";
$dataSet_HDR[TBR] = "<dataset seriesName='0 - TBR' renderas='Area'>\n";
$countDisplay=100;

$keyMap = $GLOBALS['keyMapName'];

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName && $data[keyName] == $keyMap[$releaseName]) {
        @$metaDataPriority[$data[metaData]][$data['priority']]++;
        @$metaDataListFull[$data[metaData]]++;
  }
}

arsort($metaDataListFull);
$metaDataList = array_slice($metaDataListFull, 0, $countDisplay, true);

foreach ($metaDataList as $key=>$value) {
        $x_metaData = $x_metaData ."<category label='$key' stepskipped='false' appliedsmartlabel='true' labeltooltext='' />\n";
        $dataSet_HDR[Critical] 	= $dataSet_HDR[Critical]."<set value='".$metaDataPriority[$key]['1 - Critical']."' />\n";
        $dataSet_HDR[Urgent]	= $dataSet_HDR[Urgent]."<set value='".$metaDataPriority[$key]['2 - Urgent']."' />\n";
        $dataSet_HDR[Important] = $dataSet_HDR[Important]."<set value='".$metaDataPriority[$key]['3 - Important']."' />\n";
        $dataSet_HDR[Moderate] 	= $dataSet_HDR[Moderate]."<set value='".$metaDataPriority[$key]['4 - Moderate']."' />\n";
        $dataSet_HDR[Low] 	= $dataSet_HDR[Low]."<set value='".$metaDataPriority[$key]['5 - Low']."' />\n";
        $dataSet_HDR[TBR] 	= $dataSet_HDR[TBR]."<set value='".$metaDataPriority[$key]['0 - To Be Reviewed']."' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $category_open);
fwrite($fh, $x_metaData);
fwrite($fh, $category_close);
fwrite($fh, $dataSet_HDR[Critical]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Urgent]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Important]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Moderate]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Low]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[TBR]);
fwrite($fh, $data_set_close);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "stackedcolumn2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print New/Legacy/Regression Pie Chart Data for specified release CRs      #
#                                                                                       #
#########################################################################################
function printLegacyRegressionIssues($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showLabels='1' showValues='1' subcaption='' palettecolors='#00a65a,#5298C6,#F31B1B' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='1' decimals='1' captionfontsize='10' subcaptionfontsize='10' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";
$keyMap = $GLOBALS['keyMapName'];

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName && $data[keyName] == $keyMap[$releaseName]) {
	$newFeature++;
  } elseif ($data[releaseDetected] == $releaseName) {
	$legacyCR++;
  } else {
	$retargetCRs++;
 }
}

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName && $data[passedPreviously] == "Yes") {
	$regressionCR++;
  }
}

$strXML .= "<set label='" . "New Feature". "' value='" . $newFeature. "' />\n";
$strXML .= "<set label='" . "Legacy Feature". "' value='" . $legacyCR. "' />\n";
$strXML .= "<set label='" . "Regression CR". "' value='" . $regressionCR. "' />\n";

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "pie2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print Open CR Priority Distribution Pie Chart P1/P2 CRs of given Release  #
#                                                                                       #
#########################################################################################
function printopenCRPriorityDistribution($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showLabels='1' showValues='1' subcaption='' palettecolors='#F31B1B,#F31B1B,#5298C6,#5298C6' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='0' decimals='1' captionfontsize='10' subcaptionfontsize='10' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";
$openListAll['1 - Critical']=0;
$openListAll['2 - Urgent']=0;
$openListAssigned['1 - Critical']=0;
$openListAssigned['2 - Urgent']=0;

foreach($crList as $data) {
  if($data[releaseDetected]==$releaseName && $data[crState]=="open") {
	@$openListAssigned[$data[priority]]++;
  }
  if($data[releaseDetected]==$releaseName) {
	@$openListAll[$data[priority]]++;
  }
}

$strXML .= "<set label='Total P1' value='" . $openListAll['1 - Critical']. "' />\n";
$strXML .= "<set label='Open P1'  value='" . $openListAssigned['1 - Critical']. "' />\n";
$strXML .= "<set label='Total P2' value='" . $openListAll['2 - Urgent']. "' />\n";
$strXML .= "<set label='Open P2'  value='" . $openListAssigned['2 - Urgent']. "' />\n";

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "pie2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print top 10 Compoenents Bar Chart for a given Release  			#
#                                                                                       #
#########################################################################################
function printtopComponent($crList, $releaseName, $targetRelease, $chartID) {
$chart_open = "<chart caption='' subcaption='' xaxisname='' yaxisname='' numberprefix='' palettecolors='#0075c2' bgalpha='0' borderalpha='20' canvasborderalpha='0' useplotgradientcolor='0' plotborderalpha='10' placevaluesinside='1' rotatevalues='1' valuefontcolor='#ffffff' captionpadding='20' showaxislines='1' axislinealpha='25' divlinealpha='10'>\n";
$chart_close= "</chart>\n";

$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$strXML="";
$countDisplay=10;

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName) {
	@$componentListFull[$data[component]]++;
  }
}

arsort($componentListFull);
$componentList = array_slice($componentListFull, 0, $countDisplay, true);

foreach ($componentList as $key=>$value) {
	$strXML .= "<set label='" . $key. "' value='" . $componentList[$key]. "' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "column2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}


#########################################################################################
#                                                                                       #
# Function to print top Component Data with Priority Distrubution           		#
#                                                                                       #
#########################################################################################
function printtopComponentPriority($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showvalues='0' plotfillalpha='95' stack100percent='0' plotgradientcolor='' formatnumberscale='0' showplotborder='0' palettecolors='#DD4B39,#F39C12,#008ee4,#00A65A,#B969CB,#0000FF' canvaspadding='0' bgcolor='FFFFFF' showalternatehgridcolor='0' divlinecolor='CCCCCC' showlegend='0' showcanvasborder='0' legendborderalpha='0' legendshadow='0' interactivelegend='0' showpercentvalues='1' showsum='1' canvasborderalpha='0' showborder='0'>\n";
$category_open = "<categories >\n";
$category_close = "</categories>\n";
$data_set_close= "</dataset>\n";
$chart_close= "</chart>\n";
$dataSet_HDR[Critical] = "<dataset seriesName='1 - Critical' renderas='Area'>\n";
$dataSet_HDR[Urgent] = "<dataset seriesName='2 - Urgent' renderas='Area'>\n";
$dataSet_HDR[Important] = "<dataset seriesName='3 - Important' renderas='Area'>\n";
$dataSet_HDR[Moderate] = "<dataset seriesName='4 - Moderate' renderas='Area'>\n";
$dataSet_HDR[Low] = "<dataset seriesName='5 - Low' renderas='Area'>\n";
$dataSet_HDR[TBR] = "<dataset seriesName='0 - TBR' renderas='Area'>\n";
$countDisplay=10;

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName) {
        @$componentListPriority[$data[component]][$data['priority']]++;
        @$componentListFull[$data[component]]++;
  }
}

arsort($componentListFull);
$componentList = array_slice($componentListFull, 0, $countDisplay, true);

foreach ($componentList as $key=>$value) {
        $x_componentList = $x_componentList ."<category label='$key' stepskipped='false' appliedsmartlabel='true' labeltooltext='' />\n";
        $dataSet_HDR[Critical]  = $dataSet_HDR[Critical]."<set value='".$componentListPriority[$key]['1 - Critical']."' />\n";
        $dataSet_HDR[Urgent]    = $dataSet_HDR[Urgent]."<set value='".$componentListPriority[$key]['2 - Urgent']."' />\n";
        $dataSet_HDR[Important] = $dataSet_HDR[Important]."<set value='".$componentListPriority[$key]['3 - Important']."' />\n";
        $dataSet_HDR[Moderate]  = $dataSet_HDR[Moderate]."<set value='".$componentListPriority[$key]['4 - Moderate']."' />\n";
        $dataSet_HDR[Low]       = $dataSet_HDR[Low]."<set value='".$componentListPriority[$key]['5 - Low']."' />\n";
        $dataSet_HDR[TBR]       = $dataSet_HDR[TBR]."<set value='".$componentListPriority[$key]['0 - To Be Reviewed']."' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $category_open);
fwrite($fh, $x_componentList);
fwrite($fh, $category_close);
fwrite($fh, $dataSet_HDR[Critical]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Urgent]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Important]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Moderate]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Low]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[TBR]);
fwrite($fh, $data_set_close);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "stackedcolumn2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print all Managers Total CRs Pie Chart for a given Release                #
#                                                                                       #
#########################################################################################
function printCRbyManager($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption=''  showLabels='1' showValues='1' subcaption='' palettecolors='#F31B1B,F39C12,#5298C6,#00a65a,#B969CB,#605CA8,#00C0EF,#3C3C3C,#7722CC,#7F5454,#547F7F,#0075c2' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='1' decimals='1' captionfontsize='10' subcaptionfontsize='10' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName) {
	@$managerCRList[$data[creatorManager]]++;
  }
}

foreach ($managerCRList as $key=>$value) {
	$strXML .= "<set label='" . $key. "' value='" . $managerCRList[$key]. "' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "pie2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print all Managers Total CRs Pie Chart for a given Release                #
#                                                                                       #
#########################################################################################
function printRBCClassification($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showLabels='1' dataEmptyMessage='Zero RBC Issues' showValues='1' subcaption='' palettecolors='#00a65a,#5298C6' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='1' decimals='1' captionfontsize='10' subcaptionfontsize='10' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";
@$rbcList['RBC-INTERNAL']=0;
@$rbcList['RBC-ORIGINAL']=0;

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName) {
	@$rbcList[$data[reportedByCustomer]]++;
  }
}

$strXML .= "<set label='RBC-Internal' value='" . $rbcList['RBC-INTERNAL']. "' />\n";
$strXML .= "<set label='RBC-Original' value='" . $rbcList['RBC-ORIGINAL']. "' />\n";

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "pie2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}


#########################################################################################
#                                                                                       #
# Function to print the priority Vs severity barChart for a given Release               #
#                                                                                       #
#########################################################################################
function printPrioritySeverity($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showvalues='0' plotfillalpha='95' stack100percent='1' plotgradientcolor='' formatnumberscale='0' showplotborder='0' palettecolors='#DD4B39,#F39C12,#008ee4,#00A65A,#B969CB' canvaspadding='0' bgcolor='FFFFFF' showalternatehgridcolor='0' divlinecolor='CCCCCC' showcanvasborder='0' legendborderalpha='0' legendshadow='0' interactivelegend='0' showpercentvalues='1' showsum='1' canvasborderalpha='0' showborder='0'>\n";
$category_open = "<categories >\n";
$category_close = "</categories>\n";
$data_set_close= "</dataset>\n";
$chart_close= "</chart>\n";
$dataSet_HDR[Crash] = "<dataset seriesName='Crash' renderas='Area'>\n";
$dataSet_HDR[Major] = "<dataset seriesName='Major' renderas='Area'>\n";
$dataSet_HDR[Minor] = "<dataset seriesName='Minor' renderas='Area'>\n";
$dataSet_HDR[Trivial] = "<dataset seriesName='Trivial' renderas='Area'>\n";
$dataSet_HDR[NewFeature] = "<dataset seriesName='NewFeature' renderas='Area'>\n";

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName) {
	@$crPriority[$data['priority']][$data['severity']]++;
  }
}
ksort($crPriority);

foreach ($crPriority as $key=>$value) {
 	$x_priority = $x_priority ."<category label='$key' stepskipped='false' appliedsmartlabel='true' labeltooltext='' />\n";
	$dataSet_HDR[Crash] = $dataSet_HDR[Crash]."<set value='".$value['1 - Crash']."' />\n";
	$dataSet_HDR[Major] = $dataSet_HDR[Major]."<set value='".$value['2 - Major']."' />\n";
	$dataSet_HDR[Minor] = $dataSet_HDR[Minor]."<set value='".$value['3 - Minor']."' />\n";
	$dataSet_HDR[Trivial] = $dataSet_HDR[Trivial]."<set value='".$value['4 - Trivial']."' />\n";
	$dataSet_HDR[NewFeature] = $dataSet_HDR[NewFeature]."<set value='".$value['5 - New Feature']."' />\n";
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $category_open);
fwrite($fh, $x_priority);
fwrite($fh, $category_close);	
fwrite($fh, $dataSet_HDR[Crash]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Major]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Minor]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Trivial]);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[NewFeature]);
fwrite($fh, $data_set_close);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "stackedcolumn2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to Find Vs Fix table for a given Release			                #
#                                                                                       #
#########################################################################################
function printfindVsfix($crList, $releaseName, $targetRelease, $chartID, $conn) {
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");

$chart_open = "<chart caption='' subcaption='' exportEnabled='1' captionfontsize='14' subcaptionfontsize='14' basefontcolor='#333333' yAxisMinValue='0' basefont='Helvetica Neue,Arial' subcaptionfontbold='0' xaxisname='' yaxisname='' showvalues='1' palettecolors='#0075c2,#1aaf5d' bgcolor='#ffffff' showborder='0' showshadow='0' showalternatehgridcolor='0' showcanvasborder='0' showxaxisline='1' xaxislinethickness='1' xaxislinecolor='#999999' canvasbgcolor='#ffffff' legendborderalpha='0' legendshadow='0' divlinealpha='50' divlinecolor='#999999' divlinethickness='1' divlinedashed='1' divlinedashlen='1'>\n";
$category_open = "<categories >\n";
$category_close = "</categories>\n";
$data_set_close= "</dataset>\n";
$chart_close= "</chart>\n";
$dataSet_HDR[Find] = "<dataset seriesName='Find' color='B22222' anchorBorderColor='B22222' anchorBgColor='B22222'>\n";
$dataSet_HDR[Fix] = "<dataset seriesName='Fix' color='006400' anchorBorderColor='006400' anchorBgColor='006400'>\n";

$incomingCR = printLineIncomingCR($conn, $releaseName, $targetRelease);
$fixedCR    = printLineFixedCR($conn, $releaseName, $targetRelease);

foreach ($fixedCR as $fixdata) {
	$fdata[$fixdata[month]] = $fixdata[total];
}

foreach ($incomingCR as $data) {
	$month = $data[month];
	$x_axis_month = $x_axis_month."<category label='$data[month]' />\n";
	$dataFind = $dataFind."<set value='$data[total]' />\n";
	if($fdata[$month]=="") {
		$dataFix = $dataFix."<set value='0' />\n";
	} else {
		$dataFix = $dataFix."<set value='$fdata[$month]' />\n";
	}
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $category_open);
fwrite($fh, $x_axis_month);
fwrite($fh, $category_close);
fwrite($fh, $dataSet_HDR[Find]);
fwrite($fh, $dataFind);
fwrite($fh, $data_set_close);
fwrite($fh, $dataSet_HDR[Fix]);
fwrite($fh, $dataFix);
fwrite($fh, $data_set_close);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "msspline",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to CR Distribution Table for a given Release                                 #
#                                                                                       #
#########################################################################################
function printCRStateDistribution($crList, $releaseName, $targetRelease, $chartID) {
$strXML="";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");
$chart_open = "<chart caption='' showLabels='1' showValues='1' subcaption='' palettecolors='#F31B1B,#00a65a,#605CA8,#F39C12' bgcolor='#ffffff' showborder='0' use3dlighting='1' showshadow='0' enablesmartlabels='1' startingangle='0' showpercentvalues='0' showpercentintooltip='1' decimals='1' captionfontsize='10' subcaptionfontsize='10' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='0' legendbgcolor='#ffffff' legendborderalpha='1' legendshadow='1' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
$chart_close =  "</chart>\n";
$totalCount=0;

foreach($crList as $data) {
  if($data[releaseDetected] == $releaseName) {
	@$releaseState[$data['crState']]++;
	$totalCount++;
  }
}

$strXML .= "<set label='" . "Open". "' value='" . $releaseState[open]. "' />\n";
$strXML .= "<set label='" . "Closed". "' value='" . $releaseState[closed]. "' />\n";
$strXML .= "<set label='" . "SQA Pending". "' value='" . $releaseState[sqaPending]. "' />\n";
$strXML .= "<set label='" . "Moved to Future". "' value='" . $releaseState[futureScope]. "' />\n";

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "pie2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print the last 7 days Incoming for given Release				#
#                                                                                       #
#########################################################################################
function printlastWeekIncoming($crList, $releaseName, $targetRelease, $chartID) {
$chart_open = "<chart caption='' subcaption='' valuePadding='5' valueFontSize='10' valueFontBold='1'  xaxisname='' yaxisname='' numberprefix='' palettecolors='#0075c2' bgcolor='#ffffff' showborder='0' showcanvasborder='0' plotborderalpha='10' useplotgradientcolor='0' plotfillalpha='50' showxaxisline='0' axislinealpha='0' yAxisMaxValue='10' divlinealpha='0' showvalues='1' showYAxisValues='0' showXAxisValues='0'  showalternatehgridcolor='0' captionfontsize='14' subcaptionfontsize='14' subcaptionfontbold='0' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5'>\n";
$chart_close= "</chart>\n";
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");

$lastWeek = getLastNDays(7);
foreach($crList as $data) {
  if (in_array($data['creationTimeStamp'], $lastWeek) && $data[releaseDetected] == $releaseName) {
	@$crDate[$data['creationTimeStamp']]++;
  }
}

$frameCount=1;
foreach ($lastWeek as $key=>$value) {
  if ($crDate[$value]=="") {
	$dayCount=0;
  } else {
	$dayCount=$crDate[$value];
  }
  $strXML .= "<set label='" . $value. "' value='" . $dayCount. "' link='F-drill". $frameCount ."-index.php?printArray=yes&targetrelease=".$targetRelease."&releaseName=".$releaseName."&crState=creationDate&date=". $value ."' />\n";
  $frameCount++;
}

#Writing data to file
fwrite($fh, $chart_open);
fwrite($fh, $strXML);
fwrite($fh, $chart_close);
fclose($fh);

$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var $chartID = new FusionCharts({
                        "type": "area2d",
                        "renderAt": "$chartID",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "dataSource": "Data/$chartID.xml"
                });
$chartID.render();
});
</script>
HTML;
echo $renderChart;
}

#########################################################################################
#                                                                                       #
# Function to print the CR Table							#
#                                                                                       #
#########################################################################################
function printTable($crList, $releaseName, $targetRelease, $crState) {
include 'template.php';
echo "<html>";
echo $headInclude;
echo $dataTableHeader;
$filterScript = <<<HTML
        <script type="text/javascript" class="init">
        $(document).ready(function() {
                $('#example').DataTable( {
          		"bPaginate": false,
			"colReorder": true,
                        dom: 'Bfrtip',
                        buttons: [
                                'copyHtml5',
                                'excelHtml5'
                        ]
                } );
        } );
        </script>
HTML;
echo $filterScript;
echo $topBar;

      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "Printing $releaseName - $crState CRs";
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
                echo "</div>"; #<!-- /.box-header -->
                echo "<div class=\"box-body\">";
                  echo "<table id=\"example\" class=\"table table-bordered table-striped\">";
                    echo "<thead>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Release State</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Verifier</th>";
                        echo "<th>Manager</th>";
                        echo "<th>CR State</th>";
                      echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

		   if($crState=="allRelease") {
                      foreach($crList as $row) {
			 if($row[releaseDetected]==$releaseName) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[verifier]."</td>";
                          echo "<td>".$row[creatorManager]."</td>";
                          echo "<td>".$row[crState]."</td>";
                          echo "</tr>";
			 }
                        }
                    } elseif ($crState=="creationDate") {
                        foreach($crList as $row) {
                         if($row[releaseDetected]==$releaseName && $row[creationTimeStamp]==$_GET[date]) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[verifier]."</td>";
                          echo "<td>".$row[creatorManager]."</td>";
                          echo "<td>".$row[crState]."</td>";
                          echo "</tr>";
                         }
                        }
                    } elseif ($crState=="plusRetarget") {
                        foreach($crList as $row) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[verifier]."</td>";
                          echo "<td>".$row[creatorManager]."</td>";
                          echo "<td>".$row[crState]."</td>";
                          echo "</tr>";
                        }
		    } else {
			foreach($crList as $row) {
			 if($row[crState]==$crState && $row[releaseDetected]==$releaseName) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[globalState]."</td>";
                          echo "<td>".$row[releaseDetected]."</td>";
                          echo "<td>".$row[releaseState]."</td>";
                          echo "<td>".$row[summary]."</td>";
                          echo "<td>".$row[severity]."</td>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[creator]."</td>";
                          echo "<td>".$row[verifier]."</td>";
                          echo "<td>".$row[creatorManager]."</td>";
                          echo "<td>".$row[crState]."</td>";
                          echo "</tr>";
			 }
			}
		    }

                    echo "</tbody>";
                    echo "<tfoot>";
                      echo "<tr>";
                        echo "<th>BugNumber</th>";
                        echo "<th>Global State</th>";
                        echo "<th>Release Detected</th>";
                        echo "<th>Release State</th>";
                        echo "<th>Summary</th>";
                        echo "<th>Severity</th>";
                        echo "<th>Priority</th>";
                        echo "<th>Creator</th>";
                        echo "<th>Verifier</th>";
                        echo "<th>Manager</th>";
                        echo "<th>CR State</th>";
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

  #echo $scriptInclude;
  echo "</body>";
echo "</html>";

}

?>

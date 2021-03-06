<?php
#########################################################################################
#                                                                                       #
#  Global Variable for Today
#                                                                                       #
#########################################################################################
$today = date("Y-m-d");
$GLOBALS['keyMapName'] = array("EXOS 22.1.1"=>"New Feature 22.1", "EXOS 22.2.1"=>"New Feature 22.2", "EXOS 22.3.1"=>"New Feature 22.3", "EXOS 22.4.1"=>"New Feature 22.4", "EXOS 22.5.1"=>"New Feature 22.5", "EXOS 23.1.1"=>"New Feature 23.1");

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

if ($releaseName == "EXOS 22.6.1") {
 $preRelease = "EXOS 22.4.1";
} elseif ($releaseName == "EXOS 22.5.1") {
 $preRelease = "EXOS 22.3.1";
} elseif ($releaseName == "EXOS 22.4.1") {
 $preRelease = "EXOS 22.2.1";
} elseif ($releaseName == "EXOS 22.3.1") {
 $preRelease = "EXOS 22.1.1";
}

$sql = "select bugDescriptions_shortPriority as priority, count(bugDescriptions_shortPriority) as priorityCount from (
SELECT bugDescriptions.bugNumber as bugDescriptions_bugNumber,
    bugDescriptions.severity+0 as bugDescriptions_shortSeverity,
    bugDescriptions.priority+0 as bugDescriptions_shortPriority,
    bugDescriptions.gaBlocking as bugDescriptions_gaBlocking,
    GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseGaBlocking, ')' SEPARATOR '<BR>') AS releaseTracking_releaseGaBlocking , If(count(distinct(targetReleaseId))>1, concat(globalState,'*'), globalState) as bugDescriptions_globalState,
    bugDescriptions.status as bugDescriptions_status,
    creatorManagerAlias.userName as bugDescriptions_creatorManager,
    bugDescriptions.creationTimeStamp as bugDescriptions_creationTimeStamp,
    bugDescriptions.platform as bugDescriptions_platform,
    IF(bugDescriptions.assignedTo != '', bugDescriptions.assignedTo, releaseTracking.assignedTo) as bugDescriptions_assignedTo,
    IF(bugDescriptions.assignedTo != '', assignedToManagerAlias.userName, assignedToManagerRTAlias.userName) as bugDescriptions_assignedToManager,
    GROUP_CONCAT(DISTINCT releases.releaseName, ' - ', releaseTracking.assignedTo SEPARATOR '<BR>') AS releaseTracking_assignedTo , bugDescriptions.customerName as bugDescriptions_customerName,
    bugDescriptions.releaseDetected as bugDescriptions_releaseDetected,
    releaseTracking.releaseState as releaseTracking_releaseState,
    GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseState, ')' SEPARATOR '<BR>') AS releases_releaseName , bugDescriptions.summary as bugDescriptions_summary 
FROM bugDescriptions 
LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber 
LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId 
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
   OR ( ( bugDescriptions.productFamily = 'xos' )
   AND ( bugDescriptions.releaseDetected = '$releaseName' )
   AND ( bugDescriptions.globalState = 'Assigned' 
   OR bugDescriptions.globalState = 'Assigned Review' 
   OR bugDescriptions.globalState = 'Deferred' 
   OR bugDescriptions.globalState = 'Feedback Needed' 
   OR bugDescriptions.globalState = 'Idle' 
   OR bugDescriptions.globalState = 'Study' )
   AND ( isNull(releases.releaseName) ) ) 
GROUP BY bugDescriptions.bugNumber) as t1 group by bugDescriptions_shortPriority order by bugDescriptions_shortPriority ASC";

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

####################################################################################
## 	Calculating RDI Value for VPEX CRs - Query ID 14333			   #
####################################################################################
$rdiVPEX = 0;
$p1Rdi = 0;
$p2Rdi = 0;
$p3Rdi = 0;
$p4Rdi = 0;


$sql = "select bugDescriptions_shortPriority as priority, count(bugDescriptions_shortPriority) as priorityCount from (
SELECT bugDescriptions.bugNumber as bugDescriptions_bugNumber,
    bugDescriptions.severity+0 as bugDescriptions_shortSeverity,
    bugDescriptions.priority+0 as bugDescriptions_shortPriority,
    bugDescriptions.creationTimeStamp as bugDescriptions_creationTimeStamp,
    bugDescriptions.platform as bugDescriptions_platform,
    IF(bugDescriptions.assignedTo != '', bugDescriptions.assignedTo, releaseTracking.assignedTo) as bugDescriptions_assignedTo,
    IF(bugDescriptions.assignedTo != '', assignedToManagerAlias.userName, assignedToManagerRTAlias.userName) as bugDescriptions_assignedToManager,
    bugDescriptions.releaseDetected as bugDescriptions_releaseDetected,
    GROUP_CONCAT(DISTINCT releases.releaseName, ' (', releaseTracking.releaseState, ')' SEPARATOR '<BR>') AS releases_releaseName , bugDescriptions.summary as bugDescriptions_summary 
FROM bugDescriptions 
LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber 
LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId 
LEFT JOIN metadata ON metadata.typeId=bugDescriptions.bugNumber 
LEFT JOIN users as assignedToManager ON bugDescriptions.assignedTo=assignedToManager.userName 
LEFT JOIN users as assignedToManagerAlias ON assignedToManagerAlias.userName=assignedToManager.managerName 
LEFT JOIN users as assignedToManagerRT ON releaseTracking.assignedTo=assignedToManagerRT.userName 
LEFT JOIN users as assignedToManagerRTAlias ON assignedToManagerRTAlias.userName=assignedToManagerRT.managerName 
WHERE ( bugDescriptions.productFamily = 'xos' )
   AND ( bugDescriptions.globalState = 'Assigned' 
   OR bugDescriptions.globalState = 'Feedback Needed' )
   AND ( bugDescriptions.releaseDetected <= '$releaseName' )
   AND ( releaseTracking.releaseState <> 'Build Pending' 
   AND releaseTracking.releaseState <> 'Released' 
   AND releaseTracking.releaseState <> 'Task Complete' 
   AND releaseTracking.releaseState <> 'Unverified Released' 
   AND releaseTracking.releaseState <> 'Verified' 
   AND releaseTracking.releaseState <> 'Verify Fix' )
   AND ( ( metadata.value = 'Bridge Port Extender (802.1BR)' )
   OR ( metadata.value = 'VPEX (802.1BR) on the X690' )
   OR ( bugDescriptions.platform LIKE '%BCM: Summit VPEX%' )
   OR ( bugDescriptions.summary LIKE '%dot1br%' ) ) 
GROUP BY bugDescriptions.bugNumber) as t1
group by bugDescriptions_shortPriority order by bugDescriptions_shortPriority ASC";

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

$rdiVPEX = $p1Rdi + $p2Rdi + $p3Rdi + $p4Rdi;

echo "<div class=\"col-md-2 col-xs-6\">";
 echo "<div class=\"small-box bg-aqua\">";
  echo "<div class=\"inner\">";
   echo "<h3>$rdiValue</h3>";
   echo "<p>VPEX - $rdiVPEX </p>";
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

$sql = "SELECT bugDescriptions.bugNumber, summary, severity, priority, GROUP_CONCAT(DISTINCT bugTestBlocking.testBlocking SEPARATOR ', ') AS testBlocking,
globalState, component, subComponent, releaseState, releaseDetected, targetReleaseId, creator, verifier,
releaseTracking.assignedTo, creatorManager.ldapManagerName as creatorManager, verifierManager.ldapManagerName as verifierManager,
udf1.features as udfFeature, udf2.passedPreviously as passedPreviously, udf3.lastPassBuild as lastPassBuild, metadata.value as metaData, metadataKeyMap.keyName as keyName,
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
                @$row['crState']="futureScope";
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
# Function to Find Vs Fix table for a given Release			                #
#                                                                                       #
#########################################################################################
function printfindVsfix($crList, $releaseName, $targetRelease, $chartID, $conn) {
$myFile = "Data/$chartID.xml";
unlink($myFile);
$fh = fopen($myFile, 'w') or die("Can't open file");

$chart_open = "<chart caption='' subcaption='' captionfontsize='14' subcaptionfontsize='14' basefontcolor='#333333' yAxisMinValue='0' basefont='Helvetica Neue,Arial' subcaptionfontbold='0' xaxisname='' yaxisname='' showvalues='1' palettecolors='#0075c2,#1aaf5d' bgcolor='#ffffff' showborder='0' showshadow='0' showalternatehgridcolor='0' showcanvasborder='0' showxaxisline='1' xaxislinethickness='1' xaxislinecolor='#999999' canvasbgcolor='#ffffff' legendborderalpha='0' legendshadow='0' divlinealpha='50' divlinecolor='#999999' divlinethickness='1' divlinedashed='1' divlinedashlen='1'>\n";
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
# Function to Find Vs Fix table for a given Release                                     #
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
  if (in_array($data['creationTimeStamp'], $lastWeek)) {
	@$crDate[$data['creationTimeStamp']]++;
  }
}

foreach ($lastWeek as $key=>$value) {
  if ($crDate[$value]=="") {
	$dayCount=0;
  } else {
	$dayCount=$crDate[$value];
  }
  $strXML .= "<set label='" . $value. "' value='" . $dayCount. "' />\n";
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

		   if($crState=="all") {
                      foreach($crList as $row) {
			 if($row[releaseDetected]==$releaseName) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[globalState]."</td>";
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
		    } else {
			foreach($crList as $row) {
			 if($row[crState]==$crState && $row[releaseDetected]==$releaseName) {
                          echo "<tr>";
                          echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$row[bugNumber] target=\"_blank\">".$row[bugNumber]."</a></td>";
                          echo "<td>".$row[globalState]."</td>";
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

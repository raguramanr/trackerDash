<?php 
define ("DB_HOST", "trackback");
define ("DB_USER", "enikiosk-RO");
define ("DB_PASS","enikiosk-RO");
define ("DB_NAME","tracker");

$link = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Couldn't make connection.");
$db = mysql_select_db(DB_NAME, $link) or die("Couldn't select database");

$releaseName = $_GET[releaseName];
$targetRelease = $_GET[targetRelease];
$metadataKeyMapId = $_GET[metadataKeyMapId];
$setCounter = 0;

if ($_GET[getReport] == "RDI") {
	$setExcelName = "rdiCRs";
	
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
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.3.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.4.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.5.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.6.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.7.1'";
} elseif ($releaseName == "EXOS 30.1.1") {
  $sqlCondition = "bugDescriptions.releaseDetected = 'EXOS 22.4.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.5.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.6.1'
                OR bugDescriptions.releaseDetected = 'EXOS 22.7.1'
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

$sql = "select * from ( SELECT bugDescriptions.bugNumber as bugDescriptions_bugNumber,
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
GROUP BY bugDescriptions.bugNumber) t where (releases_releaseName not like '%Verified%' and releases_releaseName not like '%Released%') or isNull(releases_releaseName)";

} elseif ($_GET[getReport] == "allCRs") {

        $setExcelName = "allCRs";
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

} else {

}
	
$setRec = mysql_query($sql);
$setCounter = mysql_num_fields($setRec);

for ($i = 0; $i < $setCounter; $i++) {
    $setMainHeader .= mysql_field_name($setRec, $i)."\t";
}

while($rec = mysql_fetch_row($setRec))  {
  $rowLine = '';
  foreach($rec as $value)       {
    if(!isset($value) || $value == "")  {
      $value = "\t";
    }   else  {
//It escape all the special charactor, quotes from the data.
      $value = strip_tags(str_replace('"', '""', $value));
      $value = '"' . $value . '"' . "\t";
    }
    $rowLine .= $value;
  }
  $setData .= trim($rowLine)."\n";
}
  $setData = str_replace("\r", "", $setData);

if ($setData == "") {
  $setData = "\nNo matching records found\n";
}
$setCounter = mysql_num_fields($setRec);

//This Header is used to make data download instead of display the data
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$setExcelName."_Report.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo ucwords($setMainHeader)."\n".$setData."\n";
?>

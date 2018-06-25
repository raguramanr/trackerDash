<html>
<body>
<form method="post" action="<?php echo $PHP_SELF?>">
<?php

#########################################################################################
#                                                                                       #
#  Function to return list of users from database					#
#                                                                                       #
#########################################################################################
function getCRList() {
include 'db_connect.php';

$sql = "SELECT bugNumber, severity, priority, globalState, component, subComponent, releaseState, releaseDetected, targetReleaseId, 
releaseTracking.assignedTo, creatorManager.ldapManagerName as creatorManager, verifierManager.ldapManagerName as verifierManager 
from bugDescriptions 
LEFT JOIN releaseTracking USING(bugNumber)
LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.username
LEFT JOIN users as verifierManager ON releaseTracking.assignedTo=verifierManager.username
 WHERE (releaseDetected='EXOS 22.4.1'  || targetReleaseId='3358') 
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
 $releaseName = 'EXOS 22.4.1';
 $targetRelease = '3358';

   	if((($globalState=="Assigned" || $globalState=="Assigned Review" || $globalState=="Build Pending" || $globalState=="Committing" || $globalState=="Idle" || $globalState=="Study") && (is_null($targetReleaseId))) || ((($releaseState=="Assigned" && ($globalState!="Feedback Needed" && $globalState!="Verify No Change" && $globalState!="Verify Duplicate")) || $releaseState=="Assigned Review" || $releaseState=="Build Pending" || $releaseState=="Committing") && $targetReleaseId==$targetRelease)) {
     		@$row['crState']="open";
   	} elseif ((($globalState=="Closed" || $globalState=="Deferred" || $globalState=="Duplicate" || $globalState=="No Change" || $globalState=="Released" || $globalState=="Unverified Released " || $globalState=="Verified" || $globalState=="Task Complete") && $targetReleaseId=="") || (($releaseState=="Verified" || $releaseState=="Released" || $releaseState=="Unverified Released" || $releaseState=="Task Complete") && $targetReleaseId==$targetRelease)) {
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
 #print_r($value);
 $conn->close();
}



#########################################################################################
#                                                                                       #
#  Print all record									#
#                                                                                       #
#########################################################################################
function printCRList($arrayName) {
echo "<b><br>Printing CR data</b><br>";
echo "<table border=1>\n";
echo "<tr>
        <td class=a>crState</td>
        <td class=a>BugNumber</td>
        <td class=a>severity</td>
        <td class=a>Priority</td>
        <td class=a>globalState</td>
        <td class=a>releaseState</td>
        <td class=a>releaseDetected</td>
        <td class=a>targetReleaseId</td>
        <td class=a>assignedTo</td>
        <td class=a>CreatorMgr</td>
        <td class=a>VerifierMgr</td>
        </tr>\n";

foreach($arrayName as $row) {
        printf("<tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                </tr>\n",
                $row["crState"],
                $row["bugNumber"],
                $row["severity"],
                $row["priority"],
                $row["globalState"],
                $row["releaseState"],
                $row["releaseDetected"],
                $row["targetReleaseId"],
                $row["assignedTo"],
                $row["creatorManager"],
                $row["verifierManager"]);
}

return $crState;
echo "</tr></table>";
}


#########################################################################################
#                                                                                       #
#  Main Function starts									#
#                                                                                       #
#########################################################################################
$result = getCRList();
$mgrName   = array("Gopla Ramkumar", "Raj Velusamy", "Shankar Palanivel", "Raguraman Rajan", "Uma Parthasarathy", "Suresh Babu Thuravupala");
$crState   = array("open"=>"0", "closed"=>"0", "sqaPending"=>"0", "futureScope"=>"0", "unCategorized"=>"0");

######################################################################
#Printing the Total CRs by State
######################################################################
foreach($result as $data)
{
    @$crState[$data['crState']]++;
}

echo "<table border=1>\n";
echo "<tr>
        <td>Total Cases</td>
        <td>Open</td>
        <td>Closed</td>
        <td>SQA Pending</td>
        <td>Future Scoped</td>
        <td>Uncategorized</td>";
echo "</tr><tr>
        <td>". sizeof($result). "</td>
        <td>$crState[open]</td>
        <td>$crState[closed]</td>
        <td>$crState[sqaPending]</td>
        <td>$crState[futureScope]</td>
        <td>$crState[unCategorized]</td>";
echo "</tr></table><br><br>";

######################################################################
#Printing CRs per manager
######################################################################
foreach($result as $data) {
    @$crManager[$data['creatorManager']][total]++;
    @$crManager[$data['creatorManager']][$data['crState']]++;
    if($data['crState']=="sqaPending" && in_array($data['verifierManager'], $mgrName) && !in_array($data['creatorManager'], $mgrName)) {
      @$crManager[$data['verifierManager']][$data['crState']]++;
    }
}

arsort($crManager);

echo "<table border=1>\n";
echo "<tr>
        <td>Manager Name</td>
        <td>Total </td>
        <td>Open</td>
        <td>Closed</td>
        <td>SQA Pending</td>
        <td>Future Scope</td>
        <td>Uncategorized</td>
        </tr><tr>";

foreach ($crManager as $key=>$value) {
    if (in_array($key, $mgrName)) {
     echo "<td>$key</td>";
     echo "<td>$value[total]</td>";
     echo "<td>$value[open]</td>";
     echo "<td>$value[closed]</td>";
     echo "<td>$value[sqaPending]</td>";
     echo "<td>$value[futureScope]</td>";
     echo "<td>$value[unCategorized]</td></tr>";
    }
}
echo "</table>";
echo "<br>";

######################################################################
#Printing top 5 components
######################################################################
$countDisplay=10;
$itemToList = "component";

foreach($result as $data)
{
    @$crItemFull[$data[$itemToList]]++;
}

arsort($crItemFull);
$crItem = array_slice($crItemFull, 0, $countDisplay, true);
#print_r($crItem);

echo "<table border=1>\n";
echo "<tr>
        <td>Component</td>
        <td>Total </td>
        </tr><tr>";

foreach ($crItem as $key=>$value) {
     echo "<td>$key</td>";
     echo "<td>$crItem[$key]</td></tr>";
}
echo "</table>";
echo "<br>";


######################################################################
#Printing priority Vs Severity Table
######################################################################

foreach($result as $data)
{
    @$crPriority[$data['priority']][$data['severity']]++;
}
ksort($crPriority);

#print_r($crPriority);

echo "<table border=1>\n";
echo "<tr>";
echo "<th>Priority</th>";
echo "<th>1-Crash</th>";
echo "<th>2-Major</th>";
echo "<th>3-Minor</th>";
echo "<th>4-Trivial</th>";
echo "<th>5-New Feature</th>";
echo "<th>Total</th>";
echo "</tr>";

foreach ($crPriority as $key=>$value) {
     echo "<td>$key</td>";
     echo "<td>".$value['1 - Crash']."</td>";
     echo "<td>".$value['2 - Major']."</td>";
     echo "<td>".$value['3 - Minor']."</td>";
     echo "<td>".$value['4 - Trivial']."</td>";
     echo "<td>".$value['5 - New Feature']."</td>";
     echo "<td>".array_sum($value)."</td>";
 
     echo "</tr>";
}
echo "</table>";

#printCRList($result);

?>
</body>
</html>

<!DOCTYPE html>
<?php
include 'template.php';

#########################################################################################
#                                                                                       #
# Function to print the CR Analysis Table                                               #
#                                                                                       #
#########################################################################################
function printCRAnalysis($releaseName) {
include 'db_connect.php';
include 'procs.php';
include 'template.php';

$targetRelease = getDetail($conn, "SELECT releaseId as value FROM releases WHERE productId=132 AND releaseName='$releaseName'");
$keyMap = $GLOBALS['keyMapName'];
$metadataKeyMapId = getDetail($conn, "select metadataKeyMapId as value from metadataKeyMap where keyName like '%$keyMap[$releaseName]%'");

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
                        "searching": false,
                        dom: 'Bfrtip',
                        buttons: [
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
                        "searching": false,
                        dom: 'Bfrtip',
                        buttons: [
                        ]
                } );
        } );
        </script>

HTML;
echo $filterScript;
echo $topBarwoSearch;
$dummy = 0;

$sqaMgr=array("Gopla Ramkumar","Raj Velusamy","Shankar Palanivel","Uma Parthasarathy","Raguraman Rajan");
$autoMgr=array("Suresh Babu Thuravupala");
$exosMgr=array("Gopla Ramkumar","Raj Velusamy","Shankar Palanivel","Uma Parthasarathy","Raguraman Rajan","Suresh Babu Thuravupala");

$crList = getCRList($conn,$releaseName,$targetRelease);
$sqaPendingList = getSQAPendingList($conn,$releaseName,$targetRelease);
$EXOS2221="EXOS 22.2.1";
$EXOS2231="EXOS 22.3.1";
$EXOS2241="EXOS 22.4.1";
$EXOS2251="EXOS 22.5.1";
$EXOS2261="EXOS 22.6.1";
$EXOS2271="EXOS 22.7.1";
$EXOS3011="EXOS 30.1.1";
$EXOS3021="EXOS 30.2.1";

      #<!-- Right side column. Contains the navbar and content of the page -->
      echo "<div class=\"content-wrapper\">";
        #<!-- Content Header (Page header) -->
        echo "<section class=\"content-header\">";
          echo "<h1>";
            echo "$releaseName - CR Analysis";
            echo "<small></small>";
          echo "</h2>";
          echo "<ol class=\"breadcrumb\">";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2221) . " ><font size=3>EXOS 22.2.1</font></a></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2231) . " ><font size=3>EXOS 22.3.1</font></a></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2241) . " ><font size=3>EXOS 22.4.1</font></a></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2251) . " ><font size=3>EXOS 22.5.1</font></a></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2261) . " ><font size=3>EXOS 22.6.1</a></font></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2271) . " ><font size=3>EXOS 22.7.1</a></font></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS3011) . " ><font size=3>EXOS 30.1.1</a></font></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS3021) . " ><font size=3>EXOS 30.2.1</a></font></li>";
            echo "<li><a href=downloadExcel.php?getReport=allCRs&releaseName=". urlencode($releaseName) . "&targetRelease=$targetRelease&metadataKeyMapId=$metadataKeyMapId><font size=3><i class=\"fa fa-cloud-download\"></i></a></font></li>";
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

                echo "<table border=0 align=center width=85%>";
                    echo "<tr height=25 bgcolor=65535><td colspan=3> <font color=white><center><b>$releaseName CRs - Including Retargets </td>";
		    echo "<td bgcolor=605CA8> <font color=white><center><b>$releaseName - New Feature CRs </td></tr>";
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr>";
                     echo "<td valign=top>";

                        ##Master Table - Row 1 Col 1
                        echo "<table border=1 width=90% align=left>"; 
                                echo "<tr bgcolor=65535><td colspan=2><font color=white><center><b>Total CRs - Release Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Release</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";

                                $totalCount=0;
                                foreach($crList as $data) {
                                    @$crReleaseDetected[$data['releaseDetected']]++;
                                    $totalCount++;
                                }


                                echo "<tr>";
                                echo "<td>" . $releaseName ." CRs</td>";
                                echo "<td align=center>". $crReleaseDetected[$releaseName]."</td>";
                                echo "</tr>";

                                $reTargetCRCount = $totalCount - $crReleaseDetected[$releaseName];
                                echo "<tr>";
                                echo "<td>Retargeted CRs</td>";
                                echo "<td align=center>". $reTargetCRCount ."</td>";
                                echo "</tr>";

                                echo "<tr>";
                                echo "<td><b>Total CRs</td>";
                                echo "<td align=center><b>". $totalCount ."</td>";
                                echo "</tr>";

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 1 Col 2
                        echo "<table border=1 width=90% align=center>"; 
                                echo "<tr bgcolor=65535><td colspan=3><font color=white><center><b>Total CRs - State Distribution </td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>CR State</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>% Total</th>";
                                echo "</tr>";

				$totalCount=0;
                                foreach($crList as $data) {
                                    @$crState[$data['crState']]++;
                                    $totalCount++;
                                }

                                arsort($crState);
                                foreach($crState as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "<td align=center>". round(($value/$totalCount)*100) ."%</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 1 Col 3
                        echo "<table border=1 width=90% align=right>"; 
                                echo "<tr bgcolor=65535><td colspan=3><font color=white><center><b>Total CRs - Collateral/Other CRs</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Regression Flag</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>% Total</th>";
                                echo "</tr>";


				$totalCount=0;
                                foreach($crList as $data) {
                                    @$crRegression[$data['passedPreviously']]++;
                                    $totalCount++;
                                }

                                foreach($crRegression as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "<td align=center>". round(($value/$totalCount)*100) ."%</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";

                     echo "<td rowspan=5 valign=top>";
                        ##Master Table - Row 1 Col 4
                        echo "<table border=1 width=90% align=right>";
                                echo "<tr bgcolor=3C8DBC><td colspan=4><font color=white><center><b>New Feature CRs</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Feature Name</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>Assigned</th>";
                                echo "<th bgcolor=DDEBF7><center>P1/P2</th>";
                                echo "</tr>";


				#Listing all MetaData CRs
                                unset($metaData);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$metaData[$data['metaData']]++;
                                        }
                                }

				#Listing all MetaData CRs that are in OPEN State
                                unset($metaDataOpen);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName && $data[crState] == "open") {
                                                @$metaDataOpen[$data['metaData']]++;
                                        }
                                }


				#Listing all P1/P2 MetaData CRs and that are in OPEN State
                                unset($metaDataP1P2);
                                unset($metaDataP1P2Open);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName && (($data[priority] == "1 - Critical") || ($data[priority] == "2 - Urgent"))) {
                                                @$metaDataP1P2[$data['metaData']]++;
						$metaDataP1P2Count++;
						if($data[crState] == "open") {
							@$metaDataP1P2Open[$data['metaData']]++;	
							$metaDataP1P2OpenCount++;
						}
			
                                        }
                                }

                                arsort($metaData);
                                $totalCount=0;
				$metaDataP1P2Count=0;
                                $metaDataP1P2OpenCount=0;
                                foreach($metaData as $key=>$value) {
                                        if ($key != "") {
                                          echo "<tr>";
                                          echo "<td>$key</td>";
                                          echo "<td align=center>$value</td>";
                                          echo "<td align=center>$metaDataOpen[$key]</td>";
                                          echo "<td align=center>$metaDataP1P2Open[$key]</td>";
                                          echo "</tr>";
                                          $totalCount = $totalCount + $value;
					  $metaDataOpenCount = $metaDataOpen[$key] + $metaDataOpenCount;
					  $metaDataP1P2Count = $metaDataP1P2[$key] + $metaDataP1P2Count;
					  $metaDataP1P2OpenCount = $metaDataP1P2Open[$key] + $metaDataP1P2OpenCount;
                                        }
                                }
				
                                echo "<tr>";
			        echo "<td><b>Total CRs</td>";
			        echo "<td align=center><b>$totalCount </td>";
				echo "<td align=center><b>$metaDataOpenCount</td>";
				echo "<td align=center><b>$metaDataP1P2OpenCount</td>";
				echo "</tr>";

                        echo "</table>";


                     echo "</tr>";

                    ####### Master Table - Beginning of Second Row 
                    echo "<tr><td colspan=3>&nbsp;</td></tr>";
                    echo "<tr height=25 bgcolor=605CA8><td colspan=3> <font color=white><center><b>$releaseName - All CRs</td></tr>";
                    echo "<tr><td colspan=3>&nbsp;</td></tr>";
                    echo "<tr>";
                     echo "<td valign=top>";

                        ##Master Table - Row 2 Col 1
                        echo "<table border=1 width=90% align=left>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=3><font color=white><center><b>State Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>CR State</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>% Total</th>";
                                echo "</tr>";

                                unset($crState);
                                $totalCount=0;
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$crState[$data['crState']]++;
                                                $totalCount++;
                                        }
                                }

                                arsort($crState);
                                foreach($crState as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "<td align=center>". round(($value/$totalCount)*100) ."% </td>";
                                        echo "</tr>";
                                }
                                echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td><td></td></tr>";

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 2 Col 2
                        echo "<table border=1 width=90% align=center>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=3><font color=white><center><b>Team Distribution </td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Team</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>% Total</th>";
                                echo "</tr>";


                                unset($crCreatorManager);
				$totalCount=0;
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                            if(in_array($data['creatorManager'],$sqaMgr)) {
                                                @$crCreatorManager['SQA']++;
                                            } elseif (in_array($data['creatorManager'],$autoMgr)) {
                                                @$crCreatorManager['Automation']++;
                                            } else {
                                                @$crCreatorManager['Others']++;
                                            }
					$totalCount++;
                                        }
                                }


                                arsort($crCreatorManager);
                                foreach($crCreatorManager as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "<td align=center>". round(($value/$totalCount)*100) ."%</td>";
                                        echo "</tr>";
                                }
				echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td><td></td></tr>";

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 2 Col 3
                        echo "<table border=1 width=90% align=right>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=5><font color=white><center><b>Collateral/Other CRs</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Team</th>";
                                echo "<th bgcolor=DDEBF7><center>Yes</th>";
                                echo "<th bgcolor=DDEBF7><center>No</th>";
                                echo "<th bgcolor=DDEBF7><center>Blank</th>";
                                echo "<th bgcolor=DDEBF7><center>% Collateral</th>";
                                echo "</tr>";

                                unset($crRegression);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
					    if($data['passedPreviously'] == "") {
						$data['passedPreviously'] = "Blank";
					    }
                                            if(in_array($data['creatorManager'],$sqaMgr)) {
                                                @$crRegression['SQA'][$data['passedPreviously']]++;
                                            } elseif (in_array($data['creatorManager'],$autoMgr)) {
                                                @$crRegression['Automation'][$data['passedPreviously']]++;
                                            } else {
                                                @$crRegression['Others'][$data['passedPreviously']]++;
                                            }
                                        }
                                }


				arsort($crRegression);
				foreach ($crRegression as $key => $item) {
                                	echo "<tr>";
					echo "<td>$key</td>";
					echo "<td><center>$item[Yes]</td>";
					echo "<td><center>$item[No]</td>";
					echo "<td><center>$item[Blank]</td>";
					echo "<td><center>". round(($item[Yes]/$totalCount)*100) ."%</td>";
                                	echo "</tr>";
				}

                        echo "</table>";
                     echo "</td>";

                     echo "</tr>";


                    ####### Master Table - Beginning of Third Row - Print Priority Tables 
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr height=25 bgcolor=605CA8><td colspan=3> <font color=white><center><b>$releaseName - P1/P2 CRs</td>";
                    echo "<td bgcolor=0075C2> <font color=white><center><b>Top 10 Component/SubComponent</td></tr>";
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr>";
                     echo "<td valign=top>";

                        ##Master Table - Row 3 Col 1
                        echo "<table border=1 width=90% align=left>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=3><font color=white><center><b>Manager Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Manager</th>";
                                echo "<th bgcolor=DDEBF7><center>&nbsp;&nbsp;Total&nbsp;&nbsp;</th>";
                                echo "<th bgcolor=DDEBF7><center>&nbsp;&nbsp;Open&nbsp;&nbsp;</th>";
                                echo "</tr>";

                                unset($crCreatorManager);
                                unset($crCreatorManagerP1P2Open);
				$totalCount=0;
				$totalP1P2Count=0;
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
					    if(in_array($data['creatorManager'],$exosMgr) && (($data[priority] == "1 - Critical") || ($data[priority] == "2 - Urgent"))) {
                                                @$crCreatorManager[$data[creatorManager]]++;	
						$totalCount++;
						if($data[crState] == "open") {
						   @$crCreatorManagerP1P2Open[$data[creatorManager]]++;
						   $totalP1P2Count++;
						}
					    }

                                        }
                                }

				arsort($crCreatorManager);
                                foreach($crCreatorManager as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "<td align=center>$crCreatorManagerP1P2Open[$key]</td>";
                                        echo "</tr>";
                                }

                                echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount</td><td align=center><b>$totalP1P2Count</td></tr>";

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 3 Col 2
                        echo "<table border=1 width=90% align=center>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=3><font color=white><center><b>Team Distribution </td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Team</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>% Total</th>";
                                echo "</tr>";

                                unset($crCreatorManager);
                                $totalCount=0;
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName && (($data[priority] == "1 - Critical") || ($data[priority] == "2 - Urgent"))) {
                                            if(in_array($data['creatorManager'],$sqaMgr)) {
                                                @$crCreatorManager['SQA']++;
                                            } elseif (in_array($data['creatorManager'],$autoMgr)) {
                                                @$crCreatorManager['Automation']++;
                                            } else {
                                                @$crCreatorManager['Others']++;
                                            }
						$totalCount++;
                                        }
                                }


                                arsort($crCreatorManager);
                                foreach($crCreatorManager as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "<td align=center>". round(($value/$totalCount)*100) ."%</td>";
                                        echo "</tr>";
                                }
                                echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td><td></td></tr>";

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 3 Col 3
                        echo "<table border=1 width=90% align=right>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=5><font color=white><center><b>Collateral/Other CRs</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Team</th>";
                                echo "<th bgcolor=DDEBF7><center>Yes</th>";
                                echo "<th bgcolor=DDEBF7><center>No</th>";
                                echo "<th bgcolor=DDEBF7><center>Blank</th>";
                                echo "<th bgcolor=DDEBF7><center>% Collateral</th>";
                                echo "</tr>";

                                unset($crRegression);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName && (($data[priority] == "1 - Critical") || ($data[priority] == "2 - Urgent"))) {
                                            if($data['passedPreviously'] == "") {
                                                $data['passedPreviously'] = "Blank";
                                            }
                                            if(in_array($data['creatorManager'],$sqaMgr)) {
                                                @$crRegression['SQA'][$data['passedPreviously']]++;
                                            } elseif (in_array($data['creatorManager'],$autoMgr)) {
                                                @$crRegression['Automation'][$data['passedPreviously']]++;
                                            } else {
                                                @$crRegression['Others'][$data['passedPreviously']]++;
                                            }
                                        }
                                }


                                arsort($crRegression);
                                foreach ($crRegression as $key => $item) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td><center>$item[Yes]</td>";
                                        echo "<td><center>$item[No]</td>";
                                        echo "<td><center>$item[Blank]</td>";
                                        echo "<td><center>". round(($item[Yes]/$totalCount)*100) ."%</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";


                     echo "<td valign=top>";
                        ##Master Table - Row 3 Col 4
			echo "<table align=center border=0 width=100%><tr><td valign=top>";
                          echo "<table border=1 width=90% align=right>";
                                echo "<tr bgcolor=3C8DBC><td colspan=3><font color=white><center><b>Top 10 Component</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Component</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>% Total</th>";
                                echo "</tr>";


                                unset($componentListFull);
				$totalCRCount=0;
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$componentListFull[$data['component']]++;
						$totalCRCount++;
                                        }
					
                                }
				
				arsort($componentListFull);
				$componentList = array_slice($componentListFull, 0, 10, true);
				$totalCount=0;
                                foreach($componentList as $key=>$value) {
					if ($key != "") {
                                          echo "<tr>";
                                          echo "<td>$key</td>";
                                          echo "<td align=center>$value</td>";
                                          echo "<td><center>". round(($value/$totalCRCount)*100) ."%</td>";
                                          echo "</tr>";
					  $totalCount = $totalCount + $value;
					}
                                }
				echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td><td></td></tr>";
                          echo "</table>";

			echo "</td><td valign=top>";

                          echo "<table border=1 width=90% align=right>";
                                echo "<tr bgcolor=3C8DBC><td colspan=3><font color=white><center><b>Top 10 SubComponent</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>SubComponent</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "<th bgcolor=DDEBF7><center>% Total</th>";
                                echo "</tr>";


                                unset($subcomponentListFull);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$subcomponentListFull[$data['subComponent']]++;
                                        }
                                }

                                arsort($subcomponentListFull);
                                $subcomponentList = array_slice($subcomponentListFull, 0, 11, true);
                                $totalCount=0;
                                foreach($subcomponentList as $key=>$value) {
                                        if ($key != "") {
                                          echo "<tr>";
                                          echo "<td>$key</td>";
                                          echo "<td align=center>$value</td>";
                                          echo "<td><center>". round(($value/$totalCRCount)*100) ."%</td>";
                                          echo "</tr>";
                                          $totalCount = $totalCount + $value;
                                        }
                                }
                                echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td><td></td></tr>";
                          echo "</table>";
			echo "</td></tr></table>";
                     echo "</td>";
		     echo "</tr>";


                    ####### Master Table - Beginning of Fourth Row - Print SQA Pending CRs
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr height=25 bgcolor=605CA8><td colspan=4> <font color=white><center><b>EXOS SQA - CRs to Verify and close</td>";
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";

                    echo "<tr>";
                     echo "<td valign=top>";

                        ##Master Table - Row 4 Col 1
                        echo "<table border=1 width=90% align=left>";
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>SQA Pending</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Manager</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";

                                unset($crCreatorManager);
                                $totalCount=0;
                                foreach($sqaPendingList as $data) {
                                                @$crCreatorManager[$data[ldapManagerName]]++;
                                                $totalCount++;
                                }

                                arsort($crCreatorManager);
                                foreach($crCreatorManager as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "</tr>";
                                }

                                echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td></tr>";

                        echo "</table>";
                     echo "</td>";


                     echo "<td valign=top colspan=3>";

                        ##Master Table - Row 4 Col 2
                        echo "<table border=1 width=100% align=center>";
                                echo "<tr bgcolor=3C8DBC><td colspan=13><font color=white><center><b>SQA Pending</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>CR Number</th>";
                                echo "<th bgcolor=DDEBF7>Severity</th>";
                                echo "<th bgcolor=DDEBF7>Priority</th>";
                                echo "<th bgcolor=DDEBF7>GlobalState</th>";
                                echo "<th bgcolor=DDEBF7>ReleaseState</th>";
                                echo "<th bgcolor=DDEBF7>Creator</th>";
                                echo "<th bgcolor=DDEBF7>AssignedTo</th>";
                                echo "<th bgcolor=DDEBF7>Manager</th>";
                                echo "<th bgcolor=DDEBF7>Transition</th>";
                                echo "<th bgcolor=DDEBF7>Days Pending</th>";
                                echo "<th bgcolor=DDEBF7>Related CR</th>";
                                echo "<th bgcolor=DDEBF7>Related GlobalState</th>";
                                echo "<th bgcolor=DDEBF7>Related CR State</th>";
                                echo "</tr>";

				#Sorting the array based on ldapManagerName
				#$crListSort = array();
				#foreach ($sqaPendingList as $key => $row)
				#{
    				#	$sqaPendingListSort[$key] = $row['ldapManagerName'];
				#}
				#array_multisort($sqaPendingListSort, SORT_ASC, $sqaPendingList);

                                foreach($sqaPendingList as $data) {
                                        	echo "<tr>";
                                        	echo "<td><a href=https://tracker.extremenetworks.com/cgi/trackerReport.pl?bugNumber=$data[bugNumber] target=\"_blank\">".$data[bugNumber]."</a></td>";
                                        	echo "<td>$data[severity]</td>";
                                        	echo "<td>$data[priority]</td>";
                                        	echo "<td>$data[globalState]</td>";
                                        	echo "<td>$data[releaseState]</td>";
                                        	echo "<td>$data[creator]</td>";
                                        	echo "<td>$data[assignedTo]</td>";
                                        	echo "<td>$data[ldapManagerName]</td>";
                                        	echo "<td>$data[transitiondate]</td>";
                                        	echo "<td><center>$data[numDays]</td>";
                                        	echo "<td>$data[relatedCRID]</td>";
                                        	echo "<td><center>$data[relatedCRGlobalState]</td>";
                                        	echo "<td><center>$data[relatedCRState]</td>";
                                        	echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";

                #Master table end tag
                echo "</tr>";
                echo "</table>";

                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</div><!-- /.col -->";
          echo "</div><!-- /.row -->";
  echo "</body>";
$footer = <<<HTML
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
    </div>
    <strong><center>RDI History </strong> [22.1.1 - 3829] [22.2.1 - 3145] [22.3.1 - 3200] [22.4.1 - 2516] [22.5.1-  2119]
  </footer>
HTML;

echo $footer;
echo "</html>";

}

#########################################################################################
#                                                                                       #
# Default EXOS Set to EXOS 22.4.1, otherwise clicked different link                     #
#                                                                                       #
#########################################################################################
if($_GET[releaseName] != "") {
      printCRAnalysis($_GET[releaseName]);
} else {
      printCRAnalysis("EXOS 22.6.1");
}


?>

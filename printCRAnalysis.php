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
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2251) . " ><font size=3>EXOS 22.5.1</font></a></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2261) . " ><font size=3>EXOS 22.6.1</a></font></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS2271) . " ><font size=3>EXOS 22.7.1</a></font></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS3011) . " ><font size=3>EXOS 30.1.1</a></font></li>";
            echo "<li><a href=$PHP_SELF?releaseName=" . urlencode($EXOS3021) . " ><font size=3>EXOS 30.2.1</a></font></li>";
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

                echo "<table border=0 align=center width=80%>";
                    echo "<tr height=25 bgcolor=605CA8><td colspan=4> <font color=white><center><b>$releaseName CRs - Including Retargets </td></tr>";
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr>";
                     echo "<td valign=top>";

                        ##Master Table - Row 1 Col 1
                        echo "<table border=1 width=90% align=left>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Total CRs - Release Distribution</td></tr>";
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
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Total CRs - State Distribution </td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>CR State</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";

                                foreach($crList as $data) {
                                    @$crState[$data['crState']]++;
                                    $totalCount++;
                                }

                                arsort($crState);
                                foreach($crState as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 1 Col 3
                        echo "<table border=1 width=90% align=right>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Total CRs - Regression Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Regression Flag</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";


                                foreach($crList as $data) {
                                    @$crRegression[$data['passedPreviously']]++;
                                    $totalCount++;
                                }

                                foreach($crRegression as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";
                     echo "</tr>";

                    ####### Master Table - Beginning of Second Row 
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr height=25 bgcolor=605CA8><td colspan=4> <font color=white><center><b>$releaseName - All CRs</td></tr>";
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr>";
                     echo "<td valign=top>";

                        ##Master Table - Row 2 Col 1
                        echo "<table border=1 width=90% align=left>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>State Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>CR State</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
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
                                        echo "</tr>";
                                }
                                echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td></tr>";

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 2 Col 2
                        echo "<table border=1 width=90% align=center>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Functional Distribution </td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Team</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";


                                unset($crCreatorManager);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                            if(in_array($data['creatorManager'],$sqaMgr)) {
                                                @$crCreatorManager['SQA']++;
                                            } elseif (in_array($data['creatorManager'],$autoMgr)) {
                                                @$crCreatorManager['Automation']++;
                                            } else {
                                                @$crCreatorManager['Others']++;
                                            }
                                        }
                                }


                                arsort($crCreatorManager);
                                foreach($crCreatorManager as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 2 Col 3
                        echo "<table border=1 width=90% align=right>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Regression Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Regression Flag</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";


                                unset($crRegression);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$crRegression[$data['passedPreviously']]++;
                                        }
                                }

                                foreach($crRegression as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";


                     echo "<td valign=top>";
                        ##Master Table - Row 2 Col 4
                        echo "<table border=1 width=90% align=right>";
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>New Feature CRs</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Feature Name</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";


                                unset($metaData);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$metaData[$data['metaData']]++;
                                        }
                                }
				
				arsort($metaData);
				$totalCount=0;
                                foreach($metaData as $key=>$value) {
					if ($key != "") {
                                          echo "<tr>";
                                          echo "<td>$key</td>";
                                          echo "<td align=center>$value</td>";
                                          echo "</tr>";
					  $totalCount = $totalCount + $value;
					}
                                }
				echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td></tr>";

                        echo "</table>";
                     echo "</td>";
                     echo "</tr>";


                    ####### Master Table - Beginning of Third Row - Print Priority Tables 
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr height=25 bgcolor=605CA8><td colspan=4> <font color=white><center><b>$releaseName - P1/P2 CRs</td></tr>";
                    echo "<tr><td colspan=4>&nbsp;</td></tr>";
                    echo "<tr>";
                     echo "<td valign=top>";

                        ##Master Table - Row 3 Col 1
                        echo "<table border=1 width=90% align=left>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Manager Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Manager</th>";
                                echo "<th bgcolor=DDEBF7><center>P1/P2 CRs</th>";
                                echo "</tr>";

                                unset($crCreatorManager);
				$totalCount=0;
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
					    if(in_array($data['creatorManager'],$exosMgr)) {
                                                @$crCreatorManager[$data[creatorManager]]++;	
						$totalCount++;
					    }
                                        }
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

                     echo "<td valign=top>";
                        ##Master Table - Row 3 Col 2
                        echo "<table border=1 width=90% align=center>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Functional Distribution </td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>CR State</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";

                                unset($crCreatorManager);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                            if(in_array($data['creatorManager'],$sqaMgr)) {
                                                @$crCreatorManager['SQA']++;
                                            } elseif (in_array($data['creatorManager'],$autoMgr)) {
                                                @$crCreatorManager['Automation']++;
                                            } else {
                                                @$crCreatorManager['Others']++;
                                            }
                                        }
                                }


                                arsort($crCreatorManager);
                                foreach($crCreatorManager as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";

                     echo "<td valign=top>";
                        ##Master Table - Row 2 Col 3
                        echo "<table border=1 width=90% align=right>"; 
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>Regression Distribution</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Regression Flag</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";


                                unset($crRegression);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$crRegression[$data['passedPreviously']]++;
                                        }
                                }

                                foreach($crRegression as $key=>$value) {
                                        echo "<tr>";
                                        echo "<td>$key</td>";
                                        echo "<td align=center>$value</td>";
                                        echo "</tr>";
                                }

                        echo "</table>";
                     echo "</td>";


                     echo "<td valign=top>";
                        ##Master Table - Row 2 Col 4
                        echo "<table border=1 width=90% align=right>";
                                echo "<tr bgcolor=3C8DBC><td colspan=2><font color=white><center><b>New Feature CRs</td></tr>";
                                echo "<tr>";
                                echo "<th bgcolor=DDEBF7>Feature Name</th>";
                                echo "<th bgcolor=DDEBF7><center>Total</th>";
                                echo "</tr>";


                                unset($metaData);
                                foreach($crList as $data) {
                                        if($data[releaseDetected] == $releaseName) {
                                                @$metaData[$data['metaData']]++;
                                        }
                                }
				
				arsort($metaData);
				$totalCount=0;
                                foreach($metaData as $key=>$value) {
					if ($key != "") {
                                          echo "<tr>";
                                          echo "<td>$key</td>";
                                          echo "<td align=center>$value</td>";
                                          echo "</tr>";
					  $totalCount = $totalCount + $value;
					}
                                }
				echo "<tr><td><b>Total CRs</td><td align=center><b>$totalCount </td></tr>";

                        echo "</table>";
                     echo "</td>";




                #Master table end tag
                echo "</tr>";
                echo "</table>";




                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</div><!-- /.col -->";
          echo "</div><!-- /.row -->";

/*
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

*/

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
# Default EXOS Set to EXOS 22.4.1, otherwise clicked different link                     #
#                                                                                       #
#########################################################################################
if($_GET[releaseName] != "") {
      printCRAnalysis($_GET[releaseName]);
} else {
      printCRAnalysis("EXOS 22.6.1");
}


?>

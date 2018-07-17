<!DOCTYPE html>
<?php
#########################################################################################
#                                                                                       #
#  Procedure to print the Overall HTML Page                                             #
#                                                                                       #
#########################################################################################
function main($releaseName) {
include 'db_connect.php';
include 'procs.php';
$targetRelease = getDetail($conn, "SELECT releaseId as value FROM releases WHERE productId=132 AND releaseName='$releaseName'");
$keyMap = $GLOBALS['keyMapName'];
$metadataKeyMapId = getDetail($conn, "select metadataKeyMapId as value from metadataKeyMap where keyName like '%$keyMap[$releaseName]%'");
include 'template.php';

#########################################################################################
#                                                                                       #
#  Procedure to print the CR Table							#
#                                                                                       #
#########################################################################################
if ($_GET[printCQG] == "yes") {
printCQGTable($conn);
exit;
}

#########################################################################################
#                                                                                       #
#  Quuerying the CR List								#
#                                                                                       #
#########################################################################################
echo "<html>";
$crList = getCRList($conn,$releaseName,$targetRelease);
writeArraytoFile($crList, "allCRList".$targetRelease);

foreach($crList as $data) {
    @$crState[$data['crState']]++;
    $totalCount++;
}


#########################################################################################
#                                                                                       #
#  Procedure to print the CR Table							#
#                                                                                       #
#########################################################################################
if ($_GET[printArray] == "yes") {
printTable($crList, $_GET[releaseName], $_GET[targetRelease], $_GET[crState]);
exit;
}

echo $headInclude;
echo $scriptInclude;
echo $topBar;
echo $leftMenu;

printHeader($releaseName, $targetRelease, $crList);

#########################################################################################
#                                                                                       #
#  Procedure to print the Popup Chart Page						# 
#                                                                                       #
#########################################################################################
if ($_GET[popup] == "yes") {
$renderChart = <<<HTML
<script type="text/javascript">
        FusionCharts.ready(function () {
                var myChart = new FusionCharts({
                        "type": "$_GET[chartType]",
                        "renderAt": "myChart",
                        "width": "100%",
                        "height": "100%",
                        "dataFormat": "xmlurl",
                        "exportEnabled": "1",
                        "dataSource": "Data/$_GET[chartFile].xml"
                });
myChart.render();
});
</script>
HTML;
echo $renderChart;

$popupData = <<<HTML
<section class="content">
          <div class="row">
            <div class="col-md-16">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">$_GET[chartTitle] - $_GET[releaseName]</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <div class="chart" id="myChart" style="height: 500px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
</section><!-- /.content -->
HTML;
echo $popupData;
echo $scriptInclude;
echo "</body>";
echo "</html>";
exit;
}


?>

<!-- Main Page Content Starts here -->
 <section class="content">
	<!-- Box-1 -->
	<!-- Printing RDI --> 
        <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row tile_count">
          <?php printRDI($conn,$releaseName,$targetRelease); ?>

        <!-- Printing Total EXOS CRs -->
            <?php
                $lastWeek = getLastNDays(7);
		$releaseCRstate=array("open"=>"0","closed"=>"0","sqaPending"=>"0","futureScope"=>"0");

                foreach($crList as $data) {
                  if ($data[releaseDetected] == $releaseName) {
                        @$releaseCRstate[$data['crState']]++;
			$totalreleaseCRstate++;
                  }
                }

            ?>
	<!-- Box-2 -->
	<!-- Printing EXOS Release Total CRs -->
            <div class="col-md-2">
              <div class="small-box bg-yellow">
               <div class="inner"><h3><?php echo $totalreleaseCRstate; ?></h3><p>
		<?php 
		echo $releaseName; 
		echo "- Total CRs";
		echo "<a target=\"_blank\" href='$PHP_SELF?printArray=yes&crState=allRelease&targetRelease=$targetRelease&releaseName=$releaseName'>";
                echo "&nbsp;&nbsp;<i style=\"color:white\" class=\"fa fa-arrow-right\"></i>";
		echo "</a>";
		?> 
		</p></div>
               <div class="icon"><i class="ion ion-stats-bars"></i></div>
              </div><!-- /.box -->
            </div><!-- /.col -->

	<!-- Box-3 -->
	<!-- Printing EXOS Release Open CRs -->
            <div class="col-md-2">
              <div class="small-box bg-red">
               <div class="inner"><h3><?php echo $releaseCRstate[open]; ?></h3><p>
                <?php
                echo $releaseName;
                echo "- Open CRs";
                echo "<a target=\"_blank\" href='$PHP_SELF?printArray=yes&crState=open&targetRelease=$targetRelease&releaseName=$releaseName'>";
                echo "&nbsp;&nbsp;<i style=\"color:white\" class=\"fa fa-arrow-right\"></i>";
                echo "</a>";
                ?>
		</p></div>
               <div class="icon"><i class="ion ion-pie-graph"></i></div>
              </div><!-- /.box -->
            </div><!-- /.col -->


	<!-- Box-4 -->	
	<!-- Printing EXOS Release closed CRs -->
            <div class="col-md-2">
              <div class="small-box bg-purple">
               <div class="inner"><h3><?php echo $releaseCRstate[sqaPending]; ?></h3><p>
                <?php
                echo $releaseName;
                echo "- SQA Action";
                echo "<a target=\"_blank\" href='$PHP_SELF?printArray=yes&crState=sqaPending&targetRelease=$targetRelease&releaseName=$releaseName'>";
                echo "&nbsp;&nbsp;<i style=\"color:white\" class=\"fa fa-arrow-right\"></i>";
                echo "</a>";
                ?>
		</p></div>
               <div class="icon"><i class="ion ion-checkmark"></i></div>
              </div><!-- /.box -->
            </div><!-- /.col -->

	<!-- Box-5 -->
	<!-- Printing Line Chart for last 7 days incoming --> 
            <div class="col-md-2">
              <div class="box box-solid"> 
                <div class="box-header">
                  <h3 class="box-title text-blue">Last 7 days Incoming</h3>
                </div><!-- /.box-header -->
                <div class="box-body text-center">
                  <?php printlastWeekIncoming($crList, $releaseName, $targetRelease, "lastWeekIncoming".$targetRelease) ?>
                  <div class="chart" id="<?php echo "lastWeekIncoming".$targetRelease; ?>" style="height: 40px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

	<!-- Box-6 -->
	<!-- Printing Pie Chart for Total CRs --> 
	<script type='text/javascript'>
	$(window).load(function(){
	var values = [<?php echo "$totalCount,$crState[open],$crState[closed]"; ?>];
	$('#sparkline').sparkline(values, {
	    type: "pie",
	    height: "85px",
	    tooltipFormat: '{{offset:offset}} - {{value}} ({{percent.1}}%)',
	    tooltipValueLookups: {
	        'offset': {
	            0: 'Total',
	            1: 'Open',
	            2: 'Closed'
	        }
	    },
	});
	});
	</script>

	 <div class="col-md-2 col-sm-4 col-xs-12" height="100px">
             <div class="small-box bg-teal">
                <div class="x_content">
                  <table class="chart" style="width:100%" height="103px">
		     <tr>
                        <td rowspan="5" align="center"> 
			<span id="sparkline">&nbsp;</span>
                        </td><td><b>
			<?php
                        echo "<a target=\"_blank\" href='$PHP_SELF?printArray=yes&crState=plusRetarget&targetrelease=$targetRelease&releaseName=$releaseName'>";
			?>
                        Includes Retargets</a></b></td> 
		     </tr> 
		  	 <tr><td>Total -  <?php echo "$totalCount"; ?></td></tr>
		  	 <tr><td>Open  -  <?php echo "$crState[open]"; ?></td></tr>
                         <tr><td>Closed - <?php echo "$crState[closed]"; ?></td></tr>
                         <tr><td></td></tr>
                      </td>
                  </table>
                </div>
              </div>
            </div>

          </div> <!-- /.row -->
          <!-- /top tiles -->


        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->
        <!-- Main content. Starting of ROW-1 R1C1 -->
          <div class="row">
        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->

       <!-- R1C1 --> 
       <!-- Printing Priority Pie Chart -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup1" data-toggle="tooltip" title="Priority Distribution Chart for the Specified Release CRs" onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=pie2d&chartFile=priorityChart".$targetRelease."&releaseName=".$releaseName."&chartTitle=Priority Distribution"; ?>','popup1','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">Priority Distribution</a></h3>
                </div>
                <div class="box-body chart-responsive">
		  <?php printPriorityDistribution($crList, $releaseName, $targetRelease, "priorityChart".$targetRelease) ?>
                  <div class="chart" id="<?php echo "priorityChart".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->

        <!-- R1C2 --> 
        <!-- Printing Top 10 metaData Component -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup2" data-toggle="tooltip" title="CRs raised for new Features in the specified Release and its Priority Distribution"  onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=stackedcolumn2d&chartFile=topmetaDataPriority".$targetRelease."&releaseName=".$releaseName."&chartTitle=New Features"; ?>','popup2','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">New Features</a></h3>
                </div>
                <div class="box-body chart-responsive">
		  <?php printnewFeaturePriority($crList, $releaseName, $targetRelease, "topmetaDataPriority".$targetRelease) ?>
                  <div class="chart" id="<?php echo "topmetaDataPriority".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->

        <!-- R1C3 --> 
        <!-- Printing Pie Chart for New/Legacy/Regression Issues  -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup3" data-toggle="tooltip" title="Distribution for New Features CRs/Legacy CRs (based on metaData) and total Regression Flagged CRs"onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=pie2d&chartFile=newvsLegacy".$targetRelease."&releaseName=".$releaseName."&chartTitle=New/Legacy/Regression Issues"; ?>','popup3','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">New/Legacy/Regression Issues</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php printLegacyRegressionIssues($crList, $releaseName, $targetRelease, "newvsLegacy".$targetRelease) ?>
                  <div class="chart" id="<?php echo "newvsLegacy".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->
        <!-- Closing of ROW-1 and Opening of ROW-2 -->
          </div><!-- /.row -->
          <div class="row">
        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->

        <!-- R2C1 --> 
        <!-- Printing Open P1/P2 CRs - EXOS Release Specific -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup4" data-toggle="tooltip" title="Distribution Chart for Total P1/P2 CRs with Current OPEN P1/P1 CRs" onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=pie2d&chartFile=openCR".$targetRelease."&releaseName=".$releaseName."&chartTitle=Open CRs - Priority Distribution"; ?>','popup4','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">Open CRs - Priority Distribution</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php printopenCRPriorityDistribution($crList, $releaseName, $targetRelease, "openCR".$targetRelease) ?>
                  <div class="chart" id="<?php echo "openCR".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->

        <!-- R2C2 --> 
        <!-- Printing Top 10 Component -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup5" data-toggle="tooltip" title="CR count for the Top 10 Components with their Priority Distribution" onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=stackedcolumn2d&chartFile=topComponentPriority".$targetRelease."&releaseName=".$releaseName."&chartTitle=Top 10 Component"; ?>','popup5','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">Top 10 Component</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php printtopComponentPriority($crList, $releaseName, $targetRelease, "topComponentPriority".$targetRelease) ?>
                  <div class="chart" id="<?php echo "topComponentPriority".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->
        <!-- R2C3 --> 
        <!-- Printing All Managers Total -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup6" data-toggle="tooltip" title="Total CRs filed in the specified released distributed by managers" onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=pie2d&chartFile=managerCRs".$targetRelease."&releaseName=".$releaseName."&chartTitle=Total CRs/Manager"; ?>','popup6','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">Total CRs/Manager</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php printCRbyManager($crList, $releaseName, $targetRelease, "managerCRs".$targetRelease) ?>
                  <div class="chart" id="<?php echo "managerCRs".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->


        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->
        <!-- Closing of ROW-2 and Opening of ROW-3 -->
          </div><!-- /.row -->
          <div class="row">
        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->

        <!-- R3C1 --> 
       <!-- Printing RBC Pie Chart -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup7" data-toggle="tooltip" title="RBC-INTERNAL Vs RBC-ORIGINAL flagged CRs for the specified Release" onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=pie2d&chartFile=rbcClassification".$targetRelease."&releaseName=".$releaseName."&chartTitle=RBC Original Vs Internal"; ?>','popup7','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">RBC Original Vs Internal</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php printRBCClassification($crList, $releaseName, $targetRelease, "rbcClassification".$targetRelease) ?>
                  <div class="chart" id="<?php echo "rbcClassification".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->
        <!-- R3C2 --> 
        <!-- Printing Find Vs Fix Table -->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup8" data-toggle="tooltip" title="Find Rate for the specified release along with Fix rate. Retargeted CRs included in the Fix Rate trend" onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=msspline&chartFile=findVsfix".$targetRelease."&releaseName=".$releaseName."&chartTitle=CR Find Vs Fix"; ?>','popup8','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">CR Find Vs Fix</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php printfindVsfix($crList, $releaseName, $targetRelease, "findVsfix".$targetRelease, $conn) ?>
                  <div class="chart" id="<?php echo "findVsfix".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->
        <!-- R3C3 --> 
       <!-- Printing Pie Chart for EXOS Total/Open/Closed/SQAPending/Retargeted CRs-->
            <div class="col-md-4">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><a href="" target="popup9" data-toggle="tooltip" title="Total Open/Closed/SQA Pending/Moved to Future CRs for the specified release. Doesnt Include retargeted CRs" onclick="window.open('<?php echo $PHP_SELF."?popup=yes&chartType=pie2d&chartFile=crStateDistribution".$targetRelease."&releaseName=".$releaseName."&chartTitle=State Distribution"; ?>','popup9','width=600,height=600,status=0,location=0,menubar=0,titlebar=0,toolbar=0,resizable=0'); return false;">State Distribution</a></h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php printCRStateDistribution($crList, $releaseName, $targetRelease, "crStateDistribution".$targetRelease) ?>
                  <div class="chart" id="<?php echo "crStateDistribution".$targetRelease; ?>" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->

         <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->
        <!-- Closing of ROW-3 3 -->
          </div><!-- /.row -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->


<?php
echo "</body>";
echo "</html>";
}

#########################################################################################
#                                                                                       #
# Default EXOS Set to EXOS 22.6.1, otherwise clicked different link                     #
#                                                                                       #
#########################################################################################
if(isset($_POST['releaseName'])) {
      main($_POST[releaseName]); 
} elseif ($_GET[releaseName] != "") {
      main($_GET[releaseName]);
} else {
      main("EXOS 22.6.1");
}
?>

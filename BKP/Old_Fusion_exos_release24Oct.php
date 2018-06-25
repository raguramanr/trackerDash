<!DOCTYPE html>
<?php
#########################################################################################
#                                                                                       #
#  Procedure to print the Overall HTML Page                                             #
#                                                                                       #
#########################################################################################
function main($releaseName) {
include("Chart/Code/PHP/Includes/FusionCharts.php");
include 'procs.php';
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

?>

        <!-- Main content -->
          <div class="row">
            <div class="col-md-6">

              <!-- DONUT CHART -->
              <div class="box box-danger">
                <div class="box-header">
                  <h3 class="box-title">Priority Distribution</h3>
                </div>
                <div class="box-body chart-responsive">
                  <!-- <div class="chart" id="priority-chart" style="height: 300px; position: relative;"></div> -->
		  <?php
			$priorityData = printDonutPriority($conn, $releaseName, $targetRelease);
		        $strXML = "<graph caption='' decimalPrecision='0' showPercentageValues='0' showNames='1' showValues='1' showPercentageInLabel='1' pieYScale='60' pieBorderAlpha='40' pieFillAlpha='70' pieSliceDepth='15' pieRadius='130'>";
			foreach ($priorityData as $data) {
				$strXML .= "<set name='" . $data['priority']. "' value='" . $data['total']. "' />";
			}
		        $strXML .= "</graph>";
			echo renderChart("Chart/Charts/FCF_Pie3D.swf", "", $strXML, "Assignment", 500, 300);
		  ?>
                </div><!-- /.box-body -->
              </div><!-- /.box -->


              <!-- AREA CHART -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Top 10 Component</h3>
                </div>
                <div class="box-body chart-responsive">
                   <?php            
                        $componentData = printBarComponent($conn, $releaseName, $targetRelease);
                        $strXML = "<graph caption=''  xAxisName='Component' yAxisName='CRs' decimalPrecision='0' formatNumberScale='0'>";
                        foreach ($componentData as $data) {
                              $strXML .= "<set name='" . $data['component']. "' value='" . $data['total']. "' />";
                        }
                        $strXML .= "</graph>";
                        echo renderChart("Chart/Charts/FCF_Column3D.swf", "", $strXML, "Component", 700, 300);
                  ?>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

            </div><!-- /.col (LEFT) -->
            <div class="col-md-6">
              <!-- LINE CHART -->
              <div class="box box-info">
                <div class="box-header">
                  <h3 class="box-title">CR Find Vs Fix</h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php 
			$graph_open = "<graph caption=''  subcaption='' hovercapbg='FFECAA' hovercapborder='F47E00' formatNumberScale='0' decimalPrecision='0' showvalues='1' numdivlines='4' numVdivlines='0' yaxisminvalue='0' yaxismaxvalue='$yaxismaxvalue_overall' rotateNames='0' showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5'> \n";
			$category_open = "<categories >\n";
			$category_close = "</categories>\n";
			$data_set_close= "</dataset>\n";
			$graph_close= "</graph>\n";
 			$dataSet_HDR[Find] = "<dataset seriesName='Find' color='B22222' anchorBorderColor='B22222' anchorBgColor='B22222'>\n";	
 			$dataSet_HDR[Fix] = "<dataset seriesName='Fix' color='006400' anchorBorderColor='006400' anchorBgColor='006400'>\n";

			$myFile = "Data/IncomingCR-$today.xml";
			unlink($myFile);
			$fh = fopen($myFile, 'w') or die("Can't open file");

			$incomingCR = printLineIncomingCR($conn, $releaseName, $targetRelease);	
			$fixedCR    = printLineFixedCR($conn, $releaseName, $targetRelease);	
			foreach ($fixedCR as $fixdata) {
			    $fdata[$fixdata[month]] = $fixdata[total];		
			}

                        foreach ($incomingCR as $data) {
				$month = $data[month];
                                $x_axis_month = $x_axis_month."<category name='$data[month]' />\n";
			        $dataFind = $dataFind."<set value='$data[total]' />\n";	
				if($fdata[$month]=="") {
			  	  $dataFix = $dataFix."<set value='0' />\n";
				} else {
			  	  $dataFix = $dataFix."<set value='$fdata[$month]' />\n";
				}
                        }

			#Writing data to file
			  fwrite($fh, $graph_open);
			  fwrite($fh, $category_open);
			  fwrite($fh, $x_axis_month);
			  fwrite($fh, $category_close);
			  fwrite($fh, $dataSet_HDR[Find]);
			  fwrite($fh, $dataFind);
			  fwrite($fh, $data_set_close);
                          fwrite($fh, $dataSet_HDR[Fix]);
                          fwrite($fh, $dataFix);
                          fwrite($fh, $data_set_close);
			  fwrite($fh, $graph_close);
			  fclose($fh);

			echo renderChartHTML("Chart/Charts/FCF_MSLine.swf", "Data/IncomingCR-$today.xml", "", "myFirst", 700, 300, false);

                  ?>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

              <!-- BAR CHART -->
              <div class="box box-success">
                <div class="box-header">
                  <h3 class="box-title">Priority Vs Severity</h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php
			$graph_open = "<graph xAxisName='Priority' yAxisName='Severity' caption='' decimalPrecision='0' rotateNames='0' numDivLines='5' numberPrefix='' showValues='0' showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5' canvasBorderThickness='0' showColumnShadow='0'> \n";
			$category_open = "<categories >\n";
			$category_close = "</categories>\n";
			$data_set_close= "</dataset>\n";
			$graph_close= "</graph>\n";
			$dataSet_HDR[Crash] = "<dataset seriesName='Crash' color='DD4B39' showValues='1'>\n";
			$dataSet_HDR[Major] = "<dataset seriesName='Major' color='F39C12' showValues='1'>\n";
			$dataSet_HDR[Minor] = "<dataset seriesName='Minor' color='00C0EF' showValues='1'>\n";
			$dataSet_HDR[Trivial] = "<dataset seriesName='Trivial' color='00A65A' showValues='0'>\n";
			$dataSet_HDR[NewFeature] = "<dataset seriesName='NewFeature' color='0000FF' showValues='0'>\n";


                        $myFile = "Data/PrioritySeverity-$today.xml";
                        unlink($myFile);
                        $fh = fopen($myFile, 'w') or die("Can't open file");

                        $priSev = printSeverityPriority($conn, $releaseName, $targetRelease);
                        foreach ($priSev as $data) {
                                $x_priority = $x_priority ."<category name='$data[priority]' />\n";
				$dataSet_HDR[Crash] = $dataSet_HDR[Crash]."<set value='$data[Crash]' />\n";
				$dataSet_HDR[Major] = $dataSet_HDR[Major]."<set value='$data[Major]' />\n";
				$dataSet_HDR[Minor] = $dataSet_HDR[Minor]."<set value='$data[Minor]' />\n";
				$dataSet_HDR[Trivial] = $dataSet_HDR[Trivial]."<set value='$data[Trivial]' />\n";
				$dataSet_HDR[NewFeature] = $dataSet_HDR[NewFeature]."<set value='$data[NewFeature]' />\n";
                        }

			#Writing data to file
			  fwrite($fh, $graph_open);
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
			  fwrite($fh, $graph_close);
			  fclose($fh);

                        echo renderChart("Chart/Charts/FCF_StackedColumn2D.swf", "Data/PrioritySeverity-$today.xml", "", "Overall", 700, 300);

                  ?>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

            </div><!-- /.col (RIGHT) -->
          </div><!-- /.row -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->


<?php echo $scriptInclude; ?>
    <script type="text/javascript">
      $(function () {
        "use strict";

        // AREA CHART
        var area = new Morris.Area({
          element: 'revenue-chart',
          resize: true,
          data: [
            {y: '2011 Q1', item1: 2666, item2: 2666},
            {y: '2011 Q2', item1: 2778, item2: 2294},
            {y: '2011 Q3', item1: 4912, item2: 1969},
            {y: '2011 Q4', item1: 3767, item2: 3597},
            {y: '2012 Q1', item1: 6810, item2: 1914},
            {y: '2012 Q2', item1: 5670, item2: 4293},
            {y: '2012 Q3', item1: 4820, item2: 3795},
            {y: '2012 Q4', item1: 15073, item2: 5967},
            {y: '2013 Q1', item1: 10687, item2: 4460},
            {y: '2013 Q2', item1: 8432, item2: 5713}
          ],
          xkey: 'y',
          ykeys: ['item1', 'item2'],
          labels: ['Item 1', 'Item 2'],
          lineColors: ['#a0d0e0', '#3c8dbc'],
          hideHover: 'auto'
        });

        // LINE CHART
        var line = new Morris.Line({
          element: 'line-chart',
          resize: true,
          data: [
          <?php
                $chartData = printLineIncomingCR($conn,$releaseName,$targetRelease);
                echo $chartData;

          ?>
          ],
          xkey: 'y',
          ykeys: ['Open'],
          labels: ['Open'],
          lineColors: ['#3c8dbc'],
          hideHover: 'auto'
        });

        //DONUT CHART
        var donut = new Morris.Donut({
          element: 'priority-chart',
          resize: true,
          colors: [ "#F31B1B", "#f56954", "#3c8dbc", "#00a65a", "#B969CB", "#A4ADA6"],
          data: [
          <?php 
		$chartData = printDonutPriority($conn,$releaseName,$targetRelease); 
		echo $chartData;
	
	  ?>
          ],
          hideHover: 'true',
	  smooth:true
        });

        //BAR CHART
        var bar = new Morris.Bar({
          element: 'bar-chart',
          resize: false,
          data: [
          <?php
                $chartData = printBarComponent($conn,$releaseName,$targetRelease);
                echo $chartData;

          ?>
          ],
          barColors: ['#f56954'],
          xkey: 'y',
          ykeys: ['CR'],
          labels: ['CR'],
          hideHover: 'false'
        });
      });
    </script>

<?php

echo "</body>";
echo "</html>";
}

#########################################################################################
#                                                                                       #
# Default EXOS Set to EXOS 22.4.1, otherwise clicked different link                     #
#                                                                                       #
#########################################################################################
if (!$_GET[trackRelease]) {
      main("EXOS 22.4.1");
} else {
      main($_GET[trackRelease]); 
}
?>

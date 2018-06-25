<!DOCTYPE html>
<?php
#########################################################################################
#                                                                                       #
#  Procedure to print the Overall HTML Page                                             #
#                                                                                       #
#########################################################################################
function main($releaseName) {
include 'procs.php';
include 'template1.php';
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
            #printRDI($conn,$releaseName,$targetRelease);
            #printTotalCRs($conn,$releaseName,$targetRelease);
            #printOpenCRs($conn,$releaseName,$targetRelease);
            #printClosedCRs($conn,$releaseName,$targetRelease);
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
	        <?php
		$strXML="";
                $myFile = "Data/Priority.xml";
                unlink($myFile);
                $fh = fopen($myFile, 'w') or die("Can't open file");
                $chart_open = "<chart caption='' subcaption='' palettecolors='#F31B1B,#EBD05B,#5298C6,#00a65a,#B969CB,#A4ADA6' bgcolor='#ffffff' showborder='0' use3dlighting='0' showshadow='0' enablesmartlabels='0' startingangle='0' showpercentvalues='1' showpercentintooltip='0' decimals='1' captionfontsize='14' subcaptionfontsize='14' subcaptionfontbold='1' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='1' legendbgcolor='#ffffff' legendborderalpha='0' legendshadow='0' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>\n";
                $chart_close =  "</chart>\n";

                $priorityData = printDonutPriority($conn, $releaseName, $targetRelease);
                       foreach ($priorityData as $data) {
                               $strXML .= "<set label='" . $data['priority']. "' value='" . $data['total']. "' />";
                       }

                         #Writing data to file
                          fwrite($fh, $chart_open);
                          fwrite($fh, $strXML);
                          fwrite($fh, $chart_close);
                          fclose($fh);

                   ?>

                <script type="text/javascript">
                FusionCharts.ready(function () {
                    var myChart = new FusionCharts({
                              "type": "pie3d",
                              "renderAt": "priorityChart",
                              "width": "700",
                              "height": "300",
                              "dataFormat": "xmlurl",
                              "dataSource": "Data/Priority.xml"
                    });

                  myChart.render();
                });
                </script>
                <div class="chart" id="priorityChart" style="height: 300px; position: relative;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->


              <!-- AREA CHART -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Top 10 Component</h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php
                        $chart_open = "<chart caption='' subcaption='' xaxisname='' yaxisname='#CRs' numberprefix='' palettecolors='#008ee4' bgalpha='0' borderalpha='20' canvasborderalpha='0' useplotgradientcolor='0' plotborderalpha='10' placevaluesinside='1' rotatevalues='1' valuefontcolor='#ffffff' captionpadding='20' showaxislines='1' axislinealpha='25' divlinealpha='10'>\n";
                        $chart_close= "</chart>\n";

                        $myFile = "Data/topComponent.xml";
                        unlink($myFile);
                        $fh = fopen($myFile, 'w') or die("Can't open file");
			$strXML="";

                        $componentData = printBarComponent($conn, $releaseName, $targetRelease);
                        foreach ($componentData as $data) {
                              $strXML .= "<set label='" . $data['component']. "' value='" . $data['total']. "' />\n";
                        }

                        #Writing data to file
                          fwrite($fh, $chart_open);
                          fwrite($fh, $strXML);
                          fwrite($fh, $chart_close);
                          fclose($fh);

                  ?>

                <script type="text/javascript">
                FusionCharts.ready(function () {
                    var topComponent = new FusionCharts({
                              "type": "column2d",
                              "renderAt": "topComponent",
                              "width": "700",
                              "height": "300",
                              "dataFormat": "xmlurl",
                              "dataSource": "Data/topComponent.xml"
                    });

                  topComponent.render();
                });
                </script>
                <div class="chart" id="topComponent" style="height: 300px; position: relative;"> </div>
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
			$chart_open = "<chart caption='' subcaption='' captionfontsize='14' subcaptionfontsize='14' basefontcolor='#333333' yAxisMinValue='0' basefont='Helvetica Neue,Arial' subcaptionfontbold='0' xaxisname='' yaxisname='#CR' showvalues='1' palettecolors='#0075c2,#1aaf5d' bgcolor='#ffffff' showborder='0' showshadow='0' showalternatehgridcolor='0' showcanvasborder='0' showxaxisline='1' xaxislinethickness='1' xaxislinecolor='#999999' canvasbgcolor='#ffffff' legendborderalpha='0' legendshadow='0' divlinealpha='50' divlinecolor='#999999' divlinethickness='1' divlinedashed='1' divlinedashlen='1'>\n";
			$category_open = "<categories >\n";
			$category_close = "</categories>\n";
			$data_set_close= "</dataset>\n";
			$chart_close= "</chart>\n";
 			$dataSet_HDR[Find] = "<dataset seriesName='Find' color='B22222' anchorBorderColor='B22222' anchorBgColor='B22222'>\n";	
 			$dataSet_HDR[Fix] = "<dataset seriesName='Fix' color='006400' anchorBorderColor='006400' anchorBgColor='006400'>\n";

			$myFile = "Data/findVsfix.xml";
			unlink($myFile);
			$fh = fopen($myFile, 'w') or die("Can't open file");

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

                  ?>

                <script type="text/javascript">
                FusionCharts.ready(function () {
                    var findVsfix = new FusionCharts({
                              "type": "msspline",
                              "renderAt": "findVsfix",
                              "width": "700",
                              "height": "300",
                              "dataFormat": "xmlurl",
                              "dataSource": "Data/findVsfix.xml"
                    });

                  findVsfix.render();
                });
                </script>
		<div class="chart" id="findVsfix" style="height: 300px; position: relative;"> </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

              <!-- BAR CHART -->
              <div class="box box-success">
                <div class="box-header">
                  <h3 class="box-title">Priority Vs Severity</h3>
                </div>
                <div class="box-body chart-responsive">
                  <?php
			$chart_open = "<chart caption='' showvalues='0' plotgradientcolor='' formatnumberscale='0' showplotborder='0' palettecolors='#DD4B39,#F39C12,#00C0EF,#00A65A,#0000FF,#DD9D82' canvaspadding='0' bgcolor='FFFFFF' showalternatehgridcolor='0' divlinecolor='CCCCCC' showcanvasborder='0' legendborderalpha='0' legendshadow='0' interactivelegend='0' showpercentvalues='1' showsum='1' canvasborderalpha='0' showborder='0'>\n";
			$category_open = "<categories >\n";
			$category_close = "</categories>\n";
			$data_set_close= "</dataset>\n";
			$chart_close= "</chart>\n";
			$dataSet_HDR[Crash] = "<dataset seriesName='Crash' renderas='Area'>\n";
			$dataSet_HDR[Major] = "<dataset seriesName='Major' renderas='Area'>\n";
			$dataSet_HDR[Minor] = "<dataset seriesName='Minor' renderas='Area'>\n";
			$dataSet_HDR[Trivial] = "<dataset seriesName='Trivial' renderas='Area'>\n";
			$dataSet_HDR[NewFeature] = "<dataset seriesName='NewFeature' renderas='Area'>\n";


                        $myFile = "Data/PrioritySeverity.xml";
                        unlink($myFile);
                        $fh = fopen($myFile, 'w') or die("Can't open file");

                        $priSev = printSeverityPriority($conn, $releaseName, $targetRelease);
                        foreach ($priSev as $data) {
                                $x_priority = $x_priority ."<category label='$data[priority]' stepskipped='false' appliedsmartlabel='true' labeltooltext='' />\n";
				$dataSet_HDR[Crash] = $dataSet_HDR[Crash]."<set value='$data[Crash]' />\n";
				$dataSet_HDR[Major] = $dataSet_HDR[Major]."<set value='$data[Major]' />\n";
				$dataSet_HDR[Minor] = $dataSet_HDR[Minor]."<set value='$data[Minor]' />\n";
				$dataSet_HDR[Trivial] = $dataSet_HDR[Trivial]."<set value='$data[Trivial]' />\n";
				$dataSet_HDR[NewFeature] = $dataSet_HDR[NewFeature]."<set value='$data[NewFeature]' />\n";
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
                  ?>
                <script type="text/javascript">
                FusionCharts.ready(function () {
                    var prioritySev = new FusionCharts({
                              "type": "stackedcolumn2d",
                              "renderAt": "prioritySev",
                              "width": "700",
                              "height": "300",
                              "dataFormat": "xmlurl",
                              "dataSource": "Data/PrioritySeverity.xml"
                    });

                  prioritySev.render();
                });
                </script>
                <div class="chart" id="prioritySev" style="height: 300px; position: relative;"> </div>
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

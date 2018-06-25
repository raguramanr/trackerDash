<?php
#########################################################################################
#                                                                                       #
#  Procedure to print the Overall HTML Page                                             #
#                                                                                       #
#########################################################################################
include 'procs.php';
include 'template.php';
echo "<html>";
echo $headInclude;
echo $paletteStyle;
echo $topBarwoSearch;
echo $leftMenu;
printHeader($releaseName, $targetRelease, $crList);

#########################################################################################
#                                                                                       #
#  Procedure to print the comparative graph starts here					#
#                                                                                       #
#########################################################################################
if(isset($_POST[compare])) {
include 'db_connect.php';

echo "<section class=\"content\">";
echo "<div class=\"row\">";

foreach ($_POST['releaseSelection'] as $releaseName) {
$targetRelease = getDetail($conn, "SELECT releaseId as value FROM releases WHERE productId=132 AND releaseName='$releaseName'");
$crList = getCRList($conn,$releaseName,$targetRelease);

echo "<div class=\"col-md-4\">";
 echo "<div class=\"box box-primary\">";
  echo "<div class=\"box-header\">";
   echo "<h3 class=\"box-title\">$_POST[chartType] - $releaseName</a></h3>";
  echo "</div>";
  echo "<div class=\"box-body chart-responsive\">";
	if ($_POST[chartType] == "priorityChart") {
		printPriorityDistribution($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "topmetaData") {
		printnewFeature($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "newvsLegacy") {
		printLegacyRegressionIssues($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "openCR") {
		printopenCRPriorityDistribution($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "topComponent") {
		printtopComponent($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "managerCRs") {
		printCRbyManager($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "rbcClassification") {
		printRBCClassification($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "findVsfix") {
		printfindVsfix($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease, $conn); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} elseif ($_POST[chartType] == "crStateDistribution") {
		printCRStateDistribution($crList, $releaseName, $targetRelease, $_POST[chartType].$targetRelease); 
  		echo "<div class=\"chart\" id=\"$_POST[chartType]$targetRelease\" style=\"height: 300px; position: relative;\"></div>";
	} else { 
		echo "No charts Selected";	
	}
  echo "</div>";
 echo "</div>";
echo "</div>";
}
echo "</div>"; #Column Ends
echo "</div>"; #Row Ends

echo $scriptInclude;
echo "</body>";
echo "</html>";
exit;
}
?>

<!-- ####################################################################################
#                                                                                       #
#  Main page that displays the selection boxes						#
#                                                                                       #
###################################################################################### -->
<!-- Main Page Content Starts here -->
 <section class="content">
    <div class="row">
       <!-- R1C2 --> 
       <!-- Printing Chart Names -->
            <form method="post" name="compare" action="<?php echo $PHP_SELF ?>">
            <div class="col-md-4"></div>
            <div class="col-md-4">
             <div class='color-palette-set'>
                    <div class='bg-blue color-palette'>Select Chart Type</div>
                  </div>
                    <div class="form-group">
                      <select name="chartType" class="form-control">
                        <option value="priorityChart">Priority Distribution</option>
                        <option value="topmetaData">New Features</option>
                        <option value="newvsLegacy">New/Legacy/Regression Issues</option>
                        <option value="openCR">Priority Distribution - P1/P2 CRs</option>
                        <option value="topComponent">Top 10 Component</option>
                        <option value="managerCRs">Total CRs/Manager</option>
                        <option value="rbcClassification">RBC Original Vs Internal</option>
                        <option value="findVsfix">CR Find Vs Fix</option>
                        <option value="crStateDistribution">All CRs - State Distribution</option>
                      </select>
                    </div>	
            </div><!-- /.col -->
          </div><!-- /.row -->
          <div class="row">

        <!-- --------------------------------------------------------------------------------------------------------------------------------------  -->

        <!-- R2C2 --> 
        <!-- Printing EXOS Release Names -->
            <div class="col-md-4"></div>
            <div class="col-md-4">
             <div class='color-palette-set'>
                    <div class='bg-blue color-palette'>Select EXOS Release</div>
                  </div>
 		  <div class="form-group">
                      <select name="releaseSelection[]" size=10 multiple class="form-control">
                        <option>EXOS 22.5.1</option>
                        <option>EXOS 22.4.1</option>
                        <option>EXOS 22.3.1</option>
                        <option>EXOS 22.2.1</option>
                        <option>EXOS 22.1.1</option>
                        <option>EXOS 21.1.4</option>
                        <option>EXOS 21.1.3</option>
                        <option>EXOS 21.1.2</option>
                        <option>EXOS 21.1.1</option>
                        <option>EXOS 16.2.4</option>
                        <option>EXOS 16.2.3</option>
                        <option>EXOS 16.2.2</option>
                        <option>EXOS 16.2.1</option>
                        <option>EXOS 16.1.5</option>
                        <option>EXOS 16.1.4</option>
                        <option>EXOS 16.1.3</option>
                        <option>EXOS 16.1.2</option>
                        <option>EXOS 16.1.1</option>
                      </select>
                    </div>	
                      <center><button type="submit" class="btn btn-primary" name="compare">Submit</button></center>
                    </form>
                  </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->

<?php 
echo $scriptInclude;
echo "</body>";
echo "</html>";
?>

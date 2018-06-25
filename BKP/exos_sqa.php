<!DOCTYPE html>
<?php
#########################################################################################
#                                                                                       #
#  Procedure to print the Overall HTML Page						#
#                                                                                       #
#########################################################################################
function main($releaseName) {
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
            printTotalCRs_Sqa($conn,$releaseName,$targetRelease);
            printOpenCRs_Sqa($conn,$releaseName,$targetRelease);
            printClosedCRs_Sqa($conn,$releaseName,$targetRelease);
          echo "</div><!-- /.row -->";


          #<!-- Main Graphs and Data coded here after the header cards -->
          #<!-- Printing the CR Distrubtion Table -->
          echo "<div class=\"row\">";
            #<!-- Left col -->";
            echo "<section class=\"col-lg-7 connectedSortable\">";
		#<!--    Right side Frame Starts. -->";
              echo "<div class=\"box box-primary\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">CR Distribution</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
   		  printCRDistributionTable($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";

          #<!-- Printing the Priority Vs Severity Table -->
              echo "<div class=\"box box-info\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Priority Vs Severity</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
	        printSeverityPriorityTable($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";
            echo "</section><!-- /.Left col -->";

          #<!-- Printing the Top 5 Component Table -->
            #<!-- right col (We are only adding the ID to make the widgets sortable)-->
            echo "<section class=\"col-lg-5 connectedSortable\">";
		    #<!-- Left side Frame starts -->";
              echo "<div class=\"box box-danger\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Top 5 Component</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
   		  printTopComponent($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";

          #<!-- Printing the Top 5 Sub Component Table -->
              echo "<div class=\"box box-success\">";
                echo "<div class=\"box-header\">";
                  echo "<h3 class=\"box-title\">Top 5 Sub-Component</h3>";
                echo "</div><!-- /.box-header -->";
                echo "<div class=\"box-body no-padding\">";
   		  printTopSubComponent($conn,$releaseName,$targetRelease);
                echo "</div><!-- /.box-body -->";
              echo "</div><!-- /.box -->";

            echo "</section><!-- /.content -->";
      echo "</div><!-- /.content-wrapper -->";
    echo "</div><!-- ./wrapper -->";

echo $scriptInclude;
echo "</body>";
echo "</html>";
}

#########################################################################################
#                                                                                       #
# Default EXOS Set to EXOS 22.4.1, otherwise clicked different link			#
#                                                                                       #
#########################################################################################
if (!$_GET[trackRelease]) {
      main("EXOS 22.4.1");
} else {
      main($_GET[trackRelease]); 
}
?>

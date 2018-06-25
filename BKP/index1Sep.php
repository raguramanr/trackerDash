<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <link rel="icon" href="dist/img/icons/favicon-96x96.png" type="image/x-icon" />
    <title>CR Dashboard</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />    
    <link href="plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/ionicons-2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />    
    <link href="plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="skin-blue">

<?php
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
#  Getting the Total, Open and Closed CR count for EXOS 22.4.1
#                                                                                       #
#########################################################################################
include 'db_connect.php';
$countAssigned = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
                            LEFT JOIN releaseTracking USING(bugNumber)
                            LEFT JOIN users ON bugDescriptions.creator=users.username
                            WHERE globalState='Assigned' AND (releaseDetected='EXOS 22.4.1'  || targetReleaseId='3358')
                            AND (ldapManagerName like '%Velusamy%'      ||
                                 ldapManagerName like '%Ramkumar%'      ||
                                 ldapManagerName like '%Parthasarathy%' ||
                                 ldapManagerName like '%Palanivel%'     ||
                                 ldapManagerName like '%Raguraman%')");

$countTotal = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
			 LEFT JOIN releaseTracking USING(bugNumber)
			 LEFT JOIN users ON bugDescriptions.creator=users.username
			 WHERE (releaseDetected='EXOS 22.4.1'  || targetReleaseId='3358')
			 AND (ldapManagerName like '%Velusamy%'      ||
     			      ldapManagerName like '%Ramkumar%'      ||
                              ldapManagerName like '%Parthasarathy%' ||
                              ldapManagerName like '%Palanivel%'     ||
                              ldapManagerName like '%Raguraman%')");

$countClosed = getDetail($conn, "SELECT count(bugNumber) as value from bugDescriptions
			  LEFT JOIN releaseTracking USING(bugNumber)
                          LEFT JOIN users ON bugDescriptions.creator=users.username
	                  WHERE releaseState='Verified' 
			  AND (releaseDetected='EXOS 22.4.1'  || targetReleaseId='3358')
                          AND (ldapManagerName like '%Velusamy%'      ||
                               ldapManagerName like '%Ramkumar%'      ||
                               ldapManagerName like '%Parthasarathy%' ||
                               ldapManagerName like '%Palanivel%'     ||
                               ldapManagerName like '%Raguraman%')");

$totCurrentReleaseCRs = getDetail($conn, "select count(*) as value from bugDescriptions where releaseDetected='EXOS 22.4.1'");

$mgrName = array
  (
  array("Ramkumar",0,0,0,0),
  array("Raj",0,0,0,0),
  array("Shankar",0,0,0,0),
  array("Raguraman",0,0,0,0),
  array("Uma Parthasarathy",0,0,0,0),
  );

## Loop for populating Total CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value from bugDescriptions LEFT JOIN releaseTracking USING(bugNumber) LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (releaseDetected='EXOS 22.4.1'  || targetReleaseId='3358') AND ldapManagerName like '%$ldapName%' GROUP BY ldapManagerName";
   $mgrName[$row][1] = getDetail($conn, $sql);
  }
}


## Loop for populating Open CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value from bugDescriptions LEFT JOIN releaseTracking USING(bugNumber) LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (globalState='Assigned' || releaseState='Assigned') AND (releaseDetected='EXOS 22.4.1'  || targetReleaseId='3358') AND ldapManagerName like '%$ldapName%' GROUP BY ldapManagerName";
   $mgrName[$row][2] = getDetail($conn, $sql);
  }
}

## Loop for populating Closed CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value from bugDescriptions LEFT JOIN releaseTracking USING(bugNumber) LEFT JOIN users ON bugDescriptions.creator=users.username WHERE (releaseState='Verified' || releaseState='Task Complete') AND (releaseDetected='EXOS 22.4.1'  || targetReleaseId='3358') AND ldapManagerName like '%$ldapName%' GROUP BY ldapManagerName";
   $mgrName[$row][3] = getDetail($conn, $sql);
  }
}

## Loop for populating Verify-Fix/Feedback Needed CRs
for ($row = 0; $row <= 4; $row++) {
  for ($col = 1; $col <= 1; $col++) {
   $ldapName = $mgrName[$row][0];
   $sql = "SELECT count(bugNumber) as value FROM      (SELECT bugNumber, targetReleaseId, transitionDate, assignedTo as relassignedTo, releaseState FROM bugShortHistory LEFT JOIN releaseTracking USING(bugNumber) WHERE action='submit' AND targetReleaseId='3358' AND releaseState='Verify Fix') AS tmpTbl1     LEFT JOIN bugDescriptions USING(bugNumber)       LEFT JOIN users as creatorManager ON bugDescriptions.creator=creatorManager.username LEFT JOIN users as verifierManager ON relassignedTo=verifierManager.username WHERE (releaseState='Verify Fix' || globalState='Feedback Needed') AND verifierManager.ldapManagerName like '%$ldapName%' GROUP BY verifierManager.managerName";
   $mgrName[$row][4] = getDetail($conn, $sql);
  }
}

## Calculating RDI for EXOS 22.1 CRs
$sql = "SELECT bugDescriptions.priority as priority, count(bugDescriptions.bugNumber) as priorityCount FROM bugDescriptions  LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber  LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId WHERE ( bugDescriptions.productFamily = 'xos' )    AND ( bugDescriptions.severity <> '5 - New Feature' ) AND ( bugDescriptions.releaseDetected = 'EXOS 22.4.1' )    AND ( bugDescriptions.globalState = 'Assigned' )    AND ( isNull(releases.releaseName) )  GROUP BY priority order by priority ASC";

$rdiValue = 0;
$result = $conn->query($sql);
  if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      if($row[priority] == "1 - Critical") {
        $p1Rdi = $row[priorityCount] * 20;
      } elseif ($row[priority] == "2 - Urgent") { 
        $p2Rdi = $row[priorityCount] * 15;
      } elseif ($row[priority] == "3 - Important") { 
        $p3Rdi = $row[priorityCount] * 5;
      } elseif ($row[priority] == "4 - Moderate") { 
        $p4Rdi = $row[priorityCount];
      } else {
      }
     }
   } else {
}

$rdiValue = $p1Rdi + $p2Rdi + $p3Rdi + $p4Rdi;

$sql = "SELECT bugDescriptions.priority as priority, count(bugDescriptions.bugNumber) as priorityCount FROM bugDescriptions LEFT JOIN releaseTracking ON releaseTracking.bugNumber=bugDescriptions.bugNumber LEFT JOIN releases ON releases.releaseId=releaseTracking.targetReleaseId WHERE ( bugDescriptions.productFamily = 'xos' ) AND ( bugDescriptions.globalState = 'Assigned' OR bugDescriptions.globalState = 'Assigned Review' OR bugDescriptions.globalState = 'Deferred' OR bugDescriptions.globalState = 'Feedback Needed' OR bugDescriptions.globalState = 'Idle' OR bugDescriptions.globalState = 'Study' ) AND ( bugDescriptions.severity <> '5 - New Feature' ) AND ( bugDescriptions.priority = '1 - Critical' OR bugDescriptions.priority = '2 - Urgent' OR bugDescriptions.priority = '3 - Important' OR bugDescriptions.priority = '4 - Moderate' ) AND ( bugDescriptions.releaseDetected <= 'EXOS 22.4.1' ) AND ( bugDescriptions.releaseDetected >= 'EXOS 22.2.1' ) AND ( bugDescriptions.releaseDetected <> 'EXOS 16.2.1' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-falcon' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-OSPFv3-upgrade' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.1-SDK646' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.2' AND bugDescriptions.releaseDetected <> 'EXOS 16.2.2-GA-Jan17' AND bugDescriptions.releaseDetected <> 'EXOS 16.3.1' AND bugDescriptions.releaseDetected <> 'EXOS 16.3.1-linux-318' AND bugDescriptions.releaseDetected <> 'EXOS 17.9.10' ) AND NOT ( ( releases.releaseName LIKE '%22.4.1%' ) AND ( releaseTracking.releaseState = 'Build Pending' OR releaseTracking.releaseState = 'Committing' OR releaseTracking.releaseState = 'Released' OR releaseTracking.releaseState = 'Task Complete' OR releaseTracking.releaseState = 'Unverified Released' OR releaseTracking.releaseState = 'Verified' OR releaseTracking.releaseState = 'Verify Fix' OR releaseTracking.releaseState = 'Verify Task Complete' ) ) GROUP BY priority order by priority ASC";

$result = $conn->query($sql);
  if ($result->num_rows > 0) {
     while($row = $result->fetch_assoc()) {
      if($row[priority] == "1 - Critical") {
        $p1Rdi = $row[priorityCount] * 20;
      } elseif ($row[priority] == "2 - Urgent") {
        $p2Rdi = $row[priorityCount] * 15;
      } elseif ($row[priority] == "3 - Important") {
        $p3Rdi = $row[priorityCount] * 5;
      } elseif ($row[priority] == "4 - Moderate") {
        $p4Rdi = $row[priorityCount];
      } else {
      }
     }
   } else {
}

$rdiValue = $rdiValue + $p1Rdi + $p2Rdi + $p3Rdi + $p4Rdi;

## Calculating RDI for last three Releases


?>
    <div class="wrapper">
      <header class="main-header">
        <!-- Logo -->
        <a href="index.php" class="logo"><b>Tracker</b>Dash</a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </nav>
      </header>

      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="dist/img/extreme.png" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p>Tracker<p>
            </div>
          </div>
          <!-- search form -->
          <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="EXOS.."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form>
          <!-- /.search form -->
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
              <a href="#">
                <i class="fa fa-dashboard"></i> <span>Release</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="active"><a href="index.php"><i class="fa fa-circle-o"></i> EXOS 22.4</a></li>
                <!-- <li><a href="index.php"><i class="fa fa-circle-o"></i> EXOS 22.3</a></li> -->
              </ul>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Release: EXOS 22.4.1 
          </h1>
        </section>

        <!-- Top Boxes for CR overall count -->
        <section class="content">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h3><?php echo $rdiValue; ?></h3>
                  <p>RDI</p>
                </div>
                <div class="icon">
                  <i class="ion-ios-pulse"></i>
                </div>
                <a href="#" class="small-box-footer">Details <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                  <h3><?php echo $countTotal; ?></h3>
                  <p>Total CRs</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">Details <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-red">
                <div class="inner">
                  <h3><?php echo $countAssigned; ?></h3>
                  <p>Open CRs</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">Details <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-green">
                <div class="inner">
                  <h3><?php echo $countClosed; ?></h3>
                  <p>Closed</p>
                </div>
                <div class="icon">
                  <i class="ion ion-checkmark"></i>
                </div>
                <a href="#" class="small-box-footer">Details <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
          </div><!-- /.row -->

          <!-- Main Graphs and Data coded here after the header cards -->
          <div class="row">
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
		<!--    Right side Frame Starts. -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">CR Distribution</h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <table class="table table-striped">
                    <tr>
                      <th>Team</th>
                      <th>Total CRs</th>
                      <th>Open</th>
                      <th>Closed</th>
                      <th>Pending</th>
                      <th>Progress</th>
                      <th style="width: 40px">Percent</th>
                    </tr>

		     <?php
		      for ($row = 0; $row <= 4; $row++) {
		      echo "<tr>";
		            for ($col = 0; $col <= 4; $col++) {
			    echo "<td>".$mgrName[$row][$col]."</td>";
			    }
		      echo "<td><div class=\"progress progress-xs\">";
		      $progressPercent = ceil(($mgrName[$row][3]/$mgrName[$row][1]) * 100);	
		      echo "<div class=\"progress-bar progress-bar-success\" style=\"width: $progressPercent% \"></div>";
		      echo "</div> </td>";
		      echo "<td><span class=\"badge bg-red\">$progressPercent%</span></td>";
		      echo "</tr>";
		      }
  		      ?>

                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->


              <div class="box box-info">
                <div class="box-header">
                  <h3 class="box-title">Priority Vs Severity</h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <table class="table table-striped">
                    <tr>
                      <th>Priority</th>
                      <th>1-Crash</th>
                      <th>2-Major</th>
                      <th>3-Minor</th>
                      <th>4-Trivial</th>
                      <th>5-New Feature</th>
                      <th>Total</th>
                    </tr>

                     <?php
                      $sql = "select priority,  coalesce(sum(severity like '1 - Crash'),0)  Crash,  coalesce(sum(severity like '2 - Major'),0) Major,   coalesce(sum(severity like '3 - Minor'),0)  Minor,  coalesce(sum(severity like '4 - Trivial'),0)  Trivial,  coalesce(sum(severity like '5 - New Feature'),0)  NewFeature, count(priority) as totalCount from bugDescriptions   where releaseDetected='EXOS 22.4.1'  group by priority";
                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>".$row[priority]."</td>";
                          echo "<td>".$row[Crash]."</td>";
                          echo "<td>".$row[Major]."</td>";
                          echo "<td>".$row[Minor]."</td>";
                          echo "<td>".$row[Trivial]."</td>";
                          echo "<td>".$row[NewFeature]."</td>";
                          echo "<td>".$row[totalCount]."</td>";
                          echo "</tr>";
                         }
                        } else {
                          echo "0 results";
                        }
                      ?>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

            </section><!-- /.Left col -->

            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <section class="col-lg-5 connectedSortable">
		    <!-- Left side Frame starts -->
              <div class="box box-danger">
                <div class="box-header">
                  <h3 class="box-title">Top 5 Component</h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <table class="table table-striped">
                    <tr>
                      <th>Component</th>
                      <th>Total CRs</th>
                      <th style="width: 40px">Percent</th>
                    </tr>

                     <?php
		      $sql = "select component, count(component) as count  from bugDescriptions  where releaseDetected='EXOS 22.4.1' group by component order by count(component) desc limit 5";
		      $result = $conn->query($sql);

		      if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>".$row[component]."</td>";
                          echo "<td>".$row[count]."</td>";
			  $progressPercent = ceil(($row[count]/$totCurrentReleaseCRs) * 100);  	
                          echo "<td><span class=\"badge bg-red\">$progressPercent%</span></td>";
                          echo "</tr>";
			 }
			} else {
		          echo "0 results";
   			}
                      ?>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

              <div class="box box-success">
                <div class="box-header">
                  <h3 class="box-title">Top 5 Sub-Component</h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <table class="table table-striped">
                    <tr>
                      <th>Component</th>
                      <th>Total CRs</th>
                      <th style="width: 40px">Percent</th>
                    </tr>

                     <?php
                      $sql = "select subcomponent, count(subcomponent) as count  from bugDescriptions  where releaseDetected='EXOS 22.4.1' group by subcomponent order by count(subcomponent) desc limit 5";
                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>".$row[subcomponent]."</td>";
                          echo "<td>".$row[count]."</td>";
			  $progressPercent = ceil(($row[count]/$totCurrentReleaseCRs) * 100);  	
                          echo "<td><span class=\"badge bg-red\">$progressPercent%</span></td>";
                          echo "</tr>";
                         }
                        } else {
                          echo "0 results";
                        }
                      ?>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

            </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->

    <!-- jQuery 2.1.3 -->
    <script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- DATA TABES SCRIPT -->
    <script src="plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="dist/js/app.min.js" type="text/javascript"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
      $(function () {
        $("#example1").dataTable();
        $('#example2').dataTable({
          "bPaginate": true,
          "bLengthChange": false,
          "bFilter": false,
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": false
        });
      });
    </script>

  </body>
</html>

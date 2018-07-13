<?php
$headInclude = <<<HTML
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
    <link href="plugins/morris/morris.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="fusioncharts/js/fusioncharts.js"></script>
    <script type="text/javascript" src="fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
    <script>
        document.onkeydown=function(evt){
            var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
            if(keyCode == 13)
            {
                //your function call here
                document.test.submit();
            }
        }
    </script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <style>
	.hide {
    		display:none; 
	}
    </style>
  </head>
HTML;

$dataTableHeader = <<<HTML
        <link rel="shortcut icon" type="image/png" href="https://datatables.net/media/images/favicon.png">
        <link rel="stylesheet" type="text/css" href="./plugins/DataTable/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/DataTable/buttons.dataTables.min.css">

        <style type="text/css" class="init"> </style>
	<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<script src='plugins/fastclick/fastclick.min.js'></script>
	<script src="dist/js/app.min.js" type="text/javascript"></script>

        <script type="text/javascript" async="" src="./plugins/DataTable/ga.js.download"></script>
	<script type="text/javascript" src="./plugins/DataTable/site.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/jquery-1.12.4.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/jquery.dataTables.min.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/dataTables.buttons.min.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/jszip.min.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/pdfmake.min.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/vfs_fonts.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/buttons.html5.min.js.download"> </script>
        <script type="text/javascript" language="javascript" src="./plugins/DataTable/demo.js.download"> 
        </script>
</head>
HTML;

$topBar = <<<HTML
  <body class="skin-purple layout-top-nav">
    <div class="wrapper">
      
      <header class="main-header">               
        <nav class="navbar navbar-static-top">
          <div class="container-fluid">
          <div class="navbar-header">
            <a href="index.php" class="navbar-brand"><b>Tracker</b>Dash</a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
              <i class="fa fa-bars"></i>
            </button>
          </div>

          <div class="collapse navbar-collapse" id="navbar-collapse">
            <form method="post" name="test" action=$PHP_SELF? class="navbar-form navbar-left" role="search">
              <div class="form-group">
                <input type="text" name="releaseName" class="form-control" id="navbar-search-input" placeholder="EXOS RELEASE">
              </div>
            </form>

            <ul class="nav navbar-nav navbar-right">
              <li><a href="#"><font size=5>$releaseName</font></a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">More<span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="downloadExcel.php?getReport=RDI&releaseName=$releaseName&targetRelease=$targetRelease">Download RDI List</a></li>
                  <li><a href="downloadExcel.php?getReport=allCRs&releaseName=$releaseName&targetRelease=$targetRelease&metadataKeyMapId=$metadataKeyMapId">Download CR List</a></li>
                  <li><a href="comparator.php" target="_blank">Release Comparator</a></li>
                  <!-- <li><a href="detailedAnalysis.php?releaseName=$releaseName&targetRelease=$targetRelease" target="_blank">Custom Charts</a></li> -->
                  <li><a href="index.php?printCQG=yes" target="_blank">CQG Dashboard</a></li>
                  <li><a href="printCRAnalysis.php?releaseName=$releaseName&targetRelease=$targetRelease" target="_blank">CR Analysis</a></li>
                </ul>
              </li>
            </ul>
          </div>


          </div><!-- /.container-fluid -->
        </nav>
      </header>
HTML;

$topBarwoSearch = <<<HTML
  <body class="skin-purple layout-top-nav">
    <div class="wrapper">
      <header class="main-header">               
        <nav class="navbar navbar-static-top">
          <div class="container-fluid">
          <div class="navbar-header">
            <a href="index.php" class="navbar-brand"><b>Tracker</b>Dash</a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
              <i class="fa fa-bars"></i>
            </button>
          </div>
         </div><!-- /.container-fluid -->
        </nav>
      </header>
HTML;

$topBarCRAnalysis = <<<HTML
  <body class="skin-purple layout-top-nav">
    <div class="wrapper">
      <header class="main-header">
        <nav class="navbar navbar-static-top">
          <div class="container-fluid">
          <div class="navbar-header">
            <a href="index.php" class="navbar-brand"><b>Tracker</b>Dash</a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
              <i class="fa fa-bars"></i>
            </button>
          </div>
	 <ul class="nav navbar-nav navbar-right"><li><a href="#"><font size=5>EXOS 22.5.1</font></a></li>
         </div><!-- /.container-fluid -->
        </nav>
      </header>
HTML;

$scriptInclude = <<<HTML
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
    <!-- AdminLTE for demo purposes. Control the Java Widget for Setting the Layout Boxed/Collapsed etc 
    <script src="dist/js/demo.js" type="text/javascript"></script> -->
    <!-- Sparkline -->
    <script src="plugins/sparkline/jquery-1.8.3.js" type="text/javascript"></script>
    <script src="plugins/sparkline/jquery.sparkline.js" type="text/javascript"></script>
HTML;


$paletteStyle = <<<HTML
    <style>
      .color-palette {
        height: 50px;
        line-height: 50px;
        text-align: center;

      }
      .color-palette span {
        display: none;
      }
      .color-palette:hover span {
        display: block;
      }
    </style>
HTML;
?>

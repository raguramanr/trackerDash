<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="bootstrap/css/bootstrap..css" rel="stylesheet" type="text/css" />
<link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
<title>TrackerDash - Detailed Analysis</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body class="skin-purple fixed">
        <section class="header">
          <div class="callout callout-info">
            <h4>CR Data - Pivot Analyzer<h4>
            <p>Drag and Drop respective columns from the Field Chooser to get relevant Pivot Charts</p>
          </div>

		<div id="pivotContainer" style="padding-bottom: 30px;"></div>
		<script type="text/javascript" src="flexmonster/flexmonster.js"></script>

		<!-- 3. Create an instance of the component using new Flexmonster() -->
	     <script type="text/javascript">
			var pivot = new Flexmonster({
				container: "pivotContainer",
				height: "800px",
				toolbar: true,
				beforetoolbarcreated: customizeToolbar,
				   report: {
      					dataSource: {
         				/* OR URL to the CSV file */  
         				filename: "allCRList<?php echo $_GET[targetRelease]; ?>.csv",
      					}
   				},
				licenseKey: "Z76M-XAJ268-1Z1D61-401L4B"
			});

		function customizeToolbar(toolbar) {
		    // get all tabs
		    var tabs = toolbar.getTabs();
		    toolbar.getTabs = function () {
			        delete tabs[0];
       				delete tabs[1];
       				delete tabs[2];
		       		return tabs;
		    }
		}
		</script>


		<br/>
	</div>
</body>
</html>

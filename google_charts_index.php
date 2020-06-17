<?php
$date = new DateTime("now", new DateTimeZone('Asia/Jerusalem') );
$cur_dt =  $date->format('d-m-Y H:i:s');  
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dima - Data Digest</title>
  <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' />
  <link rel="stylesheet" type="text/css" href="assets/lib/bootstrap/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/keen-dashboards.css" />
  
	<!--Load the AJAX API-->
	
	  <script type="text/javascript" src="assets/lib/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="assets/lib/bootstrap/dist/js/bootstrap.min.js"></script>
	
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">

	  // Load the Visualization API and the corechart package.
	  google.charts.load('current', {'packages':['corechart']});

	  

// Set a callback to run when the Google Visualization API is loaded.
	  google.charts.setOnLoadCallback(drawChart);		
		
		
	  // Callback that creates and populates a data table,
 
	  function drawChart() {

		// Create the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Topping');
		data.addColumn('number', 'Slices');
		data.addRows([
		  ['Mushrooms', 3],
		  ['Onions', 1],
		  ['Olives', 1],
		  ['Zucchini', 1],
		  ['Pepperoni', 2]
		]);				

		// Set chart options
		var options = {'title':'How Much Pizza I Ate Last Night',
					   'width':400,
					   'height':300};

		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
		chart.draw(data, options);
		
		var chart2 = new google.visualization.BarChart(document.getElementById('chart_div2'));
		chart2.draw(data, options);
		
        var data2 = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);	

		var options2 = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' }
        };		
		
		var chart2 = new google.visualization.LineChart(document.getElementById('curve_chart3'));

        chart2.draw(data2, options2);	
		
		var data3 = google.visualization.arrayToDataTable([
				 ['Month', 'Bolivia', 'Ecuador', 'Madagascar', 'Papua New Guinea', 'Rwanda', 'Average'],
				 ['2004/05',  165,      938,         522,             998,           450,      614.6],
				 ['2005/06',  135,      1120,        599,             1268,          288,      682],
				 ['2006/07',  157,      1167,        587,             807,           397,      623],
				 ['2007/08',  139,      1110,        615,             968,           215,      609.4],
				 ['2008/09',  136,      691,         629,             1026,          366,      569.6]
			  ]);

		var options3 = {
		  title : 'Monthly Coffee Production by Country',
		  vAxis: {title: 'Cups'},
		  hAxis: {title: 'Month'},
		  seriesType: 'bars',
		  series: {5: {type: 'line'}}
		};
		  
		var chart3 = new google.visualization.ComboChart(document.getElementById('chart_div4'));			
		chart3.draw(data3, options3);		  		  
		

		var data4 = google.visualization.arrayToDataTable([
				  ['Year', 'Sales', 'Expenses'],
				  ['2013',  1000,      400],
				  ['2014',  1170,      460],
				  ['2015',  660,       1120],
				  ['2016',  1030,      540]
				]);
		
		var options4 = {
          title: 'Company Performance',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };
		
		var chart4 = new google.visualization.AreaChart(document.getElementById('chart_div5'));
		
		function myReadyHandler()
		{ 
		
			setTimeout(function(){
				window.status = 'ready'; 
			}, 5000);		
			
			//alert('ready!');
		} 			
				
		google.visualization.events.addListener(chart4, 'ready', myReadyHandler);
		
		chart4.draw(data4, options4);
			
	  }
	  


	</script>  
  
</head>
<body class="application">

  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Dima - Data Digest - <?php echo $cur_dt; ?></a>
      </div>
	  <!--
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-left">
          <li></li>
          <li></li>
          <li></li>
          <li></li>
        </ul>
      </div>
	  -->
    </div>
  </div>

  <div class="container-fluid">
  
  
    <div class="row">

      <div class="col-sm-6">
        <div class="chart-wrapper">
          <div class="chart-title">
            Pageviews by browser (past 24 hours)
          </div>
          <div class="chart-stage">
            <div id="chart_div"></div>
          </div>
          <div class="chart-notes">
            This is a sample text region to describe this chart.
          </div>
        </div>
      </div>

      <div class="col-sm-6">
        <div class="chart-wrapper">
          <div class="chart-title">
            Pageviews by browser (past 5 days)
          </div>
          <div class="chart-stage">
            <div id="chart_div2"></div>
          </div>
          <div class="chart-notes">
            Notes go down here
          </div>
        </div>
      </div>

    </div>

    <div class="row">

      <div class="col-sm-6">
        <div class="chart-wrapper">
          <div class="chart-title">
            Impressions by advertiser
          </div>
          <div class="chart-stage">
            <div id="curve_chart3"></div>
          </div>
          <div class="chart-notes">
            Notes go down here
          </div>
        </div>
      </div>

      <div class="col-sm-6">
        <div class="chart-wrapper">
          <div class="chart-title">
            Impressions by device
          </div>
          <div class="chart-stage">
            <div id="chart_div4"></div>
          </div>
          <div class="chart-notes">
            Notes go down here
          </div>
        </div>
      </div>

	</div>
	
	<div class="row">
	  
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Impressions by country
          </div>
          <div class="chart-stage">
            <div id="chart_div5"></div>
          </div>
          <div class="chart-notes">
            Notes go down here
          </div>
        </div>
      </div>

	</div>
     

    <hr>

    <p class="small text-muted">Built with &#9829; by Dmitry</p>

  </div>



  <script type="text/javascript" src="assets/lib/holderjs/holder.js"></script>
  <script>
    Holder.add_theme("white", { background:"#fff", foreground:"#a7a7a7", size:10 });
  </script>

 <!--<script type="text/javascript" src="assets/lib/keen-js/dist/keen.min.js"></script>-->
 <!-- <script type="text/javascript" src="assets/js/meta.js"></script>-->
 <!-- <script type="text/javascript" src="examples/starter-kit/keen.dashboard.js"></script>-->

</body>
</html>

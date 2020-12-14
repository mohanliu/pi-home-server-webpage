<?php header("refresh: 10000")?> 
<!DOCTYPE html>
<html>
      <head>
           <title>System Monitor</title>
           <meta charset="utf-8">
           <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

           <!-- Bootstrap CSS -->
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

           <style>
              body {
                background-color: #E4E6EB;
              }
           </style>
           <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
           <script type="text/javascript">
           google.charts.load('current', {'packages':['gauge']});
           google.charts.setOnLoadCallback(drawChart);
           function drawChart()
           {
                var data_temp_gauge = google.visualization.arrayToDataTable([
        		  ['Label', 'Value'],
			  ['Temperature', 
				<?php 
                                $f = fopen("/sys/class/thermal/thermal_zone0/temp","r");
                                $temp = fgets($f);
                                echo $temp/1000;
                                fclose($f);
                                ?>
		          ],
        		]);

        	var options_temp_gauge = {
			  width: 600, height: 400,
        		  redFrom: 70, redTo: 80,
        		  yellowFrom: 55, yellowTo: 80,
			  min: 20, max:80,
        		  minorTicks: 5
        		};

                var data_mem_gauge = google.visualization.arrayToDataTable([
        		  ['Label', 'Value'],
			  ['Memory', 
				<?php 
                                $f = fopen("/sys/class/thermal/thermal_zone0/temp","r");
                                $temp = fgets($f);
                                echo $temp/1000;
                                fclose($f);
                                ?>
		          ],
        		]);

        	var options_mem_gauge = {
			  width: 600, height: 400,
        		  redFrom: 70, redTo: 80,
        		  yellowFrom: 55, yellowTo: 80,
			  min: 20, max:80,
        		  minorTicks: 5
        		};

        	var chart_temp_gauge = new google.visualization.Gauge(document.getElementById('chart_div_0'));
        	var chart_mem_gauge = new google.visualization.Gauge(document.getElementById('chart_div_1'));

        	chart_temp_gauge.draw(data_temp_gauge, options_temp_gauge);
        	chart_mem_gauge.draw(data_mem_gauge, options_mem_gauge);
           }
           </script>
      </head>
      <body>
           <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
             <a class="navbar-brand" href="index.php">Home</a>
             <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
             </button>
             <div class="collapse navbar-collapse" id="navbarNav">
               <ul class="navbar-nav">
                 <li class="nav-item active">
                   <a class="nav-link" href="monitor.php">Live</a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link" href="monitor_history.php">History</a>
                 </li>
               </ul>
             </div>
           </nav>
           <div class="row">
           	<div class="col-md-4" style="width: 100%;">
                    <div id="chart_div_0" style="width: 100%;"></div>
           	</div>
           	<div class="col-md-4" style="width: 100%;">
                    <div id="chart_div_1" style=""></div>
           	</div>
           </div>

	   <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
           <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
           <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      </body>
 </html>  

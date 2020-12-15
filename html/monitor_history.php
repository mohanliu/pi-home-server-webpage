<?php
# Loading config data from *.ini-file
$ini = parse_ini_file ('/home/pi/cfg/db_config.ini');

# Assigning the ini-values to usable variables
$db_host = $ini['db_host'];
$db_name = $ini['db_name'];
$db_user = $ini['db_user'];
$db_password = $ini['db_password'];

# Prepare a connection to the mySQL database
$connection = new mysqli($db_host, $db_user, $db_password, $db_name);

# prepare a query to the mysql database 
$sql_temp = "SELECT *, TIMESTAMPDIFF(HOUR, '2020-01-01', created_at) as hour, AVG(temperature) as ave_temp, MIN(temperature) as min_temp, MAX(temperature) as max_temp FROM pitemp GROUP BY hour";
$sql_mem = "SELECT *, TIMESTAMPDIFF(HOUR, '2020-01-01', created_at) as hour, AVG(memory) as ave_mem, MIN(memory) as min_mem, MAX(memory) as max_mem FROM mem_perc GROUP BY hour";

$result_temp = $connection->query($sql_temp);
$result_mem = $connection->query($sql_mem);

?>
<!DOCTYPE html>
 <html>
      <head>
           <title>System Monitor</title>
           <meta charset="utf-8">
           <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

           <!-- Bootstrap CSS -->
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

           <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
           <script type="text/javascript">
           google.charts.load('current', {'packages':['corechart']});
           google.charts.setOnLoadCallback(drawChart);

           function drawChart()
           {
		var data_temp = new google.visualization.DataTable();
		data_temp.addColumn('string', 'x');
		data_temp.addColumn('number', 'Temperature');
		data_temp.addColumn({id:'min', type:'number', role:'interval'});
		data_temp.addColumn({id:'max', type:'number', role:'interval'});

		data_temp.addRows([
                          <?php
                          while($row = mysqli_fetch_array($result_temp))
                          {
                               echo "['".$row["created_at"]."', ".$row["ave_temp"].", ".$row["min_temp"].", ".$row["max_temp"]."],";
                          }
                          ?>
		]);

		var data_mem = new google.visualization.DataTable();
		data_mem.addColumn('string', 'x');
		data_mem.addColumn('number', 'Memory');
		data_mem.addColumn({id:'min', type:'number', role:'interval'});
		data_mem.addColumn({id:'max', type:'number', role:'interval'});

		data_mem.addRows([
                          <?php
                          while($row = mysqli_fetch_array($result_mem))
                          {
                               echo "['".$row["created_at"]."', ".$row["ave_mem"].", ".$row["min_mem"].", ".$row["max_mem"]."],";
                          }
                          ?>
		]);
		
		// combined data
		var data = [];
                data[0] = data_temp;
                data[1] = data_mem;

		// Curved line
		var options = {
			title: 'Temperature',
			curveType: 'function',
			lineWidth: 4,
            		intervals: { 'style': 'area' },
			legend: { position: 'none' },
                        animation:{
                                duration: 1000,
                                easing: 'out'
                              },
			};

		// variables
		var current = 0;

		var chart = new google.visualization.LineChart(document.getElementById('chart_div_1'));
		var button = document.getElementById('button_switch');

		function drawsingleChart() {
                  // Disabling the button while the chart is drawing.
                  button.disabled = true;
                  google.visualization.events.addListener(chart, 'ready',
                      function() {
                        button.disabled = false;
                  	if ( current == 0 ) {
		  	      button.innerHTML = "Temperature"
                  	} else if ( current == 1 ) { 
		  	      button.innerHTML = "Memory"
                  	}
                      });
                  
                  if ( current == 0 ) {
			options['title'] = "Temperature"
                  } else if ( current == 1 ) { 
			options['title'] = "Memory"
                  }

                  chart.draw(data[current], options);
                }
                drawsingleChart();

                button.onclick = function() {
                  current = 1 - current;
                  drawsingleChart();
                }

		
                //chart.draw(data[1], options);
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
                 <li class="nav-item">
                   <a class="nav-link" href="monitor.php">Live</a>
                 </li>
                 <li class="nav-item active">
                   <a class="nav-link" href="monitor_history.php">History</a>
                 </li>
               </ul>
             </div>
           </nav>
           <div class="row justify-content-md-center">
		<div class="col-md-2">
                <button id="button_switch" class="btn btn-outline-primary" style="width:100%;margin:20px;border-radius:40px;font-size:25px;padding:10px">Temperature</button>
		</div>
           </div>
           <div class="row" style="width: 100%; height: 80vh">
		<div class="col-md-12">
                <div id="chart_div_1" style="width: 100%; height: 100%;"></div>
		</div>
           </div>

	   <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
           <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
           <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      </body>
 </html>  

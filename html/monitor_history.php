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
$sql = "select * from pitemp";
$result = $connection->query($sql);

# last temperature 
$last_temp_sql = "select * from pitemp order by created_at desc limit 1";
$last_temp_result = $connection->query($last_temp_sql);
?>
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
           google.charts.load('current', {'packages':['corechart']});
           google.charts.setOnLoadCallback(drawChart);
           function drawChart()
           {
                var data = google.visualization.arrayToDataTable([
                          ['Time', 'Temperature'],
                          <?php
                          while($row = mysqli_fetch_array($result))
                          {
                               echo "['".$row["created_at"]."', ".$row["temperature"]."],";
                          }
                          ?>
                     ]);

		// Curved line
		var options = {
			title: 'Temperature',
			curveType: 'function',
			legend: { position: 'bottom' }
			};
		// Curved chart
		var chart = new google.visualization.LineChart(document.getElementById('chart_div_1'));
                chart.draw(data, options);
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
           <div style="width: 100%;">
                <div id="chart_div_1" style="width: 50%; height: 500px;"></div>
           </div>

	   <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
           <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
           <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      </body>
 </html>  

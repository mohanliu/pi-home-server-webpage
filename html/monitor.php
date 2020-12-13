<?php
# Loading config data from *.ini-file
$ini = parse_ini_file ('/home/pi/cfg/db_config.ini');

# Assigning the ini-values to usable variables
$db_host = $ini['db_host'];
$db_name = $ini['db_name'];
$db_table = $ini['db_table'];
$db_user = $ini['db_user'];
$db_password = $ini['db_password'];

# Prepare a connection to the mySQL database
$connection = new mysqli($db_host, $db_user, $db_password, $db_name);

# Prepare a query to the mySQL database and get a list of the last 10 readings.
$sql = "SELECT * FROM $db_table";
$result = $connection->query($sql);
?>
<!DOCTYPE html>
 <html>
      <head>
           <title>System Monitor</title>
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
		var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                chart.draw(data, options);
           }
           </script>
      </head>
      <body>
           <div style="width:900px;">
                <div id="curve_chart" style="width: 900px; height: 500px;"></div>
           </div>
      </body>
 </html>  

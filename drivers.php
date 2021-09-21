<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php
    session_start();
    include __DIR__."/lib/mysql.php";
    include __DIR__."/lib/const.php";
    include __DIR__."/lib/functions.php";

    
    if(!isset($_SESSION['id_utente']))
      header("Location: ./index.php");
    ?>
    <meta charset="utf-8">
    <title>Piloti</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- To prevent web caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="./css/custom.css" rel="stylesheet">
    <link href="./css/mycss/style.css" rel="stylesheet">
    <link href="./css/mycss/graph.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/home_script.js"></script>
    <script src="./js/graph_script.js"></script>

    <script>
      window.onload = function() {
        setInfoIcon();
        setTimeout(removeLoader, 2000);
      }
    </script>
  </head>
  <body class="body-pattern">
    <!-- Navbar -->
    <?php include __DIR__."/lib/navbar.php";?>
    <?php include __DIR__."/lib/loader.php";?>
    <div class="container content">
      <div class="container main-container rank-container">
        <div class="header">
          <h3>Classifica piloti</h3>
        </div>

        <div class="table-responsive">
        <!-- classifica piloti -->
          <table id="driver-chart" class="table table-sm align-middle table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Cognome</th>
                <th scope="col">Nome</th>
                <th scope="col">Punti</th>
                <!--<th scope="col" id="last_gp_header">
                  <script>document.getElementById("last_gp_header").innerHTML = getLastGpLocation();</script>
                </th>-->
                <th scope="col">Scuderia</th>
                <th scope="col">Int.</th>
                <th scope="col">Led.</th>
              </tr>
            </thead>
            <tbody class="tbody">

              <?php

                $drivers_res = getDrivers();
                
                $drivers = [];

                for($i = 0; $i < $drivers_res->num_rows; $i++) {
                  $row = $drivers_res->fetch_assoc();


                  $driver = array(
                    "id_pilota" => $row['id_pilota'],
                    "cognome_pilota" => $row['cognome_pilota'],
                    "nome_pilota" => $row['nome_pilota'],
                    "nome_scuderia" => $row['nome_scuderia']
                  );

                  array_push($drivers, $driver);
                }

                echo '<script>createDriversChart('.json_encode($drivers).');</script>'

              ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="container main-container graph-container">
        <div class="header pt-2 d-flex">
          <h3>Grafico</h3>
          <span id="info-icon-container"></span>
        </div>
        <div class="graph-content">
          <canvas id="driver-graph" class="graph-canvas" width="400" height="400"></canvas>
        <?php

          $drivers_res = getDrivers();
                          
          $drivers = [];

          for($i = 0; $i < $drivers_res->num_rows; $i++) {
            $row = $drivers_res->fetch_assoc();


            $driver = array(
              "id_pilota" => $row['id_pilota'],
              "cognome_pilota" => $row['cognome_pilota'],
              "nome_pilota" => $row['nome_pilota'],
              "nome_scuderia" => $row['nome_scuderia']
            );

            array_push($drivers, $driver);
          }

          echo '<script>createDriversGraph('.json_encode($drivers).');</script>'

        ?>
        
        </div>
      </div>
    </div>

    <button id="top-btn" name="top-page" title="Go to the top" value="top" onclick="topFunction()">^</button>
    <script>
      var mybutton = document.getElementById("top-btn");
      window.onscroll = function() {scrollFunction()};

      function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
          mybutton.style.display = "block";
        } else {
          mybutton.style.display = "none";
        }
      }
      function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
      }
    </script>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script type="text/javascript" src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

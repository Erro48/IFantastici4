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
    <title>Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- To prevent web caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="./css/custom.css" rel="stylesheet">
    <link href="./css/mycss/style.css" rel="stylesheet">
    <link href="./css/mycss/home.css" rel="stylesheet">
    


    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/home_script.js"></script>
    <script src="./js/graph_script.js"></script>
  </head>
  <body class="body-pattern">
    <!-- Navbar -->
    <?php include __DIR__."/lib/navbar.php";?>

    <div class="container">
      <div class="row">
        <!-- classifica -->
        <div class="col-12 col-xl-7 order-xl-1 order-2">
          <div class="container main-container rank-container">
            <div class="header">
              <h3>Classifica</h3>
            </div>

            <!-- Tab squadre -->
            <div class="table-responsive">
            <!-- classifica squadre -->
              <table id="team-chart" class="table table-sm align-middle table-striped">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Squadra</th>
                    <th scope="col">Proprietario</th>
                    <th scope="col">Punti</th>
                    <th scope="col" id="last_gp_header">
                      <script>document.getElementById("last_gp_header").innerHTML = getLastGpLocation();</script>
                    </th>
                    <th scope="col">Int.</th>
                    <th scope="col">Led.</th>
                  </tr>
                </thead>
                <tbody class="tbody">

                  <?php

                    // prendo le squadre con le relative info
                    $user_ids_res = getUsersId();
                    
                    $teams = [];

                    for($i = 0; $i < $user_ids_res->num_rows; $i++) {
                      $user_ids = $user_ids_res->fetch_assoc();

                      $team_res = getTeamInfoByUserId($user_ids['id_utente']);
                      $tmp = $team_res->fetch_assoc();

                      $team = array(
                        "id_squadra" => $tmp['id_squadra'],
                        "nome_squadra" => $tmp['nome_squadra'],
                        "nome_utente" => $tmp['nome_utente']
                      );

                      array_push($teams, $team);
                    }

                    echo '<script>createTeamChart('.json_encode($teams).');</script>'

                  ?>
                </tbody>
              </table>
            </div>

          </div>

          <div class="container main-container graph-container">
            <div class="header">
              <h3>Grafico</h3>
            </div>
            <div class="graph-content">
              <canvas id="team_chart" width="400" height="400"></canvas>
            <?php

              // prendo le squadre con le relative info
              $user_ids_res = getUsersId();

              $teams = [];

              for($i = 0; $i < $user_ids_res->num_rows; $i++) {
                $user_ids = $user_ids_res->fetch_assoc();

                $team_res = getTeamInfoByUserId($user_ids['id_utente']);
                $tmp = $team_res->fetch_assoc();

                $team = array(
                  "id_squadra" => $tmp['id_squadra'],
                  "nome_squadra" => $tmp['nome_squadra'],
                  "nome_utente" => $tmp['nome_utente']
                );

                array_push($teams, $team);
              }

              echo '<script>createTeamGraph('.json_encode($teams).');</script>'

            ?>
            
            </div>
          </div>
        </div>

        <!-- squadra -->
        <!-- appena riesco a ridimensionare le card col torna a md e non lg -->
        <div class="col-12 col-xl-5 order-1">
          <div class="container main-container team-container sticky-top" style="top: 76px; z-index: 1">
            <div class="header">
              <?php
              // seleziono il nome della squadra
              $result = getTeamNameByUserId($_SESSION['id_utente']);

              // prendere tutti i prezzi dei piloti con scuderia e ricavare budget rimasto (patenza -> 150)

              if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                echo "<h3 class='team-name' id='team-name' onclick='changeTeamName();'>".$row['nome_squadra']."</h3>";
                $_SESSION['id_squadra'] = $row['id_squadra'];
                echo "<script>setCookie('id_squadra', ".$_SESSION['id_squadra'].")</script>";
              }else{
                echo "Errore nome squadra";
              }
              ?>
            </div>

            <!-- gruppo carte piloti-->
            <div class="row row-cols-1 row-cols-md-3 g-4 text-dark cards-deck d-flex justify-content-center">

              <?php
              $drivers = getDriversByTeamId($_SESSION['id_squadra'], 1, 1);
              $turbo_mega_driver = getTurboAndMegaDriverByTeamId($_SESSION['id_squadra']);
              $count = 0;

              if($drivers->num_rows > 0){

                $row_td = $turbo_mega_driver->fetch_assoc();
                while($driver = $drivers->fetch_assoc()){

                  if($turbo_mega_driver->num_rows > 0){
                    if($count == 3){
                      echo '</div>
                            <div class="row row-cols-1 row-cols-md-3 g-4 text-dark cards-deck d-flex justify-content-center">';
                    }
                    printDriverCard($driver, $row_td);
                  }

                  $count++;
                }
              }
              ?>
            </div>

          <!-- carta scuderia -->
            <div class="row row-cols-1 row-cols-md-3 g-4 text-dark cards-deck d-flex justify-content-center" style="margin-top: 0px;">
              <?php
              $stable = getStableByTeamId($_SESSION['id_squadra']);

              if($stable->num_rows > 0){
                $row = $stable->fetch_assoc();
                printStableCard($row);
              }
              ?>
            </div>

            <div class="footer">
              <div class="row">
                <div class="col-10 order-1">
                  Totale
                </div>
                <div id="total-points" class="col-1 order-2"></div>
              </div>

              <div class="row">
                <div class="col-10 order-1">
                  Raceweek
                </div>
                <div id="raceweek-points" class="col-1 order-2"></div>
              </div>
            </div>
        </div>
      </div><!-- /squadra -->
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

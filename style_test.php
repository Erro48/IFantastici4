<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php
    include __DIR__."/lib/mysql.php";
    include __DIR__."/lib/functions.php";

    session_start();
    if(!isset($_SESSION['id_utente']))
      header("Location: ./index.php");
    ?>
    <meta charset="utf-8">
    <title>Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="./css/custom.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="./js/script.js"></script>
  </head>
  <body class="bg-secondary">
    <!-- Navbar -->
    <?php include __DIR__."/lib/navbar.php"; ?>


    <div class="container">
      <div class="row">
        <!-- classifica -->
        <div class="col-12 col-lg-6 order-lg-1 order-2">
          <div class="container main-container">
            <div class="header">
              <h3>Classifica</h3>
            </div>

            <div id="team-rank" class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome Squadra</th>
                    <th scope="col">Punti</th>
                    <th scope="col">Proprietario</th>
                    <th scope="col">Intervallo</th>
                    <th scope="col">Leader</th>
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

                    //echo '<script>createChart('.json_encode($teams).');</script>'

                  ?>
                </tbody>
              </table>
            </div>




  <!--          <div class="container  rank-container">
              <div id="header-charts-row" class="row charts-row">
                <div class="col-1 position">#</div>
                <div class="col-4 team-name">Nome Squadra</div>
                <div class="col-3 team-owner">Proprietario</div>
                <div class="col-2 team-score">Punti</div>
                <div class="col-1 leader-interval">Leader</div>
                <div class="col-1 interval">Int.</div>
              </div>

              <?php

              // prendo le 4 squadre

              // faccio la classifica

              // le stampo

              ?>

              <div id="charts-row-1" class="row charts-row">
                <div class="col-1 position">1</div>
                <div class="col-4 team-name">Cacca Rosa</div>
                <div class="col-3 team-owner">Pino</div>
                <div class="col-2 team-score">1000</div>
                <div class="col-1 leader-interval">-</div>
                <div class="col-1 interval">-</div>
              </div>

              <div id="charts-row-2" class="row charts-row">
                <div class="col-1 position">2</div>
                <div class="col-4 team-name">Figarotta</div>
                <div class="col-3 team-owner">Norberto</div>
                <div class="col-2 team-score">965</div>
                <div class="col-1 leader-interval">35</div>
                <div class="col-1 interval">35</div>
              </div>

              <div id="charts-row-3" class="row charts-row">
                <div class="col-1 position">3</div>
                <div class="col-4 team-name">Sterco Team</div>
                <div class="col-3 team-owner">Franco</div>
                <div class="col-2 team-score">950</div>
                <div class="col-1 leader-interval">50</div>
                <div class="col-1 interval">15</div>
              </div>
            </div> -->

          </div>
        </div>

        <!-- squadra -->
        <!-- appena riesco a ridimensionare le card col torna a md e non lg -->
        <div class="col-12 col-lg-6 order-1">
          <div class="container main-container team-container">
            <div class="header">
              <?php
              $sql = 'SELECT id_squadra, nome_squadra FROM tsquadre, tutenti WHERE id_squadra=(SELECT k_squadra FROM tutenti WHERE id_utente='.$_SESSION['id_utente'].')';
              $result = $db->query($sql);

              if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                echo "<h3 class='team-name' id='team-name' onclick='changeTeamName();'>".$row['nome_squadra']."</h3>";
                $_SESSION['id_squadra'] = $row['id_squadra'];
              }else{
                echo "Errore nome squadra";
              }
              ?>
            </div>


          <div class="col card-col">
          
          
          
          <div id="Verstappen-card" class="card text-white bg-dark xs-3">
            <div class="card-header" style="background-color: rgb(6, 0, 239); ">
              <div class="row">
                <div class="col-12 col-sm-9">
                  <div class="card-title">Verstappen</div>
                  <div class="card-subtitle">Max</div>
                </div>


                <div class="col-1 col-sm-3 d-none d-sm-block header-img" style="background-image: url(./images/drivers/verstappen.png);">
                </div>

                
              </div>
              
              
            </div>

            <div class="card-body">
              <div class="row">

                <div class="col-7 col-sm-12">
                  <p class="card-text">
                    <div class="row md-td-container">

                      <div class="col-xs-6 md-btn" onclick="setMegaDriver(this);">MD</div>
                      <div class="col-xs-6 td-btn turbo-driver" onclick="setTurboDriver(this);">TD</div>
                    </div>
                  </p>

                  <div class="row card-info">
                    <div id="Verstappen-price" class="col-12">Prezzo: 35.0$</div>
                    <div class="col-12 last-score">Punti: 15</div>

                  </div>
                </div>

                <div class="col-4 d-sm-none body-img" style="background-image: url(./images/drivers/verstappen.png);">
                </div>

                
              </div>
              
            </div>
          </div>





        </div>

            <!-- gruppo carte piloti-->
            <!--<div class="row row-cols-1 row-cols-md-3 g-4 text-dark cards-deck">
              <?php
             /* $sql = 'SELECT cognome_pilota, nome_pilota, T.id_pilota, prezzo_base FROM tpiloti T, rpossiede P WHERE T.id_pilota=P.id_pilota AND P.id_squadra='.$_SESSION['id_squadra'].' ORDER BY T.id_pilota';
              $result = $db->query($sql);

              $sql_td = 'SELECT turbo_driver, mega_driver, mega_driver_flag FROM tsquadre WHERE id_squadra='.$_SESSION['id_squadra'];
              $result_td = $db->query($sql_td);

              if($result->num_rows > 0){

                $row_td = $result_td->fetch_assoc();
                while($row = $result->fetch_assoc()){

                  if($result_td->num_rows > 0){

                  echo '<div class="col">';

                  echo '<div id="'.$row['cognome_pilota'].'-card" class="card text-white bg-dark xs-3">';
                    echo '<div class="card-header">
                          <div class="card-title">'.$row['cognome_pilota'].'</div>
                          <div class="card-subtitle">'.$row['nome_pilota'].'</div>
                        </div>

                        <div class="card-body">
                          <p class="card-text">
                            <div class="row md-td-container">';

                    // mega driver
                    if(!strcmp($row_td['mega_driver'], $row['cognome_pilota'])){
                      echo '<div class="col-xs-6 md-btn mega-driver" onclick="setMegaDriver(this);">MD</div>';
                    } else {
                      echo '<div class="col-xs-6 md-btn" onclick="setMegaDriver(this);">MD</div>';
                    }

                            // turbo driver
                  if(!strcmp($row_td['turbo_driver'], $row['cognome_pilota'])){
                    echo '<div class="col-xs-6 td-btn turbo-driver" onclick="setTurboDriver(this);">TD</div>';
                  } else {
                    echo '<div class="col-xs-6 td-btn" onclick="setTurboDriver(this);">TD</div>';
                  }

                    echo '</div>
                        </p>

                        <div class="row card-info">
                        <p id="'.$row['cognome_pilota'].'-price" class="card-text">Prezzo: '.$row['prezzo_base'].'$</p>
                        <p class="card-text last-score">Punti: -</p>

                        </div>
                      </div>
                    </div>
                  </div>';

                  }
                }
              }*/
              ?>
            </div> -->

          <!-- carta scuderia -->
            <!--div class="row row-cols-1 row-cols-md-3 g-4 text-dark cards-deck" style="margin-top: 0px;">
              <?php
              /*$sql = 'SELECT nome_breve, prezzo_base FROM tscuderie S, tsquadre T WHERE k_scuderia=id_scuderia AND id_squadra='.$_SESSION['id_squadra'];
              $result = $db->query($sql);

              if($result->num_rows > 0){
                $row = $result->fetch_assoc();

                      echo '<div class="col">
                         <div id="'.$row['nome_breve'].'-card" class="card text-white bg-dark sm-3" >
                           <div class="card-header">
                             <div class="card-title">'.$row['nome_breve'].'</div>
                           </div>

                           <div class="card-body">
                             <p id="stable-price" class="card-text">Prezzo: '.$row['prezzo_base'].'$</p>
                             <p id="stable-last-score" class="card-text">Punti: -</p>
                           </div>
                         </div>
                       </div>';
              }*/
              ?>
            </div> -->

            <div class="footer">
              <div class="row">
                <div class="col-10 order-1">
                  Totale
                </div>

                <div id="total-points" class="col-1 order-2">

                </div>
              </div>

              <div class="row">
                <div class="col-10 order-1">
                  Raceweek
                </div>

                <div id="raceweek-points" class="col-1 order-2">
                </div>
              </div>
            </div>
        </div>
      </div><!-- /squadra -->
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>

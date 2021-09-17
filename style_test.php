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

    <style>
    .game-period-image--background {
        /*position: absolute;*/
        top: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: -1;
        padding: 10px;
    }
    </style>


    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/home_script.js"></script>
    <script src="./js/graph_script.js"></script>
    <script src="./js/tracks_layout.js"></script>

    <script>
      window.onload = function() {
        createTrackSlot(getLastGpLocation());
      }

      function createTrackSlot(gp_location) {
        let score = scoreConverterToArray(getCookie('scores_data'), 31);
        let last_gp_date = getGpDate(gp_location);
        let last_gp_index = getGpIndex(gp_location, score);

        let track_slot = document.getElementById('track-slot');
        let header = createTrackHeader(score, gp_location);
        let body = createTrackBody(score, gp_location);
        let footer = createTrackFooter();
        

        track_slot.appendChild(header);
        track_slot.appendChild(body);
        track_slot.appendChild(footer);
      }

      function createTrackHeader(score, gp) {
        let header = document.createElement('ul');
        header.classList.add('track-slot-header');
        header.classList.add('nav');
        header.classList.add('nav-tabs');
        header.classList.add('accordion-header');
        header.setAttribute('id', 'track-slot-header');


        // ACCORDION
        let accordion_btn_container = document.createElement('li');
        accordion_btn_container.classList.add('nav-item');
        accordion_btn_container.classList.add('d-lg-none');

        let accordion_btn = document.createElement('button');
        accordion_btn.classList.add('accordion-button');
        accordion_btn.classList.add('collapsed');
        accordion_btn.setAttribute('type', 'button');
        accordion_btn.setAttribute('data-bs-toggle', 'collapse');
        accordion_btn.setAttribute('data-bs-target', '#track-slot-body');
        accordion_btn.setAttribute('aria-expanded', 'true');
        accordion_btn.setAttribute('aria-controls', 'track-slot-body');

        accordion_btn_container.appendChild(accordion_btn);





        let prevs_gp = document.createElement('li');
        let header_gps = [];
        let nexts_gp = document.createElement('li');

        prevs_gp.classList.add('header-elem');
        prevs_gp.classList.add('nav-item');
        prevs_gp.classList.add('nav-link');
        prevs_gp.id = 'prev-arrow';
        prevs_gp.innerHTML = '<<';

        nexts_gp.classList.add('header-elem');
        nexts_gp.classList.add('nav-item');
        nexts_gp.classList.add('nav-link');
        nexts_gp.id = 'next-arrow';
        nexts_gp.innerHTML = '>>';

        for(let i = 0; i < 3; i++) {
          let gp_location;
          header_gps.push(document.createElement('li'));
          header_gps[i].classList.add('header-elem');
          header_gps[i].classList.add('nav-item');
          header_gps[i].classList.add('nav-link');

          /*if(i == 0) {
            // prev
            let index = getLastGpIndex(score) - 1;
            gp_location = getGpByIndex(index, score).split('-')[0];
          } else if(i == 1) {
            // last
            gp_location = getLastGpLocation();
          } else {
            // next
            gp_location = getNextGpLocation();
          } */
          
          let gp_index = getGpIndex(gp, score);
          if(i == 0) {
            gp_location = getGpByIndex(gp_index-1, score).split('-')[0];
          } else if(i == 1) {
            gp_location = gp;
          } else {
            gp_location = getGpByIndex(gp_index+1, score).split('-')[0];
          }

          let gp_flag = getFlagElement(gp_location, 'top', false);
          let gp_txt = document.createElement('div');
          gp_txt.classList.add('gp-location');
          gp_txt.classList.add('d-none');
          gp_txt.classList.add('d-sm-flex');
          gp_txt.innerHTML = gp_location.substring(0, 3);

          if(i != 1) {
            header_gps[i].setAttribute('onclick', 'loadTrackLayoutSlot(\'' + gp_location + '\')'); 
          }

          header_gps[i].appendChild(gp_flag);
          header_gps[i].appendChild(gp_txt);
        }
        
        header_gps[1].classList.add('active');
        header_gps[1].classList.add('last-gp');


        header.appendChild(prevs_gp);
        for(let i = 0; i < 3; i++ ) { header.appendChild(header_gps[i]); }
        header.appendChild(nexts_gp);

        

        header.appendChild(accordion_btn_container);
        return header;
      }

      function createTrackBody(score, gp) {
        let gp_index = getGpIndex(gp, score);

        /*
        class="accordion-collapse collapse show"
        aria-labelledby="headingOne"
        data-bs-parent="#accordionExample"
        */
        let body = document.createElement('div');
        body.classList.add('track-slot-body');
        body.classList.add('accordion-collapse');
        body.classList.add('collapse');
        body.classList.add('row');
        body.setAttribute('id', 'track-slot-body');
        body.setAttribute('aria-labelledby', 'track-slot-header');
        body.setAttribute('data-bs-parent', '#accordionExample');

        let gp_location = document.createElement('div');
        let gp_date = document.createElement('div');
        let track_layout = document.createElement('div');
        let track_rank = document.createElement('div');

        gp_location.innerHTML = getGpByIndex(gp_index, score).split('-')[0];
        gp_location.classList.add('gp-location');
        
        gp_date.innerHTML = getGpByIndex(gp_index, score).split('-')[1];
        gp_date.classList.add('gp-date');

        track_layout.classList.add('track-layout');
        track_layout.appendChild(getTrackLayout(getGpByIndex(gp_index, score).split('-')[0]));

        track_rank = getTrackRank(gp_index);
        console.log(track_rank)

        body.appendChild(gp_location);
        body.appendChild(gp_date);
        body.appendChild(track_layout);
        body.appendChild(track_rank);

        return body;
      }

      function createTrackFooter() {
        let footer = document.createElement('div');
        return footer;
      }

      function getTrackRank(gp_index) {
        let score = scoreConverterToArray(getCookie('scores_data'), 31);

        let rank_container = document.createElement('div');
        rank_container.classList.add('track-rank');

        let drivers_rank = document.createElement('div');
        drivers_rank.classList.add('row');
        let drivers_total = [];

        for(let i = 1; i <= 20; i++) {
          drivers_total.push(getDriverPartialPerEachGp(i, score, gp_index)[gp_index - 1]);
        }
        let ordered_indexes = calculateRank(drivers_total, 3);
        for(let i = 0; i < ordered_indexes.length; i++) {
          let div = document.createElement('div');
          div.classList.add('col-4');
          div.classList.add('track-rank-elem');

          div.style.borderLeft = '3px solid ' + getLiveryByDriverId(ordered_indexes[i]);

          div.appendChild(createRankElement(score[0][ordered_indexes[i]].substring(0, 3), score[gp_index][ordered_indexes[i]], true))
          drivers_rank.appendChild(div);
        }




        let teams_rank = document.createElement('div');
        let stable_div = document.createElement('div');
        let team_div = document.createElement('div');
        

        teams_rank.classList.add('row');

        stable_div.classList.add('col-6');
        stable_div.classList.add('track-rank-elem');
        team_div.classList.add('col-6');
        team_div.classList.add('track-rank-elem');

        let stable_total = [];
        for(let i = 1; i <= 10; i++) {
          stable_total.push(getStablePartialPerEachGp(i, score, gp_index)[gp_index - 1]);
        }
        let best_stable = calculateRank(stable_total, 1)[0];

        stable_div.appendChild(createRankElement(score[0][20 + best_stable], score[gp_index][20 + best_stable], false));
        stable_div.style.borderLeft = '3px solid ' + getLiveryByStableId(best_stable);


        getFileContentPromise('teams_score.csv').then(
          function(teams_score) {
            teams_score = teams_score.split('\n').map(function(e) { return e.split(',') });
            let last_score = teams_score[gp_index].slice(1).map(function(e) { return castScore(e)});
            let best_team = calculateRank(last_score, 1);

            
            getTeamsInfoPromise().then(
              function(teams_obj) {
                teams_obj = JSON.parse(teams_obj).map(function(e) { return JSON.parse(e)});
                

                team_div.appendChild(createRankElement(
                  teams_obj[teams_score[0][best_team] - 1].nome_squadra,
                  teams_score[gp_index][best_team],
                  false
                  ));
                
                
                teams_rank.appendChild(stable_div);
                teams_rank.appendChild(team_div);
              }
            )
          }
        )


                
        rank_container.appendChild(drivers_rank);
        rank_container.appendChild(teams_rank);
        return rank_container;
      }

      function calculateRank(arr, rank_dim) {
        let ordered_indexes = [];
        for(let i = 0; i < rank_dim; i++) {
          let max = 0, max_index = -1;
          for(let j = 0; j < arr.length; j++) {
            if(arr[j] >= max) {
              max = arr[j];
              max_index = j; 
            }
          }

          ordered_indexes.push(max_index + 1);
          arr[max_index] = 0;
        }

        return ordered_indexes;
      }

      function createRankElement(value, score, driver_flag) {
        let div = document.createElement('div');
        div.classList.add('row');

        let val_txt = document.createElement('div');
        let score_txt = document.createElement('div');

        val_txt.classList.add('rank-name');
        score_txt.classList.add('rank-score');

        if(driver_flag == false) {
          val_txt.classList.add('col-12');
          score_txt.classList.add('col-12');
          val_txt.classList.add('col-sm-6');
          score_txt.classList.add('col-sm-6');
        } else {
          val_txt.classList.add('col-6');
          score_txt.classList.add('col-6');
        }

        val_txt.innerHTML = value;
        score_txt.innerHTML = ' (' + score + ')';

        div.appendChild(val_txt);
        div.appendChild(score_txt);

        return div;
      }

      function loadTrackLayoutSlot(gp_location) {
        let track_slot = document.getElementById('track-slot');

        while (track_slot.firstChild) {
          track_slot.removeChild(track_slot.firstChild);
        }

        createTrackSlot(gp_location);

      }
    </script>
  </head>
  <body class="body-pattern">
    <!-- Navbar -->
    <?php include __DIR__."/lib/navbar.php";?>

    <div class="container-fluid">
      <div class="row">
        <!-- tracciato -->
        <div class="col-12 col-xl-3 accordion" id="accordionExample">
          <div class="container main-container sticky-top accordion-item" id="track-slot" style="top: 76px; z-index: 1">

          </div>
        </div>

        <!-- classifica -->
        <div class="col-12 col-xl-5 order-xl-1 order-2">
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
        <div class="col-12 col-xl-4 order-1">
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
        </div><!-- /squadra -->
      </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

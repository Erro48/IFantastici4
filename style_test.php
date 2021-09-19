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
      /*window.onload = function() {
        createTrackSlot(getLastGpLocation());
      }*/

      $(window).on('load', function() {
        createTrackSlot(getLastGpLocation());
        setTimeout(removeLoader, 2000);
      })


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
        header.setAttribute('id', 'track-slot-header');

        let prevs_gp = createHeaderArrows(0, gp, score); //document.createElement('li');
        let header_gps = [];
        let nexts_gp = createHeaderArrows(1, gp, score); //document.createElement('li');

        /*prevs_gp.classList.add('header-elem');
        prevs_gp.classList.add('nav-item');
        prevs_gp.classList.add('nav-link');
        prevs_gp.id = 'prev-arrow';
        prevs_gp.innerHTML = '<<';
        prevs_gp.setAttribute('onclick', 'createAllGpView(0, \'' + gp + '\')');*/

        /*nexts_gp.classList.add('header-elem');
        nexts_gp.classList.add('nav-item');
        nexts_gp.classList.add('nav-link');
        nexts_gp.id = 'next-arrow';
        nexts_gp.innerHTML = '>>';
        nexts_gp.setAttribute('onclick', 'createAllGpView(1, \'' + gp + '\')'); */

        for(let i = 0; i < 3; i++) {
          let gp_location;
          header_gps.push(document.createElement('li'));
          header_gps[i].classList.add('header-elem');
          header_gps[i].classList.add('nav-item');
          header_gps[i].classList.add('nav-link');
          
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
            if(gp_location != '')
              header_gps[i].setAttribute('onclick', 'loadTrackLayoutSlot(\'' + gp_location + '\')'); 
          }

          if(gp_location != '')
            header_gps[i].appendChild(gp_flag);
          header_gps[i].appendChild(gp_txt);
        }
        
        header_gps[1].classList.add('active');
        header_gps[1].classList.add('last-gp');


        header.appendChild(prevs_gp);
        for(let i = 0; i < 3; i++ ) { header.appendChild(header_gps[i]); }
        header.appendChild(nexts_gp);

        

        /*header.appendChild(accordion_btn_container);*/
        return header;
      }

      function createTrackBody(score, gp) {
        let gp_index = getGpIndex(gp, score);

        let body = document.createElement('div');
        body.classList.add('track-slot-body');
        /*body.classList.add('accordion-collapse');
        body.classList.add('collapse');*/
        body.classList.add('row');
        /*body.setAttribute('id', 'track-slot-body');
        body.setAttribute('aria-labelledby', 'track-slot-header');
        body.setAttribute('data-bs-parent', '#accordionExample');*/

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

        let drivers_rank = getDriversTrackSlotRank(score, gp_index);
        let stables_rank = getStablesBestAndWorst(score, gp_index);
        let teams_rank = getTeamsBestAndWorst(score, gp_index);
      
        rank_container.appendChild(drivers_rank);
        rank_container.appendChild(stables_rank);
        rank_container.appendChild(teams_rank);
        return rank_container;
      }

      function getDriversTrackSlotRank(score, gp_index) {
        let drivers_rank = document.createElement('div');
        let drivers_rank_title = document.createElement('div');

        let drivers_total = [];
        let ordered_indexes;
        
        drivers_rank.classList.add('row');
        drivers_rank_title.classList.add('col-12');
        drivers_rank_title.classList.add('rank-title');
        drivers_rank_title.innerHTML = 'Piloti';

        drivers_rank.appendChild(drivers_rank_title);

        for(let i = 1; i <= 20; i++) {
          drivers_total.push(getDriverPartialPerEachGp(i, score, gp_index)[gp_index - 1]);
        }
        
        ordered_indexes = calculateRank(drivers_total, 3);

        for(let i = 0; i < ordered_indexes.length; i++) {
          let div = document.createElement('div');
          let driver = createRankElement(score[0][ordered_indexes[i]].substring(0, 3), score[gp_index][ordered_indexes[i]], true);
          
          div.classList.add('col');
          div.classList.add('track-rank-elem');
          div.style.borderLeft = '3px solid ' + getLiveryByDriverId(ordered_indexes[i]);

          div.appendChild(driver);
          drivers_rank.appendChild(div);
        }

        return drivers_rank;
      }

      function getStablesBestAndWorst(score, gp_index) {
        let stables_rank = document.createElement('div');
        let best_stable_div = document.createElement('div');
        let worst_stable_div = document.createElement('div');

        let stable_title = document.createElement('div');
        let stable_subtitle = document.createElement('div');
        let best_txt = document.createElement('div');
        let worst_txt = document.createElement('div');
        
        
        stables_rank.classList.add('row');
        best_stable_div.classList.add('col');
        best_stable_div.classList.add('track-rank-elem');
        worst_stable_div.classList.add('col');
        worst_stable_div.classList.add('track-rank-elem');
        
        stable_title.classList.add('col-12');
        stable_title.classList.add('rank-title');
        stable_title.innerHTML = 'Scuderie';
        stable_subtitle.classList.add('row');
        stable_subtitle.classList.add('rank-subtitle');

        best_txt.classList.add('col');
        best_txt.innerHTML = 'Migliore';

        worst_txt.classList.add('col');
        worst_txt.innerHTML = 'Peggiore';

        stable_subtitle.appendChild(best_txt);
        stable_subtitle.appendChild(worst_txt);

        stables_rank.appendChild(stable_title);
        stables_rank.appendChild(stable_subtitle);
        
        let stable_total = [];
        for(let i = 1; i <= 10; i++) {
          stable_total.push(getStablePartialPerEachGp(i, score, gp_index)[gp_index - 1]);
        }
        
        let best_stable = calculateRank(stable_total, 1)[0];
        let worst_stable = calculateRank(stable_total, -1)[0];


        best_stable_div.appendChild(createRankElement(score[0][20 + best_stable], score[gp_index][20 + best_stable], false));
        best_stable_div.style.borderLeft = '3px solid ' + getLiveryByStableId(best_stable);
        worst_stable_div.appendChild(createRankElement(score[0][20 + worst_stable], score[gp_index][20 + worst_stable], false));
        worst_stable_div.style.borderLeft = '3px solid ' + getLiveryByStableId(worst_stable);

        stables_rank.appendChild(best_stable_div);
        stables_rank.appendChild(worst_stable_div);

        return stables_rank;
      }

      function getTeamsBestAndWorst(score, gp_index) {
        let teams_rank = document.createElement('div');
        let best_team_div = document.createElement('div');
        let worst_team_div = document.createElement('div');
        
        let team_title = document.createElement('div');
        let team_subtitle = document.createElement('div');
        let team_best_txt = document.createElement('div');
        let team_worst_txt = document.createElement('div');

        teams_rank.classList.add('row');

        best_team_div.classList.add('col');
        best_team_div.classList.add('track-rank-elem');
        worst_team_div.classList.add('col');
        worst_team_div.classList.add('track-rank-elem');

        team_title.classList.add('col-12');
        team_title.classList.add('rank-title');
        team_title.innerHTML = 'Squadre';

        team_subtitle.classList.add('row');
        team_subtitle.classList.add('rank-subtitle');
        team_best_txt.classList.add('col');
        team_best_txt.innerHTML = 'Migliore';

        team_worst_txt.classList.add('col');
        team_worst_txt.innerHTML = 'Peggiore';
        team_subtitle.appendChild(team_best_txt);
        team_subtitle.appendChild(team_worst_txt);

        teams_rank.appendChild(team_title);
        teams_rank.appendChild(team_subtitle);

        getFileContentPromise('teams_score.csv').then(
          function(teams_score) {
            teams_score = teams_score.split('\n').map(function(e) { return e.split(',') });
            let last_score = teams_score[gp_index].slice(1).map(function(e) { return castScore(e)});
            let best_team = calculateRank(last_score, 1);
            let worst_team = calculateRank(last_score, -1);

            
            getTeamsInfoPromise().then(
              function(teams_obj) {
                teams_obj = JSON.parse(teams_obj).map(function(e) { return JSON.parse(e)});
                

                best_team_div.appendChild(createRankElement(
                  teams_obj[teams_score[0][best_team] - 1].nome_squadra,
                  teams_score[gp_index][best_team],
                  false
                  ));

                worst_team_div.appendChild(createRankElement(
                  teams_obj[teams_score[0][worst_team] - 1].nome_squadra,
                  teams_score[gp_index][worst_team],
                  false
                  ));
                
                
                
                teams_rank.appendChild(best_team_div);
                teams_rank.appendChild(worst_team_div);
                //teams_rank.appendChild(team_div);
              }
            )
          }
        );

        return teams_rank;
      }

      function calculateRank(arr, rank_dim) {
        let ordered_indexes = [];
        let tmp_arr = arr.slice();

        if(rank_dim > 0) {
          for(let i = 0; i < Math.abs(rank_dim); i++) {
            let max = 0, max_index = -1;
            for(let j = 0; j < tmp_arr.length; j++) {
              if(tmp_arr[j] >= max) {
                max = tmp_arr[j];
                max_index = j; 
              }
            }

            ordered_indexes.push(max_index + 1);
            tmp_arr[max_index] = 0;
          }
        } else {
          for(let i = 0; i < Math.abs(rank_dim); i++) {
            let min = tmp_arr[0], min_index = -1;
            for(let j = 0; j < tmp_arr.length; j++) {
              if(tmp_arr[j] <= min) {
                min = tmp_arr[j];
                min_index = j; 
              }
            }

            ordered_indexes.push(min_index + 1);
            tmp_arr[min_index] = 0;
          }
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
          /*val_txt.classList.add('col-12');
          score_txt.classList.add('col-12');
          val_txt.classList.add('col-sm-6');
          score_txt.classList.add('col-sm-6');*/
          val_txt.classList.add('col-12');
          score_txt.classList.add('col-12');
        } else {
          val_txt.classList.add('col');
          score_txt.classList.add('col');
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

      function createHeaderArrows(side, gp, score) {
        let dropdown_container = document.createElement('li');
        let dropdown_btn = document.createElement('button');
        let dropdown_ul = document.createElement('ul');

        dropdown_container.classList.add('btn-group');
        side == 0 ? dropdown_container.classList.add('dropend') : dropdown_container.classList.add('dropstart');

        dropdown_btn.classList.add('btn');
        dropdown_btn.classList.add('dropdown-toggle');
        dropdown_btn.classList.add('dropdown-arrows');
        dropdown_btn.setAttribute('type', 'button');
        dropdown_btn.setAttribute('data-bs-toggle', 'dropdown');
        dropdown_btn.setAttribute('aria-expanded', 'false');
        dropdown_btn.innerHTML = side == 0 ? '<<' : '>>';

        dropdown_ul.classList.add('dropdown-menu');


        let all_gps = getCookie('gps').split(',').map(function(e) { return e.split('-')[0]; });
        let gp_index = getGpIndex(gp, score);
        let selected_gps = side == 0 ? all_gps.slice(0, gp_index - 2) : all_gps.slice(gp_index + 1) ;

        console.log(selected_gps);
        for(let i = 0; i < selected_gps.length; i++) {
          let dropdown_li = document.createElement('li');
          dropdown_li.setAttribute('onclick', 'loadTrackLayoutSlot(\'' + selected_gps[i] + '\')');
          dropdown_li.innerHTML = selected_gps[i];
          dropdown_ul.appendChild(dropdown_li);
        }


        dropdown_container.appendChild(dropdown_btn);
        dropdown_container.appendChild(dropdown_ul);

        return dropdown_container;
      }
    </script>
  </head>
  <body class="body-pattern">
    <!-- Navbar -->
    <?php include __DIR__."/lib/navbar.php";?>
    <?php include __DIR__."/lib/loader.php";?>

    <div class="content container-fluid">
      <div class="row">
        <!-- tracciato -->
        <div class="col-12 col-xl-3 order-3 order-lg-1">
          <div class="container main-container sticky-top" id="track-slot">
            
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

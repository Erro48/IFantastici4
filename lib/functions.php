<?php
// FILE CON FUNZIONI IN PHP

/* -------------------------------------------------
  GENERAL FUNCTIONS
----------------------------------------------------- */


function today() {
  return new DateTime("now");
}

function dateTimeToString($date, $format) {
  return $date->format($format);
}

function stringToDateTime($string) {
  global $DEFAULT_DATETIME_FORMAT;

  $dateTime = DateTime::createFromFormat($DEFAULT_DATETIME_FORMAT, $string);
  return $dateTime;
}

function diffDate($origin, $target) {
  global $DEFAULT_DATETIME_FORMAT;

  if(is_string($origin)) {
    $origin = stringToDateTime($origin);
  }
  
  if(is_string($target)) {
    $target = stringToDateTime($target);
  }
  
  $interval = $origin->diff($target);
  return $interval->format('%R%a');
}


/* -------------------------------------------------
  GETTER
----------------------------------------------------- */

function getUsersId() {
  global $db;
  $sql = 'SELECT id_utente FROM tutenti';
  $result = $db->query($sql);

  return $result;
}

function getDrivers() {
  global $db;
  $sql = 'SELECT id_pilota, cognome_pilota, nome_pilota, nome_scuderia FROM tpiloti, tscuderie WHERE k_scuderia=id_scuderia ORDER BY id_pilota';
  $result = $db->query($sql);

  return $result;
}

function getStables() {
  global $db;
  $sql = 'SELECT * FROM tscuderie ORDER BY id_scuderia';
  $result = $db->query($sql);

  return $result;
}

function getDriversInfo($stable_short_name) {
  global $db;
  if($stable_short_name == true)
    $sql = 'SELECT id_pilota, cognome_pilota, nome_pilota, nome_breve as "scuderia" FROM tpiloti, tscuderie WHERE k_scuderia=id_scuderia ORDER BY id_pilota';
  else
    $sql = 'SELECT id_pilota, cognome_pilota, nome_pilota, nome_scuderia as "scuderia" FROM tpiloti, tscuderie WHERE k_scuderia=id_scuderia ORDER BY id_pilota';

  $result = $db->query($sql);
  $drivers = [];

  if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($drivers, array(
        "id_pilota" => $row['id_pilota'],
        "cognome_pilota" => $row['cognome_pilota'],
        "nome_pilota" => $row['nome_pilota'],
        "nome_scuderia" => $row['scuderia']
      ));
    }
  }

  return json_encode($drivers);

}

function getStablesInfo() {
  global $db;
  $sql = 'SELECT id_scuderia, nome_scuderia, nome_breve FROM tscuderie ORDER BY id_scuderia';
  $result = $db->query($sql);
  $stables = [];

  if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($stables, array(
        "id_scuderia" => $row['id_scuderia'],
        "nome_scuderia" => $row['nome_scuderia'],
        "nome_breve" => $row['nome_breve']
      ));
    }
  }

  return json_encode($stables);

}

// get by user id
function getTeamNameByUserId($user_id) {
  global $db;
  $sql = 'SELECT id_squadra, nome_squadra FROM tsquadre, tutenti WHERE id_squadra=(SELECT k_squadra FROM tutenti WHERE id_utente='.$user_id.')';
  $result = $db->query($sql);

  return $result;
}

function getTeamNameByTeamId($team_id) {
  global $db;
  $sql = 'SELECT nome_squadra FROM tsquadre WHERE id_squadra='.$team_id;
  $res = $db->query($sql);
  if($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    return $row['nome_squadra'];
  }

  return 'ERROR';
}

function getTeamInfoByUserId($user_id) {
  global $db;
  $sql = 'SELECT id_squadra, nome_squadra, nome_utente FROM tsquadre, tutenti WHERE id_utente='.$user_id.' AND id_squadra=k_squadra';
  $result = $db->query($sql);

  return $result;
}

// get by team id
function getDriversByTeamId($team_id, $active, $champ){
  global $db;
  $sql = 'SELECT cognome_pilota, nome_pilota, T.id_pilota as id_pilota, nome_breve as nome_scuderia, T.prezzo_base FROM tpiloti T, rpossiede P, tscuderie S WHERE T.id_pilota=P.id_pilota AND k_scuderia=id_scuderia AND P.id_squadra='.$team_id.' AND P.attivo='.$active.' AND P.campionato_corrente='.$champ.' ORDER BY T.id_pilota';
  $result = $db->query($sql);

  return $result;
}

function getDriversByTeamIdToString($team_id, $active, $champ) {
  $result = getDriversByTeamId($team_id, $active, $champ);
  $drivers = [];

  if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      array_push($drivers, $row['cognome_pilota']);
    }
  }

  return $drivers;
}

function getTurboAndMegaDriverByTeamId($team_id) {
  global $db;
  $sql = 'SELECT turbo_driver, mega_driver, mega_driver_flag FROM tsquadre WHERE id_squadra='.$team_id;
  $result = $db->query($sql);

  return $result;
}

function getStableByTeamId($team_id) {
  global $db, $SUMMER_BREAK_DATE;
  //echo stringToDateTime('15/08/2021')->format('d-m-Y');
  $diff_date = diffDate(today(), stringToDateTime($SUMMER_BREAK_DATE));

  if($diff_date > 0)
    $sql = 'SELECT nome_breve, prezzo_base FROM tscuderie S, tsquadre T WHERE k_scuderia=id_scuderia AND id_squadra='.$team_id;
  else
    $sql = 'SELECT nome_breve, prezzo_base FROM tscuderie S, tsquadre T WHERE k_2scuderia=id_scuderia AND id_squadra='.$team_id;

  
  $result = $db->query($sql);

  return $result;
}

function getStableByTeamIdToString($team_id) {
  $result = getStableByTeamId($team_id);

  if($result->num_rows > 0) {
    return $result->fetch_assoc()['nome_breve'];
  }
}


/* ----------------------------------------
  PRINT
------------------------------------------ */

function printDriverCard($driver, $td_md_info) {
  echo '<div class="col card-col skeleton">
          <div id="'.$driver['cognome_pilota'].'-card" class="card text-white bg-dark xs-3">
            <div class="card-header">
              <div class="row">
                <div class="col-9">
                  <div class="card-title skeleton skeleton-text">'.$driver['cognome_pilota'].'</div>
                  <div class="card-subtitle skeleton skeleton-text">'.$driver['nome_pilota'].'</div>
                </div>

                <div class="col-1 col-sm-3 d-none d-sm-block header-img" style="background-image: url(./images/drivers/'.strtolower($driver['cognome_pilota']).'.png);">
                </div>

              </div>
              
              
            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-7 col-sm-12">
                  <p class="card-text">
                    <div class="row md-td-container">';

  // mega driver
  if(!strcmp($td_md_info['mega_driver'], $driver['cognome_pilota'])){
    echo        '<div class="col-xs-6 md-btn mega-driver" onclick="setMegaDriver(this);">MD</div>';
  } else {
    echo        '<div class="col-xs-6 md-btn" onclick="setMegaDriver(this);">MD</div>';
  }

  // turbo driver
  if(!strcmp($td_md_info['turbo_driver'], $driver['cognome_pilota'])){
    echo        '<div class="col-xs-6 td-btn turbo-driver" onclick="setTurboDriver(this);">TD</div>';
  } else {
    echo        '<div class="col-xs-6 td-btn" onclick="setTurboDriver(this);">TD</div>';
  }

  echo '        </div>
                </p>

                <div class="row card-info">
                  <div id="'.$driver['cognome_pilota'].'-price" class="col-12">Prezzo: '.$driver['prezzo_base'].'$</div>
                  <div class="col-12 last-score">Punti: -</div>

                </div>

                </div> <!-- chiusura col -->

                <div class="col-5 d-sm-none body-img" style="background-image: url(./images/drivers/'.strtolower($driver['cognome_pilota']).'.png);">
                </div>


              </div>
            </div>
          </div>
        </div>';
}

function printStableCard($stable) {
  echo '<div class="col">
         <div id="'.$stable['nome_breve'].'-card" class="card text-white bg-dark sm-3" >
           <div class="card-header">
              <div class="col-9">
                <div class="card-title">'.$stable['nome_breve'].'</div>
              </div>

             <div class="col-1 col-sm-3 d-none d-sm-block header-img-stable" style="background-image: url(./images/stables/'.strtolower(str_replace(" ", "_", $stable['nome_breve'])).'.svg);">
             </div>
           </div>

           <div class="card-body">
             <p id="stable-price" class="card-text">Prezzo: '.$stable['prezzo_base'].'$</p>
             <p id="stable-last-score" class="card-text">Punti: -</p>
           </div>
         </div>
       </div>';
}

function printNavItem($label, $active, $driver) {
  $show = "";
  $display = "d-none d-sm-block";

  if($active == "true") {
    $show = "active";
    $display = "";
  }

  $label_ = str_replace(" ", "_", $label);

  if($driver) { $label = substr($label, 0, 3); }

  echo '
        <li class="nav-item my-nav-item '.$display.'" role="presentation">
          <button id="'.$label_.'-tab" class="nav-link '.$show.'" data-bs-toggle="tab" data-bs-target="#'.$label_.'-tab-pane" type="button" role="tab" aria-controls="'.$label_.'-tab-pane" aria-selected="'.$active.'">
            '.$label.'
          </button>
        </li>
  ';
}

function printDriverTabPane($label, $active) {
  $show = "";
  if($active) {
    $show = "show active ";
  }

  $label_ = str_replace(" ", "_", $label);

  echo '
  <div class="tab-pane fade '.$show.'my-tab-pane" id="'.$label_.'-tab-pane" role="tabpanel" aria-labelledby="'.$label_.'-tab">
    <div class="row">
        <div class="col-12 col-lg-7 personal-data-col">
          <div class="row">
            <div class="img-row col-xs-12 col-8">
              <div class="row">
                <div class="col-12 d-flex align-items-center">'.$label.'</div>
              </div>

              <div class="row">
                <div class="col-12">Nome</div>
              </div>

              <div class="row">
                <div class="col-12">Scuderia</div>
              </div>
            </div>
            <div class="img-row d-flex d-lg-none col-4">
            </div>
          </div>

          <div class="row">
            <div class="col-6">P. reale</div>
            <div class="col-6 d-flex align-items-center justify-content-end text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">P. base</div>
            <div class="col-6 d-flex align-items-center justify-content-end text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Posizione</div>
            <div class="col-6 d-flex align-items-center justify-content-end text-end position-text">-</div>
          </div>

          <div class="row">
            <div class="col-6">Totale</div>
            <div class="col-6 d-flex align-items-center justify-content-end text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Media</div>
            <div class="col-6 d-flex align-items-center justify-content-end text-end">-</div>
          </div>

          <div class="row">
            <div class="col-7">Miglior gp</div>
            <div class="col-5 d-flex align-items-center justify-content-end text-end">-</div>
          </div>

          <div class="row">
            <div class="col-7">Peggior gp</div>
            <div class="col-5 d-flex align-items-center justify-content-end text-end">-</div>
          </div>
        </div>
        
        <!--<div class="d-lg-none d-flex justify-content-center">
            <hr class=" w-100">
        </div> -->
        

        <div class="col-12 d-none d-lg-flex col-lg-4 driver-image-col p-0">
            
        </div>
    </div>
  </div>
  ';
}

function printStableTabPane($label, $active) {
  $show = "";
  if($active) {
    $show = "show active ";
  }

  $label_ = str_replace(" ", "_", $label);

  echo '
  <div class="tab-pane fade '.$show.'my-tab-pane" id="'.$label_.'-tab-pane" role="tabpanel" aria-labelledby="'.$label_.'-tab">
    <div class="row">
        <div class="col-12 col-lg-6 personal-data-col">
            <div class="row">
              <div class="col-4">Scuderia</div>
              <div class="col-8 text-end">'.$label.'</div>
            </div>

            <div class="row">
              <div class="col-6">#1 pilota</div>
              <div class="col-6 text-end">-</div>
            </div>

            <div class="row">
              <div class="col-6">#2 pilota</div>
              <div class="col-6 text-end">-</div>
            </div>

            <div class="row">
              <div class="col-6">P. reale</div>
              <div class="col-6 text-end">-</div>
            </div>

            <div class="row">
              <div class="col-6">P. base</div>
              <div class="col-6 text-end">-</div>
            </div>

            <div class="row">
              <div class="col-6">Posizione</div>
              <div class="col-6 text-end position-text">-</div>
            </div>

            
        </div>
        
        <!--<div class="d-lg-none d-flex justify-content-center">
            <hr class=" w-100">
        </div> -->
        

        <div class="col-12 col-lg-6 championship-data-col">
          <div class="row">
            <div class="col-6">Totale</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Media</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Miglior gp</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Peggior gp</div>
            <div class="col-6 text-end">-</div>
          </div>
        </div>
    </div>

    <div class="row p-3 justify-content-center livery-image-container">
      <div class="col-12 livery-image-div">
        <img src="./images/liverys/'.strtolower($label_).'.png" alt="car pic" width="100%" height="100%">
        </div>
    </div>
  </div>
  ';
}

function printTeamTabPane($team_id) {
  echo '
  <div class="tab-pane fade show active my-tab-pane" id="'.$team_id.'-tab-pane" role="tabpanel" aria-labelledby="'.$team_id.'-tab">
    <div class="row">
        <div class="col-12 personal-team-data-col">
          <div class="row">
            <div class="col-4">Team</div>
            <div class="col-8 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Proprietario</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Turbo driver</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Posizione</div>
            <div class="col-6 d-flex align-items-center justify-content-end text-end position-text">-</div>
          </div>

          <div class="row">
            <div class="col-6">Totale</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Media</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Miglior gp</div>
            <div class="col-6 text-end">-</div>
          </div>

          <div class="row">
            <div class="col-6">Peggior gp</div>
            <div class="col-6 text-end">-</div>
          </div>
        </div>
        
        <!--<div class="d-lg-none d-flex justify-content-center">
            <hr class=" w-100">
        </div> -->
        

        <!--<div class="col-12 col-lg-6 championship-team-data-col">
          
        </div> -->
    </div>
  </div>
  ';
}

function printOffcanvasText($label) {
  echo '
  <div type="button" class="text-reset offcanvas-text" data-bs-dismiss="offcanvas" aria-label="Close" onclick="loadTabPane(this);">
    '.$label.'
  </div>
  ';
}

 ?>

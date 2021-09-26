<?php

// FILE CON FUNZIONI PER RICHIESTE AJAX DI JS

  include __DIR__."/mysql.php";
  include __DIR__."/const.php";
  include __DIR__."/functions.php";
  session_start();

  if(isset($_POST["new_team_name"])){
// cambia il nome della squadra
      $sql = 'UPDATE tsquadre, tutenti SET nome_squadra=? WHERE id_utente='.$_SESSION['id_utente'].' AND k_squadra=id_squadra';
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $_POST['new_team_name']);
      $result = $stmt->execute();

  }elseif(isset($_POST["new_td"])) {
// imposta il nuovo turbo driver
      $sql = 'UPDATE tsquadre, tutenti SET turbo_driver=? WHERE id_utente='.$_SESSION['id_utente'].' AND k_squadra=id_squadra';
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $_POST['new_td']);
      $result = $stmt->execute();

  }elseif(isset($_POST["new_md"])) {
// imposta il nuovo mega driver
      $sql = 'UPDATE tsquadre, tutenti SET mega_driver=?, mega_driver_flag=1 WHERE id_utente='.$_SESSION['id_utente'].' AND k_squadra=id_squadra';
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $_POST['new_md']);
      $result = $stmt->execute();

  } elseif(isset($_POST['get_md_info'])) {
// prendo info su chi è md e flag_md
    $sql = 'SELECT mega_driver, mega_driver_flag FROM tsquadre, tutenti WHERE id_utente='.$_SESSION['id_utente'].' AND k_squadra=id_squadra';
    $result = $db->query($sql);

    if($result->num_rows > 0) {
      $row = $result->fetch_assoc();

      $json_ = array(
        "md_surname" => $row['mega_driver'],
        "md_flag" => $row['mega_driver_flag']);

        echo json_encode($json_);
    }

  } elseif(isset($_POST['set_md_null'])) {
    //imposta a null il md
    $sql = 'UPDATE tsquadre, tutenti SET mega_driver=NULL WHERE id_utente='.$_SESSION['id_utente'].' AND k_squadra=id_squadra';
    $result = $db->query($sql);

  } elseif(isset($_POST["driver"])) {
// seleziona il prezzo base di un pilota
      $sql= 'SELECT prezzo_base FROM tpiloti WHERE cognome_pilota=\''.$_POST['driver'].'\'';
      $result = $db->query($sql);

      if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        echo $row['prezzo_base'];
      }
  }elseif(isset($_POST["driver_surname"])) {
    // NON USO PIU
    // mi passa driver, gli ritorno l'indice
    $sql = 'SELECT id_pilota FROM tpiloti WHERE cognome_pilota=\''.$_POST['driver_surname'].'\'';
    $result = $db->query($sql);

    if($result->num_rows > 0){
      $row = $result->fetch_assoc();

      echo $row['id_pilota'];
    }
  }elseif(isset($_POST["stable_name"])) {
    // mi passa driver, gli ritorno l'indice
    $sql = 'SELECT id_scuderia FROM tscuderie WHERE nome_breve=\''.$_POST['stable_name'].'\'';
    $result = $db->query($sql);

    if($result->num_rows > 0){
      $row = $result->fetch_assoc();

      echo $row['id_scuderia'];
    }
  } elseif(isset($_POST['get_teams'])) {
    $diff_date = diffDate(today(), stringToDateTime($SUMMER_BREAK_DATE));

    if($diff_date > 0)
      $sql = 'SELECT nome_squadra, id_squadra, turbo_driver, mega_driver, id_scuderia, punteggio_squadra, punteggio_precedente_squadra, nome_utente FROM tsquadre, tscuderie, tutenti WHERE id_scuderia=k_scuderia AND k_squadra=id_squadra ORDER BY id_squadra';
    else
      $sql = 'SELECT nome_squadra, id_squadra, turbo_driver, mega_driver, id_scuderia, punteggio_squadra, punteggio_precedente_squadra, nome_utente FROM tsquadre, tscuderie, tutenti WHERE id_scuderia=k_2scuderia AND k_squadra=id_squadra ORDER BY id_squadra';
    
    $result = $db->query($sql);

    $json_obj = [];

    if($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $sql_2 = 'SELECT P.id_pilota as "idpilota" FROM tpiloti P, tsquadre S, rpossiede R WHERE S.id_squadra='.$row['id_squadra'].' AND R.id_squadra=S.id_squadra AND R.id_pilota=P.id_pilota AND R.attivo=1';
        $result_2 = $db->query($sql_2);
        $obj = new stdClass();

        $obj->id_squadra = $row['id_squadra'];
        $obj->nome_squadra = $row['nome_squadra'];
        $obj->proprietario = $row['nome_utente'];
        $obj->id_scuderia = $row['id_scuderia'];
        $obj->punteggio = $row['punteggio_squadra'];
        $obj->punteggio_precedente = $row['punteggio_precedente_squadra'];

// select turbo driver
        $sql_3 = 'SELECT id_pilota as "id_turbo_driver" FROM tsquadre S, tscuderie, tpiloti WHERE id_scuderia=S.k_scuderia AND turbo_driver=cognome_pilota AND id_squadra='.$row['id_squadra'];
        $result_3 = $db->query($sql_3);

        if($result_3->num_rows > 0){
          $row_3 = $result_3->fetch_assoc();
          $obj->turbo_driver = $row_3['id_turbo_driver'];
        }

        $sql_4 = 'SELECT P.id_pilota as "id_mega_driver", cognome_pilota, nome_squadra FROM tsquadre S, tpiloti P WHERE mega_driver=cognome_pilota AND id_squadra='.$row['id_squadra'];
        $result_4 = $db->query($sql_4);

        if($result_4->num_rows > 0){
          $row_4 = $result_4->fetch_assoc();
          $obj->mega_driver = $row_4['id_mega_driver'];
        }


        $driver_list = [];

        if($result_2->num_rows > 0) {
          while($row_2 = $result_2->fetch_assoc()) {
            array_push($driver_list, $row_2['idpilota']);
          }
        }

        $obj->drivers = $driver_list;

        array_push($json_obj, json_encode($obj));

      }
    }

    echo json_encode($json_obj);

  } elseif(isset($_POST['get_drivers_by_team_id'])) {
    $sql = 'SELECT P.cognome_pilota FROM tpiloti P, rpossiede R WHERE R.id_squadra='.$_POST['get_drivers_by_team_id'].' AND R.id_pilota=P.id_pilota AND R.attivo=1';
    $result = $db->query($sql);
    $obj = [];

    if($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        array_push($obj, $row['cognome_pilota']);
      }
    }
    
    $diff_date = diffDate(today(), stringToDateTime($SUMMER_BREAK_DATE));

    if($diff_date > 0)
      $sql = 'SELECT nome_breve FROM tsquadre, tscuderie WHERE id_squadra='.$_POST['get_drivers_by_team_id'].' AND id_scuderia=k_scuderia';
    else
      $sql = 'SELECT nome_breve FROM tsquadre, tscuderie WHERE id_squadra='.$_POST['get_drivers_by_team_id'].' AND id_scuderia=k_2scuderia';

    $result = $db->query($sql);

    if($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      array_push($obj, $row['nome_breve']);
      
    }

    echo json_encode($obj);


  } elseif(isset($_POST['get_drivers_data'])) {
    global $db;
    $sql = 'SELECT cognome_pilota, nome_pilota, P.prezzo_reale as prezzo_reale, P.prezzo_base as prezzo_base, nome_scuderia 
    FROM tpiloti P, tscuderie S, tsquadre Q, rpossiede R 
    WHERE Q.id_squadra='.$_POST['get_drivers_data'].' AND R.id_squadra='.$_POST['get_drivers_data'].' AND R.id_pilota=P.id_pilota AND P.k_scuderia=S.id_scuderia AND R.attivo=1 AND R.campionato_corrente=1 
    ORDER BY P.id_pilota';
    $result = $db->query($sql);

    $obj = [];

    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()) {
        $driver = array(
          'driver_surname' => $row['cognome_pilota'],
          'driver_name' => $row['nome_pilota'],
          'driver_stable' => $row['nome_scuderia'],
          'real_price' => $row['prezzo_reale'],
          'base_price' => $row['prezzo_base']
        );

        array_push($obj, $driver);
      }
    }

    echo json_encode($obj);

  } elseif(isset($_POST['get_stable_data'])) {
    global $db;

    $diff_date = diffDate(today(), stringToDateTime($SUMMER_BREAK_DATE));

    if($diff_date > 0) {
      $sql = 'SELECT nome_scuderia, nome_breve, S.prezzo_reale, S.prezzo_base, FP.cognome_pilota as "primo_pilota", SP.cognome_pilota as "secondo_pilota"
      FROM tscuderie S, tsquadre Q, tpiloti FP, tpiloti SP 
      WHERE id_squadra='.$_POST['get_stable_data'].' AND Q.k_scuderia=id_scuderia AND FP.k_scuderia=id_scuderia AND SP.k_scuderia=id_scuderia AND FP.id_pilota!=SP.id_pilota AND FP.id_pilota<SP.id_pilota';  
    } else {
      $sql = 'SELECT nome_scuderia, nome_breve, S.prezzo_reale, S.prezzo_base, FP.cognome_pilota as "primo_pilota", SP.cognome_pilota as "secondo_pilota"
      FROM tscuderie S, tsquadre Q, tpiloti FP, tpiloti SP 
      WHERE id_squadra='.$_POST['get_stable_data'].' AND Q.k_2scuderia=id_scuderia AND FP.k_scuderia=id_scuderia AND SP.k_scuderia=id_scuderia AND FP.id_pilota!=SP.id_pilota AND FP.id_pilota<SP.id_pilota';
    
    }
      
    $result = $db->query($sql);

    $stable;

    if($result->num_rows > 0){
      while($row = $result->fetch_assoc()) {
        $stable = array(
          'stable_name' => $row['nome_scuderia'],
          'first_driver' => $row['primo_pilota'],
          'second_driver' => $row['secondo_pilota'],
          'real_price' => $row['prezzo_reale'],
          'base_price' => $row['prezzo_base']
        );

        //array_push($obj, $stable);
      }
    }

    echo json_encode($stable);

  } elseif(isset($_POST['get_team_score'])) {
    global $db;
    $sql = 'SELECT punteggio_squadra FROM tsquadre WHERE id_squadra='.$_POST['get_team_score'];
    $result = $db->query($sql);

    if($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      echo $row['punteggio_squadra'];
    }
  } elseif(isset($_POST['update_teams_score'])) {
    global $db;
    $score = $_POST['update_teams_score'];
    $last_gp_date = $_POST['last_gp_date'];

    for($i = 0; $i < count($score); $i++) {
      $last_gp_date_sql = 'SELECT ultimo_aggiornamento_punteggio_squadra as last_score_update FROM tsquadre WHERE id_squadra='.$score[$i]['id_squadra'];
      $last_gp_date_res = $db->query($last_gp_date_sql);

      $row = $last_gp_date_res->fetch_assoc();
      $last_score_update = $row['last_score_update']; // non serve

      $last_gp_date = str_replace('/', '-', $last_gp_date);

      $origin = new DateTime($last_gp_date);
      $target = new DateTime($last_score_update);

      $day = strval(explode('-', $last_gp_date)[0]);
      $month = strval(explode('-', $last_gp_date)[1]);
      $year = strval(explode('-', $last_gp_date)[2]);


      $from_unix_time = mktime(0, 0, 0, $month, $day, $year);
      $day_before = strtotime("yesterday", $from_unix_time);
      $formatted = date('d-m-Y', $day_before);
      $origin = new DateTime(date('Y-m-d', $day_before));

      $interval = $origin->diff($target);
      $diff_days = intval($interval->format('%R%a'));

      // positivo se last_score_update è dopo last_gp_date

      $sql = 'SELECT punteggio_squadra as last_score, punteggio_precedente_squadra as old_score FROM tsquadre WHERE id_squadra='.$score[$i]['id_squadra'];
      $res = $db->query($sql);

      $row = $res->fetch_assoc();
      $last_score = $row['last_score'];
      $old_score = $row['old_score'];

      if($diff_days <= 0) {
        // inserisco i punti del gp
        $new_score = $last_score + $score[$i]['last_score'];
        $sql = 'UPDATE tsquadre 
                SET punteggio_squadra='.$new_score.', punteggio_precedente_squadra='.$last_score.', ultimo_aggiornamento_punteggio_squadra=\''.date("Y-m-d").'\' 
                WHERE id_squadra='.$score[$i]['id_squadra'];

        $res = $db->query($sql);


        echo $sql;

      } else {
        // aggiorno i punti del gp

      }
    }

    


    /*
      se la data del gp è dopo l'ultimo aggiornamento, allora devo inserire
      se la data del gp è prima, devo aggiornare
    */

  } elseif(isset($_POST['set_gps_session'])) {
    $_SESSION['gps'] = $_POST['set_gps_session'];
  } else {
    echo "Ajax fallito: nessuna funzione associata";
  }

?>

<?php

// FILE CON FUNZIONI PER RICHIESTE AJAX DI JS

  include __DIR__."/mysql.php";
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
    $sql = 'SELECT id_squadra, turbo_driver, mega_driver, id_scuderia FROM tsquadre, tscuderie WHERE id_scuderia=k_scuderia';
    $result = $db->query($sql);

    $json_obj = [];

    if($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $sql_2 = 'SELECT P.id_pilota as "idpilota" FROM tpiloti P, tsquadre S, rpossiede R WHERE S.id_squadra='.$row['id_squadra'].' AND R.id_squadra=S.id_squadra AND R.id_pilota=P.id_pilota';
        $result_2 = $db->query($sql_2);
        $obj = new stdClass();

        $obj->id_squadra = $row['id_squadra'];
        $obj->id_scuderia = $row['id_scuderia'];

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
    $sql = 'SELECT P.cognome_pilota FROM tpiloti P, rpossiede R WHERE R.id_squadra='.$_POST['get_drivers_by_team_id'].' AND R.id_pilota=P.id_pilota';
    $result = $db->query($sql);
    $obj = [];

    if($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        array_push($obj, $row['cognome_pilota']);
      }
    }

    $sql = 'SELECT nome_breve FROM tsquadre, tscuderie WHERE id_squadra='.$_POST['get_drivers_by_team_id'].' AND id_scuderia=k_scuderia';
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
    WHERE Q.id_squadra='.$_POST['get_drivers_data'].' AND R.id_squadra='.$_POST['get_drivers_data'].' AND R.id_pilota=P.id_pilota AND P.k_scuderia=S.id_scuderia 
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
    $sql = 'SELECT nome_scuderia, nome_breve, S.prezzo_reale, S.prezzo_base, FP.cognome_pilota as "primo_pilota", SP.cognome_pilota as "secondo_pilota"
    FROM tscuderie S, tsquadre Q, tpiloti FP, tpiloti SP 
    WHERE id_squadra='.$_POST['get_stable_data'].' AND Q.k_scuderia=id_scuderia AND FP.k_scuderia=id_scuderia AND SP.k_scuderia=id_scuderia AND FP.id_pilota!=SP.id_pilota AND FP.id_pilota<SP.id_pilota';
    
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
  } else {
    echo "Ajax fallito: nessuna funzione associata";
  }

?>

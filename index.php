<?php ob_start(); ?>

<html lang="en" dir="ltr">
  <head>
<?php
    include __DIR__.'/lib/mysql.php';

    session_start();
?>
    <meta charset="utf-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- To prevent web caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link href="./css/custom.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
  </head>
  <body class="">

  <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      

      <div class="row" style="height: 100%">
        <div class="col-12 col-sm-6 d-flex align-items-center body-pattern login-outer-container">
          <div class="container main-container login-container">
            <div class="header">
              <h1>Login</h1>
            </div>
            <form method="post">
              <div class="form-group">
                <label for="exampleInputEmail1">Nome</label>
                <input type="text" class="form-control" id="inputName" name="inputName" aria-describedby="emailHelp" placeholder="Inserisci il tuo nome">
              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Inserisci la password">
              </div>
                <button type="submit" class="btn btn-primary login-btn">Login</button>
              </div>
            </form>

          </div>
        </div>

        <div class="col-12 col-sm-6 p-0 d-none d-sm-block">
          <div class="carousel-item active" data-bs-interval="3000" data-bs-ride="carousel" style="background-image: url('./images/f1-5.jpeg'); min-height: 100%">
          </div>
          <div class="carousel-item" data-bs-interval="3000" data-bs-ride="carousel" style="background-image: url('./images/f1-4.jpeg'); min-height: 100%">
          </div>
          <div class="carousel-item" data-bs-interval="3000" data-bs-ride="carousel" style="background-image: url('./images/f1-6.jpeg'); min-height: 100%">
          </div>
        </div>
      </div>


    </div>
  </div>

<!--
  <div class="row" style="height: 100%">
    <div class="col-12 col-sm-6 d-flex align-items-center body-pattern login-outer-container">
      <div class="container main-container login-container">
        <div class="header">
          <h1>Login</h1>
        </div>
        <form method="post">
          <div class="form-group">
            <label for="exampleInputEmail1">Nome</label>
            <input type="text" class="form-control" id="inputName" name="inputName" aria-describedby="emailHelp" placeholder="Inserisci il tuo nome">
          <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Inserisci la password">
          </div>
            <button type="submit" class="btn btn-primary login-btn">Login</button>
          </div>
        </form>

      </div>
    </div>
  </div>
  -->
    

  <!--  <div class="row" style="width: 100.8vw;">
      <!-- Login --
      <div class="col-12 col-lg-6">
        <div class="container main-container login-container">
          <div class="header">
            <h1>Login</h1>
          </div>

    <!-- form --

        </div>
      </div>

      <!-- Carousel --
      <div class="col-12 col-lg-6 pdg-0">
        <div class="container-fluid pdg-0">
          <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active" style="background-image: url(https://source.unsplash.com/category/nature);">
              </div>
              <div class="carousel-item" style="background-image: url(https://source.unsplash.com/category/food);">
              </div>
              <div class="carousel-item" style="background-image: url(https://source.unsplash.com/category/buildings);">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->



<?php
    if(!empty($_POST['inputName']) && !empty($_POST['inputPassword'])){
      // prendo username e password
      $username = $_POST['inputName'];
      $password = $_POST['inputPassword'];

      // faccio query a db
      $stmt = $db->prepare(
      'SELECT id_utente, nome_utente, password_utente
      FROM tutenti
      WHERE nome_utente=?');

      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if($result->num_rows == 0) {
        // utente non presente
        //echo "<div>Non sei registrato? <a href=\"./registration.php\">Fallo qui!</a></div>";
      } else {
        // se entra qui l'utente esiste
        $row = $result->fetch_assoc();

        if(empty($row['password_utente'])){
          // aggiorna password
          $pssw = password_hash($_POST['password'], PASSWORD_DEFAULT);

          $sql = 'UPDATE tutenti SET password_utente = ? WHERE nome_utente = \''.$row['nome_utente'].'\'';

          $stmt = $db->prepare($sql);
          $stmt->bind_param("s", $pssw);
          $stmt->execute();

          $_SESSION['id_utente'] = $row['id_utente'];
          header('Location: ./home.php');


        }else if(password_verify($password, $row['password_utente'])){
          $_SESSION['id_utente'] = $row['id_utente'];
          header('Location: ./home.php');
        }else{
          echo "password errata";
        }
      }

    }
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>

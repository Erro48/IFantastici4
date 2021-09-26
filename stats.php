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
    <title>Squadra</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- To prevent web caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link href="./node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="./css/custom.css" rel="stylesheet">
    <link href="./css/mycss/style.css" rel="stylesheet">
    <link href="./css/mycss/stats.css" rel="stylesheet">
  
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/stats_script.js"></script>

  </head>
  <body class="body-pattern">
    <!-- Navbar -->
    <?php include __DIR__."/lib/navbar.php"; ?>
    <?php include __DIR__."/lib/loader.php";?>

    <div class="container-md container-fluid content">
        <div class="container-md container-fluid main-container mb-2">
            <div class="header">
                <h3>Statistiche</h3>
            </div>

            <div class="row">
              <!-- colonna stats piloti/scuderia -->
              <div class="col-12 col-sm-6">
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                <?php
                $drivers_surname = getDriversByTeamIdToString($_SESSION['id_squadra'], 1, 1);
                $stable_short_name = getStableByTeamIdToString($_SESSION['id_squadra']);

                for($i = 0; $i < count($drivers_surname); $i++) {
                  if($i == 0) {
                    printNavItem($drivers_surname[$i], 'true', 1);
                  } else {
                    printNavItem($drivers_surname[$i], 'false', 1);
                  }
                }

                printNavItem($stable_short_name, 'false', 0);

                ?>


                  <li class="nav-item my-nav-item d-sm-none" >
                    <button class="nav-link" id="other-tab" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample" aria-selected="false">Altro</button>
                  </li>
                </ul>

                <div class="tab-content" id="myTabContent">

                <?php
                //echo '<script>loadTeamsMembersData('.$_SESSION['id_squadra'].');</script>';
                
                for($i = 0; $i < count($drivers_surname); $i++) {
                  if($i == 0) {
                    printDriverTabPane($drivers_surname[$i], true);
                  } else {
                    printDriverTabPane($drivers_surname[$i], false);
                  }
                }

                printStableTabPane($stable_short_name, false);
                ?>
                </div>
              </div> <!-- /div stats piloti/scuderia -->

              <!-- colonna stats squadra -->
              <div class="col-12 col-sm-6">
                
              </div>
            </div>

            

            <!-- tabella -->
            <div class="container-fluid">
              <div class="header pt-2 d-flex">
                <h3>Tabella</h3>
                <span id="info-icon-container"></span>
              </div>
              <div class="table-responsive mb-2">
                  <table id="stats-chart" class="table table-sm align-middle table-wrapper table-striped">
                    <!--<script>createStatsChart();</script>-->
                  </table>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Offcanvas -->
    <div class="offcanvas offcanvas-start w-75 body-pattern" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
      <div class="offcanvas-header bg-primary text-light align-items-center">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Statistiche</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body" style="backdrop-filter: blur(2px);">
        <div>

            <h5 class="offcanvas-subtitle">Piloti</h5>
          <?php 
            for($i = 0; $i < count($drivers_surname); $i++) {
              printOffcanvasText($drivers_surname[$i]);
            }
          ?>

          <div class="d-lg-none d-flex justify-content-center">
              <hr class="w-100">
          </div>

          <h5 class="offcanvas-subtitle">Scuderia</h5>
          <?php 
            printOffcanvasText($stable_short_name);
          ?>
        </div>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

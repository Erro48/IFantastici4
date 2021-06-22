<!-- Navbar -->

<?php
  if(isset($_POST['log-out'])){
    unset($_SESSION['id_utente']);
    header("Location: ./index.php");
  }
 ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top" id="navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="../Fanta/home.php">I Fanta-stici 4</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../Fanta/home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../Fanta/stats.php">Statistiche</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Asta</a>
        </li>
      </ul>

      <ul class="navbar-nav mr-right">
        <?php
            if(isset($_SESSION['id_utente']) && $_SESSION['id_utente'] == 2){
              echo '<li class="nav-item">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loadscore-modal">
                Load Score
              </button>
              </li>';
            }
        ?>
      </ul>

      <form class="d-flex" method="post">
        <button class="btn my-btn-light" name="log-out" type="submit">Logout</button>
      </form>
    </div>
  </div>
</nav>

<!-- Scrollable modal -->
<div class="modal fade" id="loadscore-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Carica punteggi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        
      </div>
      <div class="modal-subheader">
        <div class="dropdown">
          <span class="dropdown-label">Gran Premio</span>
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            -
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </div>
      </div>
      <div class="modal-body">
        <!-- -->
      <div class="container">
          <form method="post">
            <!-- form -->
            <div class="row">
              <div class="col-12 col-sm-6 drivers-col">
              Piloti
              <?php
                //echo '<script>console.log('.json_encode($_LIVERY["Mercedes"]).')</script>';
              
                $drivers = json_decode(getDriversInfo(true), true);
                

                for($i = 0; $i < count($drivers); $i++){
                  echo '
                  <div class="loadscore-container" style="border-left: 3px solid '.$_LIVERY[$drivers[$i]["nome_scuderia"]].'">
                    <div class="row">
                      <div class="col-9">'.$drivers[$i]['cognome_pilota'].'</div>
                      <div class="col-3">
                        <input type="number" name="'.$drivers[$i]['cognome_pilota'].'-input" class="loadscore-input">
                      </div>
                      
                    </div>
                  </div>';
                }
              ?>
              </div>

              <div class="col-12 col-sm-6 stables-col">
                Scuderie
              <?php
                $stables = json_decode(getStablesInfo(), true);

                for($i = 0; $i < count($stables); $i++){
                  echo '
                  <div class="loadscore-container" style="border-left: 3px solid '.$_LIVERY[$stables[$i]["nome_breve"]].'">
                    <div class="row">
                      <div class="col-9">'.$stables[$i]['nome_scuderia'].'</div>
                      <div class="col-3">
                        <input type="number" name="'.$stables[$i]['nome_scuderia'].'-input" class="loadscore-input">
                      </div>
                      
                    </div>
                  </div>';
                }
              ?>
              </div>  <!-- /stables col -->
            </div> <!-- /row -->
            
            <!-- /form -->
          </form>
        </div>
        <!-- / -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary" onclick="loadScore()">Carica</button>
      </div>
    </div>
  </div>
</div>

<script>
var myModal = document.getElementById('loadscore-modal')
var myInput = document.getElementById('btn-modal')

console.log(myModal);

myModal.addEventListener('shown.bs.modal', function () {
  myInput.focus()
})
</script>

<!-- Navbar -->

<?php
  if(isset($_POST['log-out'])){
    unset($_SESSION['id_utente']);
    header("Location: ./index.php");
  }
 ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary " id="navbar">
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
          <a class="nav-link disabled" aria-current="page" href="../Fanta/stats.php">Squadra</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../Fanta/drivers.php">Piloti</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../Fanta/stables.php">Scuderie</a>
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
    <form method="post">
    <div class="modal-content" style="height: 850px">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Carica punteggi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      
        <div class="modal-subheader">
          <div class="dropdown">
            <span class="dropdown-label">Gran Premio</span>
            <select name="gp" id="gps" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <?php
                if(isset($_SESSION['gps'])) {
                  for($i = 0; $i < count($_SESSION['gps']); $i++) {
                    $gp = explode("-", $_SESSION['gps'][$i])[0];
                    $date = explode("-", $_SESSION['gps'][$i])[1];

                    echo '<option class="dropdown-item" value="'.str_replace(' ', '_', $_SESSION['gps'][$i]).'">'.$gp.' ('.$date.')</option>';
                  }
                }
              ?>
            </select>
            </ul>
          </div>
        </div>
        <div class="modal-body">
          <!-- -->
          <div class="container">
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
                          <input type="number" name="'.str_replace(" ", "_", $stables[$i]['nome_scuderia']).'-input" class="loadscore-input">
                        </div>
                        
                      </div>
                    </div>';
                  }
                ?>
                </div>  <!-- /stables col -->
              </div> <!-- /row -->
              
          </div>
          <!-- / -->
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <input type="submit" id="btn-modal" name="btn-load" class="btn btn-primary" value="Carica">
        </div>
      
    </div>
    </form>
  </div>
</div>

<?php
  if(isset($_POST['btn-load'])) {
    $gp = explode("-", $_POST['gp'])[0];
    $date = explode("-", $_POST['gp'])[1];
    $drivers_scores = [];
    $stables_scores = [];

    for($i = 0; $i < count($drivers); $i++) {
      array_push($drivers_scores, $_POST[$drivers[$i]['cognome_pilota'].'-input']);
    }

    for($i = 0; $i < count($stables); $i++) {
      array_push($stables_scores, $_POST[str_replace(" ", "_", $stables[$i]['nome_scuderia']).'-input']);
    }

    $load_score_obj = new stdClass();
    $load_score_obj->gp = $gp.'-'.$date;
    $load_score_obj->drivers_score = $drivers_scores;
    $load_score_obj->stables_score = $stables_scores;
    
    echo '<script>loadScore('.json_encode($load_score_obj).')</script>';
  }

?>

<script>
var myModal = document.getElementById('loadscore-modal')
var myInput = document.getElementById('btn-modal')

console.log(myModal);

myModal.addEventListener('shown.bs.modal', function () {
  myInput.focus()
})
</script>

<!-- Navbar -->

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
                <a class="nav-link" href="#" onclick="loadScore();">Load Score</a>
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

<?php
  if(isset($_POST['log-out'])){
    unset($_SESSION['id_utente']);
    header("Location: ./index.php");
  }
 ?>

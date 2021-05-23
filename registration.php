<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <?php
    include __DIR__."/lib/mysql.php";

    session_start();
    /*if(isset($_SESSION["id_utente"]))
        header("location:./home.php");*/
    ?>
    <meta charset="utf-8">
    <title>Registration</title>
  </head>
  <body>
    <div class="container">
      <h1>Registration</h1>

      <form method="post" class="inner-container">
        <input type="text" name="name" placeholder="Nome">
        <input type="text" name="surname" placeholder="Cognome">
        <input type="date" name="birth_date" placeholder="Data di nascita">
        <input type="text" name="username" placeholder="Username">
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <input type="submit" name="register" value="Registrati">
      </form>
    </div>

    <?php
    if(!empty($_POST['name']) && !empty($_POST['surname']) &&
    !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email'])){
      $name = $_POST['name'];
      $surname = $_POST['surname'];
      $username = $_POST['username'];
      $email = $_POST['email'];
      $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $date = $_POST['birth_date'];

      $stmt = $db->prepare('INSERT INTO `t_utenti`(`username`, `nome_utente`, `cognome_utente`, `email_utente`, `password_utente`, `datanascita_utente`) VALUES (?, ?, ?, ?, ?, ?)');
      $stmt->bind_param("ssssss", $username, $name, $surname, $email, $hashed_password, $date);
      $result = $stmt->execute();

      if($result == 0){
        echo "no ok";
      }else{
        $_SESSION['id_utente'] = $row['id_utente'];
        echo $_SESSION['id_utente'];
        //header('Location: ./home.php');
      }
    }
    ?>
  </body>
</html>

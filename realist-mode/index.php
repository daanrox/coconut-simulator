<?php
session_start();
require '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: /logout');
    exit;
}

$user_name = $_SESSION['username'];

$stmt = $pdo->prepare('SELECT game_points FROM users WHERE username = :username');
$stmt->execute(['username' => $user_name]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: /logout');
    exit;
}

$gamePoints = $user['game_points']; 

?>




<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Coconut Simulator</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: "Roboto", serif;
      margin: 0;
     background-color: black;
    }

    canvas {
      display: block;
    }

    #overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('../assets/background.webp') no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      z-index: 10;
    }

    .playButton {
      padding: 15px 30px;
        width: 60%;
        max-width: 450px;
      font-size: 20px;
      font-weight: bold;
      background-color: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    .playButton:hover {
      background-color: rgba(255, 255, 255, 1);
    }
    
     #points {
      position: fixed;
      z-index: 9999;
      left: 85%;
      top: 5%;
      background-color: green;
      padding: 5px 25px;
      font-size: 28px;
      justify-content: center;
      align-items: center;
      border-radius: 18px;
      color: white;
    }
     #voltar {
      position: fixed;
      z-index: 9999;
      right: 85%;
      top: 5%;
      background-color: gray;
      padding: 5px 25px;
      font-size: 28px;
      justify-content: center;
      align-items: center;
      border-radius: 18px;
      color: white;
    }
    
    @media screen and (max-width: 500px){
        #points {
    
      left: 55%;
      top: 5%;
      background-color: green;
      padding: 5px 25px;
      font-size: 20px;
      
    }
   #voltar {

      right: 55%;
      top: 5%;
      background-color: gray;
      padding: 5px 25px;
      font-size: 20px;
      
    }
    }
  </style>
 
</head>
<body style="">

 

 

<div id="pointsContainer" style="display: flex; flex-direction: column; gap:8px; align-items: center; justify-content: space-between; width: 100% z-index:9999">
    <p id="points"><?php echo $gamePoints; ?> PONTOS</p> 
    <p onclick="window.location.href='/game'"  id="voltar" style="cursor: pointer;">VOLTAR</p> 
</div>



  <script>
    let points = <?php echo $gamePoints; ?>;
    const pointsElement = document.getElementById("points");

    function updatePointsDisplay() {
      pointsElement.textContent = `${points} PONTOS`;
    }


    setInterval(() => {
      points++;
      updatePointsDisplay();
    }, 3000);

    setInterval(() => {
      fetch('update_points.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ points })
      })
      .then(response => response.json())
      .then(data => console.log(data.message))
      .catch(error => console.error('Erro ao atualizar pontos:', error));
    }, 10000);
  </script>

  
  <script>
      document.getElementById('playButton').addEventListener('click', function() {
      document.getElementById('overlay').style.display = 'none';
      document.getElementById('pointsContainer').style.display = 'flex';
      audio.play();
    });
  </script>
</body>
</html>

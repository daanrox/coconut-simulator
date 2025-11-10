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

$stmt = $pdo->query('SELECT username, game_points FROM users ORDER BY game_points DESC');
$allUsers = $stmt->fetchAll();

$userPosition = 0;
foreach ($allUsers as $index => $userData) {
    if ($userData['username'] === $user_name) {
        $userPosition = $index + 1;
        break;
    }
}

$playersToShow = 10;
$topPlayers = array_slice($allUsers, 0, $playersToShow - 1);

$showUserAtBottom = true;
foreach ($topPlayers as $player) {
    if ($player['username'] === $user_name) {
        $showUserAtBottom = false;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Ranking - Coconut Simulator</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: "Roboto", serif;
      background: url('../assets/background.webp') no-repeat center center/cover;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      padding: 20px;
      box-sizing: border-box;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 30px;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
      font-size: 2.5em;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .leaderboard-header {
      display: grid;
      grid-template-columns: 80px 1fr 120px;
      gap: 15px;
      padding: 15px 20px;
      background-color: #34495e;
      color: white;
      border-radius: 10px 10px 0 0;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .player-row {
      display: grid;
      grid-template-columns: 80px 1fr 120px;
      gap: 15px;
      padding: 15px 20px;
      background-color: white;
      margin-bottom: 8px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      align-items: center;
      transition: transform 0.2s ease;
    }

    .player-row:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .current-user {
      background-color: #e8f4fd;
      border: 2px solid #3498db;
    }

    .ranking-position {
      font-weight: bold;
      font-size: 1.2em;
      text-align: center;
    }

    .player-name {
      font-size: 1.1em;
      color: #2c3e50;
    }

    .player-points {
      font-weight: bold;
      text-align: right;
      color: #27ae60;
      font-size: 1.1em;
    }

    .trophy {
      font-size: 1.5em;
      text-align: center;
    }

    .gold { color: #ffd700; }
    .silver { color: #c0c0c0; }
    .bronze { color: #cd7f32; }

    .separator {
      height: 2px;
      background: linear-gradient(90deg, transparent, #34495e, transparent);
      margin: 20px 0;
    }

    .info-text {
      text-align: center;
      color: #7f8c8d;
      margin: 15px 0;
      font-style: italic;
    }

    .back-button {
      padding: 15px 30px;
      width: 100%;
      max-width: 300px;
      font-size: 18px;
      font-weight: bold;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 20px;
    }

    .back-button:hover {
      background-color: #2980b9;
      transform: translateY(-2px);
    }

    @media screen and (max-width: 500px) {
      .container {
        padding: 20px 15px;
      }
      
      .leaderboard-header,
      .player-row {
        grid-template-columns: 50px 1fr 80px;
        gap: 10px;
        padding: 12px 15px;
      }
      
      h1 {
        font-size: 2em;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🏆 RANKING</h1>
    
    <div class="info-text">Exibindo <?php echo $playersToShow; ?> jogadores</div>
    
    <div class="leaderboard-header">
      <div>POSIÇÃO</div>
      <div>JOGADOR</div>
      <div>PONTOS</div>
    </div>

    <?php foreach ($topPlayers as $index => $player): ?>
      <div class="player-row <?php echo $player['username'] === $user_name ? 'current-user' : ''; ?>">
        <div class="ranking-position">
          <?php if ($index === 0): ?>
            <span class="trophy gold">🥇</span>
          <?php elseif ($index === 1): ?>
            <span class="trophy silver">🥈</span>
          <?php elseif ($index === 2): ?>
            <span class="trophy bronze">🥉</span>
          <?php else: ?>
            #<?php echo $index + 1; ?>
          <?php endif; ?>
        </div>
        <div class="player-name">
          <?php echo htmlspecialchars($player['username']); ?>
          <?php if ($player['username'] === $user_name): ?>
            <span style="color: #3498db; font-size: 0.8em;">(Você)</span>
          <?php endif; ?>
        </div>
        <div class="player-points"><?php echo number_format($player['game_points']); ?></div>
      </div>
    <?php endforeach; ?>

    <?php if ($showUserAtBottom && $userPosition > $playersToShow - 1): ?>
      <div class="separator"></div>
      
      <div class="player-row current-user">
        <div class="ranking-position">#<?php echo $userPosition; ?></div>
        <div class="player-name">
          <?php echo htmlspecialchars($user_name); ?>
          <span style="color: #3498db; font-size: 0.8em;">(Você)</span>
        </div>
        <div class="player-points"><?php echo number_format($gamePoints); ?></div>
      </div>
    <?php endif; ?>
  </div>

  <button class="back-button" onclick="window.location.href='/game'">VOLTAR AO JOGO</button>
</body>
</html>
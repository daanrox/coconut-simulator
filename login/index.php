<?php
session_start();
require '../db.php'; 

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];


    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();


    if ($user) {
        header('Location: /game');
        exit;
    } else {

        unset($_SESSION['username']); 
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['username'] = $user['username']; 
        header('Location: /game'); 
        exit;
    } else {
        $error = "Email ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/three@latest/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: "Roboto", serif;
            overflow: hidden;
            touch-action: none;
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

        #formContainer {
            background-color: rgba(255, 255, 255, 0.8);
            width: 80%;
            max-width: 550px;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        input {
            margin: 10px 0;
            padding: 10px;
            width: 90%;
            font-size: 16px;
            border-radius: 5px;
        }

        button {
            padding: 15px 30px;
            font-size: 20px;
            font-weight: bold;
            background-color: rgba(0, 100, 200, 0.7);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        button:hover {
            background-color: rgba(0, 100, 200, 1);
        }

        p {
            color: red;
            font-size: 14px;
        }

        .redirect-button {
            color: rgba(0, 0, 0, 0.8);
            border: none;
            cursor: pointer;
            margin-top: 18px;
            font-size: 18px;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div id="overlay">
        <div id="formContainer">
            <h2>Login</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Senha" required><br>
                <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
                <button type="submit">Entrar</button>
            </form>
            <p class="redirect-button" onclick="window.location.href='/register'">Não tem conta? Cadastre-se</p>
        </div>
        <p style="color: black; font-size: 18px; position: absolute; bottom: 20px;">Desenvolvido por <a style="text-decoration: none; color: purple;" href="https://daanrox.com/" target="_blank">DAANROX</a></p>
    </div>

</body>
</html>

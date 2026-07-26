<?php
session_start();
require 'db.php'; // On appelle la base de données en ligne configurée dans db.php

$erreur = "";

if (isset($_POST['username']) && isset($_POST['password'])) {


if (isset($_POST['username']) && isset($_POST['password'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = htmlspecialchars($_POST['username']);
        
        $check = $bdd->prepare("SELECT * FROM utilisateurs WHERE identifiant = ?");
        $check->execute([$username]);
        $user = $check->fetch();

        if ($user && password_verify($_POST['password'], $user['mot_de_passe'])) {
            $_SESSION['utilisateur'] = $user['identifiant'];
            header('Location: index.php');
            exit();
        } else {
            $erreur = "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion de Stock</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; margin: 20px; }
        h3 { color: #21475F; margin-bottom: 25px; font-size: 24px; font-weight: 600; }
        .input-group { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 6px; color: #5a6b7c; font-size: 14px; }
        input { width: 100%; padding: 12px; border: 1px solid #ccd4dc; border-radius: 6px; font-size: 15px; transition: all 0.3s; }
        input:focus { border-color: #21475F; outline: none; box-shadow: 0 0 0 3px rgba(33, 71, 95, 0.1); }
        button { width: 100%; padding: 14px; background-color: #21475F; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.3s; margin-top: 10px; }
        button:hover { background-color: #163041; }
        .error { color: #dc3545; background-color: #ffeef0; padding: 10px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; border: 1px solid #fecdd3; }
        .link { margin-top: 20px; font-size: 14px; color: #64748b; }
        .link a { color: #21475F; text-decoration: none; font-weight: 600; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-card">
    <h3>Se connecter</h3>

    <?php if(!empty($erreur)): ?>
        <div class="error"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Identifiant ou Email</label>
            <input type="text" name="username" placeholder="Ex: rouguiyatou" required>
        </div>
        <div class="input-group">
            <label>Mot de passe</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit">Connexion</button>
    </form>

    <div class="link">
        Nouveau ici ? <a href="inscription.php">Créer un compte</a>
    </div>
</div>

</body>
</html>


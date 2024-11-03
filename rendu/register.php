<?php
include 'bdd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $bdd->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    $role = ($userCount < 2) ? 'admin' : 'user';

    $stmt = $bdd->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute(['username' => $username, 'password' => $password, 'role' => $role]);

    header('Location: login.php');
    exit();
    
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <div class="container">
        <h2>S'inscrire sur Collegram</h2>
        <form method="POST" action="">
        <input type="text" name="username" required placeholder="Nom d'utilisateur">
        <input type="password" name="password" required placeholder="Mot de passe">
        <button type="submit">S'inscrire</button>
        </form>
        <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>

    </div>
</body>
</html>
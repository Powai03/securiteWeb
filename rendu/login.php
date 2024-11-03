<?php
include 'bdd.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $bdd->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: index.php");
    } else {
        echo "Nom d'utilisateur ou mot de passe incorrect.";
    }
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
        <h2>Se connecter</h2>
        <form method="POST" action="">
            <input type="text" name="username" required placeholder="Nom d'utilisateur">
            <input type="password" name="password" required placeholder="Mot de passe">
            <button type="submit">Se connecter</button>
            <?php if (isset($error_message)): ?>
                <div class="error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
        </form>
    </div>
</body>
</html>
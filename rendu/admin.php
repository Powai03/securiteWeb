<?php
include 'bdd.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete_user']) && isset($_GET['csrf_token']) && $_GET['csrf_token'] === $_SESSION['csrf_token']) {
    $userId = $_GET['delete_user'];
    $stmt = $bdd->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    header("Location: admin.php");
    exit();
} elseif (isset($_GET['delete_user'])) {
    echo "Erreur : token CSRF invalide.";
}

if (isset($_POST['change_role']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];
    $stmt = $bdd->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute(['role' => $newRole, 'id' => $userId]);
    echo "Le rôle de l'utilisateur a été modifié avec succès.";
} elseif (isset($_POST['change_role'])) {
    echo "Erreur : token CSRF invalide.";
}

$users = $bdd->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="admins.css"> 
</head>
<body>
<header>
    <h1>Dashboard Admin</h1>
    <a href="index.php">Retour à l'accueil</a>
    <a href="logout.php">Déconnexion</a> 
</header>

<h2>Gérer les utilisateurs</h2>
<table>
    <thead>
        <tr>
            <th>Nom d'utilisateur</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <select name="role">
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit" name="change_role">Changer le rôle</button>
                    </form>
                    <a href="?delete_user=<?= $user['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" onclick="return confirm('Supprimer cet utilisateur ?');" class="delete">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    
</body>
</html>

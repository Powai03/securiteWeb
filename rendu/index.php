<?php
include 'bdd.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['delete_post']) && isset($_GET['csrf_token']) && $_SESSION['role'] === 'admin') {
    if ($_GET['csrf_token'] === $_SESSION['csrf_token']) {
        $postId = $_GET['delete_post'];
        $stmt = $bdd->prepare("DELETE FROM posts WHERE id = :id");
        $stmt->execute(['id' => $postId]);
        header("Location: index.php");
    } else {
        echo "Erreur : token CSRF invalide.";
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image'];
    $caption = htmlspecialchars($_POST['caption']);
    $imagePath = 'uploads/' . basename($image['name']);
    move_uploaded_file($image['tmp_name'], $imagePath);

    $stmt = $bdd->prepare("INSERT INTO posts (user_id, image_path, caption) VALUES (:user_id, :image_path, :caption)");
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'image_path' => $imagePath, 'caption' => $caption]);
}

$posts = $bdd->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Collegram</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Collegram</h1>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin.php">Dashboard Admin</a>
        <?php endif; ?>
        <a href="logout.php">Déconnexion</a>
    </header>

    <h2>Publier une photo</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="image" accept=".jpg, .jpeg, .png, .webp" required><button type="button" id="removeImage">Enlever l'image</button>
        <input type="text" name="caption" placeholder="Légende">
        <button type="submit">Publier</button>
        <script>
            document.getElementById('removeImage').addEventListener('click', function() {
            document.querySelector('input[name="image"]').value = '';
            });
        </script>
    </form>

    <h2>Fil d'actualités</h2>
    <?php foreach ($posts as $post): ?>
        <div style="margin-bottom: 20px;">
            <p><strong><?= htmlspecialchars($post['username']) ?></strong> </p>
            <img src="<?= htmlspecialchars($post['image_path']) ?>" width="200px">
            <br>
            <?= htmlspecialchars($post['caption']) ?>
            <br>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="?delete_post=<?= $post['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" onclick="return confirm('Supprimer ce post ?');">Supprimer</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>

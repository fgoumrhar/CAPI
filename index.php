<?php
session_start(); // Démarre la session

// Connexion à la base de données
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les noms des joueurs à partir du formulaire
    $players = explode(',', $_POST['players']);
    
    foreach ($players as $player) {
        $name = trim($player);
        if (!empty($name)) {
            // Insérer chaque joueur dans la base de données
            $stmt = $pdo->prepare("INSERT INTO players (name) VALUES (:name)");
            $stmt->execute(['name' => $name]);
            
            // Récupérer l'ID du joueur inséré
            $player_id = $pdo->lastInsertId();
            
            // Générer un `session_id` unique pour chaque joueur
            $session_id = bin2hex(random_bytes(16));

            // Insérer la session dans la table `sessions`
            $stmt = $pdo->prepare("INSERT INTO sessions (session_id, player_id) VALUES (:session_id, :player_id)");
            $stmt->execute(['session_id' => $session_id, 'player_id' => $player_id]);
        }
    }
    
    // Rediriger vers la page de jeu après l'inscription des joueurs
    header("Location: game.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription des joueurs</title>
</head>
<body>
    <h1>Inscription des joueurs</h1>
    <form method="POST">
        <label for="players">Entrez les noms des joueurs (séparés par des virgules) :</label><br>
        <input type="text" name="players" id="players" placeholder="Exemple : Alice, Bob, Charlie" required>
        <br><br>
        <button type="submit">Commencer le jeu</button>
    </form>
</body>
</html>

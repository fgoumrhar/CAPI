<?php
session_start(); // Démarre la session

include 'db.php'; // Connexion à la base de données

// Vérifie si session_id existe dans la session
if (!isset($_SESSION['session_id'])) {
    echo "Session invalide.";
    exit;
}

// Récupère le session_id depuis la session
$session_id = $_SESSION['session_id'];

// Récupérer les joueurs inscrits pour cette session
$stmt = $pdo->prepare("SELECT players.name FROM players 
                       JOIN sessions ON players.id = sessions.player_id
                       WHERE sessions.session_id = :session_id");
$stmt->execute(['session_id' => $session_id]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage des joueurs inscrits
echo "Joueurs inscrits :<br>";
foreach ($players as $player) {
    echo $player['name'] . "<br>";
}

// Récupérer le nombre total de joueurs dans la session
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE session_id = :session_id");
$stmt->execute(['session_id' => $session_id]);
$totalPlayers = $stmt->fetchColumn(); // Nombre de joueurs inscrits dans la session

// Récupérer le nombre de votes pour la session
$stmt_votes = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE player_id IN 
                             (SELECT player_id FROM sessions WHERE session_id = :session_id)");
$stmt_votes->execute(['session_id' => $session_id]);
$totalVotes = $stmt_votes->fetchColumn(); // Nombre de votes enregistrés pour cette session

echo "Total Votes: $totalVotes, Total Players: $totalPlayers<br>";

// Vérification des votes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $player_id = 1; // Exemple pour le joueur actuel, tu devrais le récupérer depuis la session
    $card_id = intval($_POST['vote']);

    // Enregistrement du vote
    $stmt = $pdo->prepare("INSERT INTO votes (player_id, card_id) VALUES (:player_id, :card_id) 
                          ON DUPLICATE KEY UPDATE card_id = :card_id");
    $stmt->execute(['player_id' => $player_id, 'card_id' => $card_id]);

    // Vérification si tous les joueurs ont voté
    if ($totalVotes === $totalPlayers) {
        // Si tous les joueurs ont voté, calcul de l'unanimité ou de la moyenne
        $unanimityCheck = $pdo->query("SELECT card_id, COUNT(*) as count FROM votes GROUP BY card_id");
        $votesArray = $unanimityCheck->fetchAll(PDO::FETCH_ASSOC);

        if (count($votesArray) === 1) {
            // Unanimité atteinte
            $resultMessage = "Unanimité atteinte ! Valeur retenue : " . $votesArray[0]['card_id'];
        } else {
            // Calcul de la moyenne des votes
            $avgQuery = $pdo->query("SELECT AVG(cards.value) as average FROM votes JOIN cards ON votes.card_id = cards.id");
            $average = round($avgQuery->fetchColumn());
            $resultMessage = "Unanimité non atteinte, moyenne des votes : $average";
        }

        echo $resultMessage;
    } else {
        echo "Veuillez attendre que tous les joueurs aient voté.";
    }
}
?>

<?php
include 'db.php'; // Connexion à la base de données

// Supposons que $player_id est récupéré dynamiquement selon la session ou l'utilisateur connecté
$player_id = 1; // Exemple pour le joueur Alice, cela devrait être dynamique selon la session de l'utilisateur

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $card_id = intval($_POST['vote']);

    // Enregistrement ou mise à jour du vote du joueur
    $stmt = $pdo->prepare("INSERT INTO votes (player_id, card_id) VALUES (:player_id, :card_id) 
                          ON DUPLICATE KEY UPDATE card_id = :card_id");
    $stmt->execute(['player_id' => $player_id, 'card_id' => $card_id]);

    // Vérification du nombre de joueurs inscrits
    $totalPlayers = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn(); // Nombre total de joueurs inscrits

    // Vérification du nombre de votes déjà enregistrés
    $totalVotes = $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn(); // Nombre total de votes

    // Debugging - afficher les valeurs pour vérifier
    echo "Total Votes: $totalVotes, Total Players: $totalPlayers<br>";

    // Vérifie que tous les joueurs inscrits ont voté
    if ($totalVotes === $totalPlayers) {
        // Tous les joueurs inscrits ont voté, maintenant on calcule la moyenne ou l'unanimité

        // Vérification de l'unanimité
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

        // Réinitialiser les votes pour la prochaine fonctionnalité (si nécessaire)
        // $pdo->query("TRUNCATE TABLE votes");

        // Affichage du message résultat
        echo $resultMessage;

        // Si le vote est "café" pour tous les joueurs
        $cafeVotes = $pdo->query("SELECT COUNT(*) FROM votes WHERE card_id = 33")->fetchColumn();
        if ($totalVotes === $totalPlayers && $cafeVotes === $totalPlayers) {
            $backlogData = ['message' => 'Pause café, état sauvegardé'];
            file_put_contents('backlog.json', json_encode($backlogData));
            echo "État sauvegardé dans backlog.json";
        }
    } else {
        // Si tous les joueurs inscrits n'ont pas encore voté, afficher un message
        echo "Veuillez attendre que tous les joueurs aient voté.";
    }
}
?>

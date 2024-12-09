<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Informations de connexion à la base de données
$host = 'localhost';
$db = 'game_project';
$user = 'root'; // Remplace par ton utilisateur MySQL
$password = ''; // Mets ton mot de passe MySQL ici

// Connexion avec PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion PDO : " . $e->getMessage());
}
?>

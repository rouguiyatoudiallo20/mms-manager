<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $sql = "DELETE FROM produits_cosmetique WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // En cas d'erreur, on peut la gérer ici
    }
}

// Redirection automatique vers l'accueil après suppression
header("Location: index.php");
exit();
?>


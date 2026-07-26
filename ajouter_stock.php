<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Augmente la quantité en stock de 1
        $sql = "UPDATE produits_cosmetique SET quantite_stock = quantite_stock + 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Erreur silencieuse
    }
}

header("Location: index.php");
exit();
?>


<?php
require 'db.php'; // Utilise votre fichier de connexion existant

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // 1. Récupérer les informations du produit
        $stmtDetails = $pdo->prepare("SELECT nom, prix_vente, prix_achat, quantite_stock FROM produits_cosmetique WHERE id = ?");
        $stmtDetails->execute([$id]);
        $produit = $stmtDetails->fetch();

        // Vérification du stock
        if ($produit && $produit['quantite_stock'] > 0) {
            
            // Calcul du bénéfice réalisé sur cette vente
            $benefice = $produit['prix_vente'] - $produit['prix_achat'];

            // Début de la transaction sécurisée
            $pdo->beginTransaction();

            // ACTION A : Diminuer la quantité en stock et augmenter la quantité vendue
            $sqlUpdate = "UPDATE produits_cosmetique SET quantite_stock = quantite_stock - 1, quantite_vendue = quantite_vendue + 1 WHERE id = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$id]);

            // ACTION B : Créer la vente globale dans l'historique
            $sqlVente = "INSERT INTO ventes (montant_total, benefice_total) VALUES (?, ?)";
            $stmtVente = $pdo->prepare($sqlVente);
            $stmtVente->execute([$produit['prix_vente'], $benefice]);
            
            // Récupérer l'ID de cette vente fraîchement créée
            $id_vente = $pdo->lastInsertId();

            // ACTION C : Lier le produit précis à cette vente
            $sqlDetail = "INSERT INTO details_ventes (id_vente, nom_produit, quantite, prix_unitaire) VALUES (?, ?, 1, ?)";
            $stmtDetail = $pdo->prepare($sqlDetail);
            $stmtDetail->execute([$id_vente, $produit['nom'], $produit['prix_vente']]);

            // Validation finale des données
            $pdo->commit();
        }
    } catch (PDOException $e) {
        // En cas de problème informatique, on annule pour ne pas fausser vos stocks
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}

// Redirection vers votre tableau de bord
header("Location: index.php");
exit();
?>


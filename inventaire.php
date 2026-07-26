<?php
require 'db.php';
$message = "";

// 1. Enregistrement en masse des nouvelles quantités de l'inventaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['stocks'])) {
    try {
        $pdo->beginTransaction();
        $sql = "UPDATE produits_cosmetique SET quantite_stock = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        foreach ($_POST['stocks'] as $id => $quantite) {
            $stmt->execute([intval($quantite), intval($id)]);
        }
        
        $pdo->commit();
        $message = "<div class='badge bg-success'>L'inventaire a été mis à jour avec succès !</div>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "<div class='badge bg-danger'>Erreur lors de l'inventaire : " . $e->getMessage() . "</div>";
    }
}

// 2. Récupération des produits pour affichage et calculs
try {
    $sql = "SELECT * FROM produits_cosmetique ORDER BY nom ASC";
    $stmt = $pdo->query($sql);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// 3. Calcul automatique de la valeur globale du stock actuel
$valeur_totale_achat = 0;
$valeur_totale_vente = 0;
foreach ($produits as $produit) {
    $valeur_totale_achat += ($produit['quantite_stock'] * $produit['prix_achat']);
    $valeur_totale_vente += ($produit['quantite_stock'] * $produit['prix_vente']);
}
$benefice_potentiel = $valeur_totale_vente - $valeur_totale_achat;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Faire l'inventaire</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0; border: none;">Inventaire Physique</h1>
            <a href="index.php" class="btn btn-secondary">Retour au stock</a>
        </div>

        <!-- Blocs de résumé financier de l'inventaire -->
        <?php if (count($produits) > 0): ?>
        <div style="display: flex; gap: 15px; margin-bottom: 25px;">
            <div style="flex: 1; background: #212529; color: white; padding: 15px; border-radius: 6px;">
                <small style="opacity: 0.8; display: block; text-transform: uppercase; font-size: 11px; font-weight: bold;">Valeur au prix d'achat</small>
                <span style="font-size: 20px; font-weight: bold;"><?php echo number_format($valeur_totale_achat, 0, ',', ' '); ?> FCFA</span>
            </div>
            <div style="flex: 1; background: #0d6efd; color: white; padding: 15px; border-radius: 6px;">
                <small style="opacity: 0.8; display: block; text-transform: uppercase; font-size: 11px; font-weight: bold;">Valeur au prix de vente</small>
                <span style="font-size: 20px; font-weight: bold;"><?php echo number_format($valeur_totale_vente, 0, ',', ' '); ?> FCFA</span>
            </div>
            <div style="flex: 1; background: #198754; color: white; padding: 15px; border-radius: 6px;">
                <small style="opacity: 0.8; display: block; text-transform: uppercase; font-size: 11px; font-weight: bold;">Bénéfice en stock</small>
                <span style="font-size: 20px; font-weight: bold;"><?php echo number_format($benefice_potentiel, 0, ',', ' '); ?> FCFA</span>
            </div>
        </div>
        <?php endif; ?>

        <p style="color: #6c757d; margin-bottom: 20px;">Comptez les produits en magasin et ajustez les quantités réelles lues ci-dessous :</p>
        
        <?php echo $message; ?>

        <form action="inventaire.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Nom du produit</th>
                        <th>Prix Achat</th>
                        <th>Prix Vente</th>
                        <th style="width: 150px; text-align: center;">Quantité réelle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produits) > 0): ?>
                        <?php foreach ($produits as $produit): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($produit['nom']); ?></strong></td>
                                <td><?php echo number_format($produit['prix_achat'], 0, ',', ' '); ?> FCFA</td>
                                <td><?php echo number_format($produit['prix_vente'], 0, ',', ' '); ?> FCFA</td>
                                <td>
                                    <input type="number" name="stocks[<?php echo $produit['id']; ?>]" 
                                           class="form-control" 
                                           value="<?php echo htmlspecialchars($produit['quantite_stock']); ?>" 
                                           min="0" required style="padding: 6px; text-align: center; font-weight: bold;">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">Aucun produit à inventorier.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if (count($produits) > 0): ?>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn btn-success" style="font-size: 16px; padding: 10px 20px;">💾 Clôturer et enregistrer l'inventaire</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>


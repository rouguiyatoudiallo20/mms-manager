
<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

require 'db.php';
?>
try {
    $sql = "SELECT * FROM produits_cosmetique ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}

// Calculs des totaux globaux
$valeur_totale_achat = 0;
$valeur_totale_vente = 0;
$total_articles_vendus = 0; // Nouveau compteur global

foreach ($produits as $produit) {
    $valeur_totale_achat += ($produit['quantite_stock'] * $produit['prix_achat']);
    $valeur_totale_vente += ($produit['quantite_stock'] * $produit['prix_vente']);
    $total_articles_vendus += $produit['quantite_vendue']; // Cumul des ventes
}
$benefice_total_stock = $valeur_totale_vente - $valeur_totale_achat;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Stocks</title>
    <link rel="stylesheet" href="bootstrap.css">
    <script src="https://unpkg.com"></script>
</head>
<body>
    <div class="container" style="max-width: 1100px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0; border: none;">Stock</h1>
            <div style="display: flex; gap: 10px;">
    <a href="inventaire.php" class="btn btn-secondary">📋 Faire l'inventaire</a>
    <a href="historique.php" class="btn btn-warning" style="background-color: #ffc107 !important; color: black !important; border-color: #ffc107 !important;">&#128220; Historique des ventes</a>

    <a href="ajouter.php" class="btn btn-primary">➕ Ajouter un produit</a>
</div>

        </div>

        <!-- Blocs de Résumé Financier -->
        <?php if (count($produits) > 0): ?>
        <div style="display: flex; gap: 15px; margin-bottom: 25px;">
            <div style="flex: 1; background: #212529; color: white; padding: 15px; border-radius: 6px;">
                <small style="opacity: 0.8; display: block; text-transform: uppercase; font-size: 11px; font-weight: bold;">Investissement Total</small>
                <span style="font-size: 20px; font-weight: bold;"><?php echo number_format($valeur_totale_achat, 0, ',', ' '); ?> FCFA</span>
            </div>
            <div style="flex: 1; background: #0d6efd; color: white; padding: 15px; border-radius: 6px;">
                <small style="opacity: 0.8; display: block; text-transform: uppercase; font-size: 11px; font-weight: bold;">Bénéfice Stock Estimé</small>
                <span style="font-size: 20px; font-weight: bold;"><?php echo number_format($benefice_total_stock, 0, ',', ' '); ?> FCFA</span>
            </div>
            <!-- Nouveau bloc pour le total des ventes -->
            <div style="flex: 1; background: #ffc107; color: #212529; padding: 15px; border-radius: 6px;">
                <small style="opacity: 0.8; display: block; text-transform: uppercase; font-size: 11px; font-weight: bold;">Total Produits Vendus</small>
                <span style="font-size: 20px; font-weight: bold;"><?php echo $total_articles_vendus; ?> articles</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Barre de recherche -->
        <div class="form-group" style="margin-bottom: 20px;">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Rechercher un produit cosmétique...">
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nom du produit</th>
                    <th>Prix Achat</th>
                    <th>Prix Vente</th>
                    <th>Bénéfice/U</th>
                    <th>Stock</th>
                    <th style="color: #b58100;">Vendus</th> <!-- Nouvelle colonne -->
                    <th>Statut</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody id="stockTable">
                <?php if (count($produits) > 0): ?>
                    <?php foreach ($produits as $produit): 
                        $benefice_unitaire = $produit['prix_vente'] - $produit['prix_achat'];
                    ?>
                        <tr>
                            <td class="product-name"><strong><?php echo htmlspecialchars($produit['nom']); ?></strong></td>
                            <td><?php echo number_format($produit['prix_achat'], 0, ',', ' '); ?> F</td>
                            <td><?php echo number_format($produit['prix_vente'], 0, ',', ' '); ?> F</td>
                            <td class="text-success">+<?php echo number_format($benefice_unitaire, 0, ',', ' '); ?> F</td>
                            <td><strong><?php echo htmlspecialchars($produit['quantite_stock']); ?></strong></td>
                            <td style="font-weight: bold; color: #b58100;"><?php echo htmlspecialchars($produit['quantite_vendue']); ?></td> <!-- Affichage ventes -->
                            <td>
                                <?php if ($produit['quantite_stock'] == 0): ?>
                                    <span class="badge bg-danger">Rupture</span>
                                <?php elseif ($produit['quantite_stock'] <= 3): ?>
                                    <span class="badge" style="background-color: #ffc107; color: #212529;">Bas</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Disponible</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="ajouter_stock.php?id=<?php echo $produit['id']; ?>" class="btn btn-primary" style="padding: 4px 8px; font-size: 12px; margin-right: 5px;">+ Ajouter 1</a>
                                <?php if ($produit['quantite_stock'] > 0): ?>
                                    <a href="vendre.php?id=<?php echo $produit['id']; ?>" class="btn btn-success" style="padding: 4px 8px; font-size: 12px; margin-right: 5px;">⚡ Vendre 1</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px; margin-right: 5px; opacity: 0.5; cursor: not-allowed;" disabled>Épuisé</button>
                                <?php endif; ?>
                                
                                <a href="modifier.php?id=<?php echo $produit['id']; ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px; margin-right: 5px;">Modifier</a>
                                <a href="supprimer.php?id=<?php echo $produit['id']; ?>" class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #6c757d;">
                            Aucun produit en stock pour le moment.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    




</body>
</html>


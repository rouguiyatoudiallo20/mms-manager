<?php
require 'db.php'; // Connexion à votre base de données mms_manager

try {
    // Récupérer toutes les ventes enregistrées
    $stmt = $pdo->query("SELECT * FROM ventes ORDER BY date_vente DESC");
    $ventes = $stmt->fetchAll();

    // Calculer les totaux globaux de l'historique
    $totalCa = 0;
    $totalBenefice = 0;
    foreach ($ventes as $v) {
        $totalCa += $v['montant_total'];
        $totalBenefice += $v['benefice_total'];
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération de l'historique : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Ventes - Stock Cosmétique</title>
    <!-- On réutilise votre fichier CSS existant pour garder le même style -->
    <link rel="stylesheet" href="bootstrap.css"> 
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f6f9; }
        .kpi-container { display: flex; gap: 20px; margin-bottom: 25px; }
        .kpi-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex: 1; text-align: center; }
        .kpi-card h3 { margin: 0 0 10px 0; font-size: 14px; color: #666; text-transform: uppercase; }
        .kpi-card p { margin: 0; font-size: 24px; font-weight: bold; }
        .ca { color: #0275d8; }
        .benefice { color: #5cb85c; }
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .btn-retour { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background: #333; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <a href="index.php" class="btn-retour">⬅️ Retour à l'accueil</a>

    <h2>Historique Global des Ventes</h2>
    <br>

    <!-- Blocs d'indicateurs (KPIs) -->
    <div class="kpi-container">
        <div class="kpi-card">
            <h3>Chiffre d'Affaires Total</h3>
            <p class="ca"><?php echo number_format($totalCa, 0, ',', ' '); ?> FCFA</p>
        </div>
        <div class="kpi-card">
            <h3>Bénéfice Total Réalisé</h3>
            <p class="benefice"><?php echo number_format($totalBenefice, 0, ',', ' '); ?> FCFA</p>
        </div>
        <div class="kpi-card">
            <h3>Nombre de Ventes</h3>
            <p><?php echo count($ventes); ?> transactions</p>
        </div>
    </div>

    <!-- Tableau de l'historique -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Vente</th>
                    <th>Date & Heure</th>
                    <th>Montant Reçu</th>
                    <th>Bénéfice</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventes)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #888;">Aucune vente enregistrée pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ventes as $vente): ?>
                        <tr>
                            <td>#<?php echo $vente['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($vente['date_vente'])); ?></td>
                            <td style="font-weight: bold; color: #0275d8;"><?php echo number_format($vente['montant_total'], 0, ',', ' '); ?> F</td>
                            <td style="font-weight: bold; color: #5cb85c;">+<?php echo number_format($vente['benefice_total'], 0, ',', ' '); ?> F</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>


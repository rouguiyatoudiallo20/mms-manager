<?php
require 'db.php'; 
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prix_achat = floatval($_POST['prix_achat']);
    $prix_vente = floatval($_POST['prix_vente']);
    $quantite_stock = intval($_POST['quantite_stock']);

    if (!empty($nom) && $prix_achat >= 0 && $prix_vente >= 0 && $quantite_stock >= 0) {
        try {
            $sql = "INSERT INTO produits_cosmetique (nom, prix_achat, prix_vente, quantite_stock) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom, $prix_achat, $prix_vente, $quantite_stock]);
            $message = "<div class='badge bg-success'>Produit ajouté avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='badge bg-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='badge bg-danger'>Veuillez remplir tous les champs correctement.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Nouveau Produit</h1>
            <a href="index.php" class="btn btn-secondary">Retour au stock</a>
        </div>
        
        <?php echo $message; ?>

        <form action="ajouter.php" method="POST">
            <div class="form-group">
                <label class="form-label">Nom du produit</label>
                <input type="text" name="nom" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Prix d'achat (FCFA)</label>
                <input type="number" name="prix_achat" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Prix de vente (FCFA)</label>
                <input type="number" name="prix_vente" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Quantité</label>
                <input type="number" name="quantite_stock" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-success">Enregistrer</button>
        </form>
    </div>
</body>
</html>


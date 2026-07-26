<?php
require 'db.php';
$message = "";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM produits_cosmetique WHERE id = ?");
        $stmt->execute([$id]);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$produit) {
            die("Produit introuvable.");
        }
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prix_achat = floatval($_POST['prix_achat']);
    $prix_vente = floatval($_POST['prix_vente']);
    $quantite_stock = intval($_POST['quantite_stock']);

    if (!empty($nom) && $prix_achat >= 0 && $prix_vente >= 0 && $quantite_stock >= 0) {
        try {
            $sql = "UPDATE produits_cosmetique SET nom = ?, prix_achat = ?, prix_vente = ?, quantite_stock = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom, $prix_achat, $prix_vente, $quantite_stock, $id]);
            header("Location: index.php");
            exit();
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
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="margin: 0; border: none;">Modifier le produit</h1>
            <a href="index.php" class="btn btn-secondary">Retour au stock</a>
        </div>
        
        <?php echo $message; ?>

        <form action="modifier.php?id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label class="form-label">Nom du produit</label>
                <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Prix d'achat (FCFA)</label>
                <input type="number" name="prix_achat" class="form-control" value="<?php echo htmlspecialchars($produit['prix_achat']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Prix de vente (FCFA)</label>
                <input type="number" name="prix_vente" class="form-control" value="<?php echo htmlspecialchars($produit['prix_vente']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Quantité</label>
                <input type="number" name="quantite_stock" class="form-control" value="<?php echo htmlspecialchars($produit['quantite_stock']); ?>" required>
            </div>
            
            <button type="submit" class="btn btn-success">Mettre à jour</button>
        </form>
    </div>
</body>
</html>


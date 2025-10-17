<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion de la configuration de la base de données
require_once 'db.php';

// Fonction pour nettoyer la session
function clearGameSession() {
    if (isset($_SESSION['board'])) unset($_SESSION['board']);
    if (isset($_SESSION['num_flips'])) unset($_SESSION['num_flips']);
    if (isset($_SESSION['first_card'])) unset($_SESSION['first_card']);
}

// Si on arrive d'une ancienne session avec des données corrompues
if (isset($_SESSION['board']) && !is_array($_SESSION['board'])) {
    clearGameSession();
}
?>
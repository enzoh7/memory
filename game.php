<?php
require_once 'Card.php';
require_once 'config.php';
session_start();

// Réinitialiser la partie si demandé
if (isset($_GET['reset'])) {
    unset($_SESSION['board']);
    unset($_SESSION['num_flips']);
    unset($_SESSION['first_card']);
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

// Initialiser le compteur de flips s'il n'existe pas
if (!isset($_SESSION['num_flips'])) {
    $_SESSION['num_flips'] = 0;
}

// Nettoyage de la session si nécessaire
if (isset($_SESSION['board']) && !is_array($_SESSION['board'])) {
    unset($_SESSION['board']);
}

// Initialisation du jeu
if (isset($_GET['pairs'])) {
    // Force la réinitialisation du plateau
    $_SESSION['board'] = [];
    $numPairs = min(12, max(3, intval($_GET['pairs'])));
    
    // Liste des rappeurs
    $rappers = [
        ['id' => 'lafeve', 'image' => 'images/la fève.jpg'],
        ['id' => 'freeze', 'image' => 'images/freeze.png'],
        ['id' => 'sch', 'image' => 'images/Jvlivs.jpg'],
        ['id' => 'bushi', 'image' => 'images/bushi.jpg'],
        ['id' => 'mister', 'image' => 'images/mister you.jpg'],
        ['id' => 'resval', 'image' => 'images/luv resval.avif'],
        ['id' => 'damso', 'image' => 'images/damso.jpg'],
        ['id' => 'khali', 'image' => 'images/khali.jpg'],
        ['id' => 'luther', 'image' => 'images/Luther.png'],
        ['id' => 'vald', 'image' => 'images/Vald.jpg'],
        ['id' => 'kpri', 'image' => 'images/Kpri.jpg']
    ];

    // Sélection des paires selon le nombre choisi
    $selectedRappers = array_slice($rappers, 0, $numPairs);
    
    // Création des cartes (deux pour chaque rappeur)
    $cards = [];
    foreach ($selectedRappers as $rapper) {
        $cards[] = new Card($rapper['id'], $rapper['image']);
        $cards[] = new Card($rapper['id'], $rapper['image']);
    }
    
    // Mélange des cartes
    shuffle($cards);
    
    // Stockage en session
    $_SESSION['board'] = array_map(function($card) {
        return $card->toArray();
    }, $cards);
    $_SESSION['num_flips'] = 0;
    $_SESSION['first_card'] = null;
}

// Gestion du retournement des cartes
if (isset($_GET['flip']) && isset($_SESSION['board'])) {
    $index = intval($_GET['flip']);
    
    // Si c'est la première carte
    if ($_SESSION['first_card'] === null) {
        $_SESSION['board'][$index]->flip();
        $_SESSION['first_card'] = $index;
    } 
    // Si c'est la deuxième carte
    else {
        $firstIndex = $_SESSION['first_card'];
        $_SESSION['board'][$index]->flip();
        $_SESSION['num_flips']++;
        
        // Vérification de la paire
        if ($_SESSION['board'][$firstIndex]->getId() === $_SESSION['board'][$index]->getId()) {
            $_SESSION['board'][$firstIndex]->match();
            $_SESSION['board'][$index]->match();
        }
        
        $_SESSION['first_card'] = null;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Memory Rap FR - Jeu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Memory Rap FR</h1>
        <div class="game-info">
            <p>Nombre de flips : <?php echo $_SESSION['num_flips']; ?></p>
        </div>
        <div class="game-board">
            <?php
            if (isset($_SESSION['board'])) {
                foreach ($_SESSION['board'] as $index => $cardData) {
                    $card = Card::fromArray($cardData);
                    echo '<div class="card-container">';
                    $card->render($index);
                    echo '</div>';
                }
            } else {
                echo "<p>Aucune carte n'est initialisée. Retournez à l'accueil pour commencer une partie.</p>";
            }
            ?>
        </div>
        <div class="menu">
            <a href="index.php">Retour à l'accueil</a>
            <a href="game.php?reset=1">Réinitialiser la partie</a>
        </div>
    </div>
</body>
</html>
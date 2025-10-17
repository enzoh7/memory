<?php
require_once 'init.php';
require_once 'card.php';

// Initialisation d'une nouvelle partie
if (isset($_GET['pairs']) && !isset($_SESSION['board'])) {
    $numPairs = min(12, max(3, intval($_GET['pairs'])));
    $cardStates = [];
    
    // Liste des paires de cartes (chaque image en double)
    $cards = [];
    
    // Ajouter chaque image deux fois pour créer les paires
    $images = [
        ['id' => 'bushi', 'image' => 'images/bushi.jpg'],
        ['id' => 'damso', 'image' => 'images/damso.jpg'],
        ['id' => 'freeze', 'image' => 'images/freeze.png'],
        ['id' => 'sch', 'image' => 'images/Jvlivs.jpg'],
        ['id' => 'khali', 'image' => 'images/khali.jpg'],
        ['id' => 'kpri', 'image' => 'images/Kpri.jpg'],
        ['id' => 'lafeve', 'image' => 'images/la fève.jpg'],
        ['id' => 'luther', 'image' => 'images/Luther.png'],
        ['id' => 'resval', 'image' => 'images/luv resval.avif'],
        ['id' => 'mister', 'image' => 'images/mister you.jpg'],
        ['id' => 'vald', 'image' => 'images/Vald.jpg']
    ];

    // Sélectionner le nombre de paires demandé
    shuffle($images);
    $selectedImages = array_slice($images, 0, $numPairs);

    // Dupliquer chaque image pour créer les paires
    foreach ($selectedImages as $image) {
        // Ajouter deux fois la même image
        $cards[] = $image;
        $cards[] = $image;
    }

    // Mélanger toutes les cartes
    shuffle($cards);
    
    foreach ($cards as $card) {
        $newCard = new Card($card['id'], $card['image']);
        $cardStates[] = $newCard->getState();
    }
    
    // Mélanger les cartes
    shuffle($cardStates);
    
    // Sauvegarder dans la session
    $_SESSION['board'] = $cardStates;
    $_SESSION['num_flips'] = 0;
    $_SESSION['first_card'] = null;
}

// Réinitialisation si demandé
if (isset($_GET['reset'])) {
    clearGameSession();
    header('Location: index.php');
    exit;
}

// Gestion des clics sur les cartes
if (isset($_GET['flip']) && isset($_SESSION['board'])) {
    $index = intval($_GET['flip']);
    $card = Card::createFromState($_SESSION['board'][$index]);
    
    if ($_SESSION['first_card'] === null) {
        $card->flip();
        $_SESSION['first_card'] = $index;
        $_SESSION['board'][$index] = $card->getState();
    } else {
        $firstIndex = $_SESSION['first_card'];
        $firstCard = Card::createFromState($_SESSION['board'][$firstIndex]);
        
        $card->flip();
        $_SESSION['board'][$index] = $card->getState();
        $_SESSION['num_flips']++;
        
        if ($firstCard->getId() === $card->getId()) {
            $firstCard->match();
            $card->match();
            $_SESSION['board'][$firstIndex] = $firstCard->getState();
            $_SESSION['board'][$index] = $card->getState();
        } else {
            $firstCard->hide();
            $card->hide();
            $_SESSION['board'][$firstIndex] = $firstCard->getState();
            $_SESSION['board'][$index] = $card->getState();
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
            <p>Nombre de flips : <?php echo isset($_SESSION['num_flips']) ? $_SESSION['num_flips'] : 0; ?></p>
        </div>
        
        <div class="game-board">
            <?php
            if (isset($_SESSION['board'])) {
                foreach ($_SESSION['board'] as $index => $cardData) {
                    echo '<div class="card-container">';
                    $card = Card::createFromState($cardData);
                    $card->render($index);
                    echo '</div>';
                }
            } else {
                echo "<p>Sélectionnez le nombre de paires pour commencer une partie.</p>";
            }
            ?>
        </div>
        
        <?php if (!isset($_SESSION['board'])): ?>
            <div class="game-setup">
                <h2>Nouvelle partie</h2>
                <form action="index.php" method="get">
                    <div class="form-group">
                        <label for="pairs">Nombre de paires (3-12):</label>
                        <input type="number" id="pairs" name="pairs" min="3" max="12" required>
                    </div>
                    <button type="submit">Commencer la partie</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="menu-button">Mon profil</a>
                <a href="game.php?reset=1" class="menu-button">Nouvelle partie</a>
            <?php else: ?>
                <a href="auth.php" class="menu-button">Se connecter</a>
                <a href="auth.php#register" class="menu-button">Créer un compte</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
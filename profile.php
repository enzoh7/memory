<?php
require_once 'init.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare('SELECT username, email FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Récupérer les statistiques du joueur
$stmt = $pdo->prepare('
    SELECT 
        COUNT(*) as total_games,
        AVG(score) as avg_score,
        MIN(num_flips) as best_flips,
        MAX(score) as best_score
    FROM scores 
    WHERE user_id = ?
');
$stmt->execute([$_SESSION['user_id']]);
$stats = $stmt->fetch();

// Récupérer les 5 dernières parties
$stmt = $pdo->prepare('
    SELECT num_pairs, num_flips, score, game_date 
    FROM scores 
    WHERE user_id = ? 
    ORDER BY game_date DESC 
    LIMIT 5
');
$stmt->execute([$_SESSION['user_id']]);
$recent_games = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - Memory Rap FR</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Profil de <?php echo htmlspecialchars($user['username']); ?></h1>
        
        <div class="profile-section">
            <h2>Mes statistiques</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <h3>Parties jouées</h3>
                    <p><?php echo $stats['total_games'] ?? '0'; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Score moyen</h3>
                    <p><?php echo $stats['avg_score'] ? number_format($stats['avg_score'], 2) : '0.00'; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Meilleur nombre de coups</h3>
                    <p><?php echo $stats['best_flips'] ?? 'N/A'; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Meilleur score</h3>
                    <p><?php echo $stats['best_score'] ? number_format($stats['best_score'], 2) : '0.00'; ?></p>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Dernières parties</h2>
            <table class="recent-games">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Paires</th>
                        <th>Coups</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_games as $game): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($game['game_date'])); ?></td>
                        <td><?php echo $game['num_pairs']; ?></td>
                        <td><?php echo $game['num_flips']; ?></td>
                        <td><?php echo number_format($game['score'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="menu">
            <a href="index.php">Retour à l'accueil</a>
            <a href="auth.php?action=logout">Déconnexion</a>
        </div>
    </div>
</body>
</html>
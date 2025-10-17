<?php
require_once 'init.php';

$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $action = $_POST['action'];

    try {
        if ($action === 'login') {
            // Tentative de connexion
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit;
            } else {
                $error = "Identifiants incorrects";
            }
        } else if ($action === 'register') {
            // Vérification si l'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                $error = "Ce nom d'utilisateur existe déjà";
            } else {
                // Création du compte
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->execute([$username, $password_hash, $email]);
                
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit;
            }
        }
    } catch (PDOException $e) {
        $error = "Une erreur est survenue. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Authentification - Memory Rap FR</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Memory Rap FR</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="auth-container">
            <!-- Formulaire de connexion -->
            <div class="auth-section">
                <h2>Connexion</h2>
                <form action="auth.php" method="post">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label for="login-username">Nom d'utilisateur</label>
                        <input type="text" id="login-username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password">Mot de passe</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn">Se connecter</button>
                </form>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="auth-section">
                <h2>Créer un compte</h2>
                <form action="auth.php" method="post">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group">
                        <label for="register-username">Nom d'utilisateur</label>
                        <input type="text" id="register-username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email">Email (optionnel)</label>
                        <input type="email" id="register-email" name="email">
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password">Mot de passe</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn">S'inscrire</button>
                </form>
            </div>
        </div>

        <div class="menu">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
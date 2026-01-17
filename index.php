<?php
session_start();
require_once 'config/database.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = "Por favor, complete todos los campos.";
    } else {
        try {
            $query = "SELECT id, username, password, full_name, email FROM users WHERE username = :username OR email = :username";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    
                    header("Location: dashboard.php?success=login");
                    exit();
                } else {
                    $error_message = "Credenciales incorrectas.";
                }
            } else {
                $error_message = "Usuario no encontrado.";
            }
        } catch(PDOException $exception) {
            $error_message = "Error en el sistema. Intente más tarde.";
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] == 'registered') {
    $success_message = "Registro exitoso. Ahora puede iniciar sesión.";
}

if (isset($_GET['error']) && $_GET['error'] == 'session_required') {
    $error_message = "Debe iniciar sesión para acceder a esa página.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boxing Store - Iniciar Sesión</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>🥊 Boxing Store</h1>
                <p>Tu tienda de artículos de boxeo</p>
            </div>
            
            <form class="auth-form" method="POST" action="" id="loginForm">
                <h2>Iniciar Sesión</h2>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="username">Usuario o Email:</label>
                    <input type="text" id="username" name="username" required>
                    <span class="error-message" id="usernameError"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error-message" id="passwordError"></span>
                </div>
                
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                
                <div class="auth-links">
                    <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/validation.js"></script>
</body>
</html>

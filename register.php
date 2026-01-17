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
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize_input($_POST['full_name']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    
    // Validaciones
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error_message = "Por favor, complete todos los campos obligatorios.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 6) {
        $error_message = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        try {
            // Verificar si el usuario o email ya existe
            $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(":username", $username);
            $check_stmt->bindParam(":email", $email);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $error_message = "El usuario o email ya está registrado.";
            } else {
                // Encriptar contraseña
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertar nuevo usuario
                $insert_query = "INSERT INTO users (username, email, password, full_name, phone, address) VALUES (:username, :email, :password, :full_name, :phone, :address)";
                $insert_stmt = $db->prepare($insert_query);
                $insert_stmt->bindParam(":username", $username);
                $insert_stmt->bindParam(":email", $email);
                $insert_stmt->bindParam(":password", $hashed_password);
                $insert_stmt->bindParam(":full_name", $full_name);
                $insert_stmt->bindParam(":phone", $phone);
                $insert_stmt->bindParam(":address", $address);
                
                if ($insert_stmt->execute()) {
                    header("Location: index.php?success=registered");
                    exit();
                } else {
                    $error_message = "Error al registrar usuario.";
                }
            }
        } catch(PDOException $exception) {
            $error_message = "Error en el sistema. Intente más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boxing Store - Registro</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>🥊 Boxing Store</h1>
                <p>Crear nueva cuenta</p>
            </div>
            
            <form class="auth-form" method="POST" action="" id="registerForm">
                <h2>Registro</h2>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="username">Usuario: *</label>
                    <input type="text" id="username" name="username" required>
                    <span class="error-message" id="usernameError"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email: *</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-message" id="emailError"></span>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Nombre Completo: *</label>
                    <input type="text" id="full_name" name="full_name" required>
                    <span class="error-message" id="fullNameError"></span>
                </div>
                
                <div class="form-group">
                    <label for="phone">Teléfono:</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                
                <div class="form-group">
                    <label for="address">Dirección:</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña: *</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error-message" id="passwordError"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña: *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span class="error-message" id="confirmPasswordError"></span>
                </div>
                
                <button type="submit" class="btn btn-primary">Registrarse</button>
                
                <div class="auth-links">
                    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/validation.js"></script>
</body>
</html>

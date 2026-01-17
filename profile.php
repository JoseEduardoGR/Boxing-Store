<?php
require_once 'config/database.php';
check_session();

$database = new Database();
$db = $database->getConnection();

$success_message = "";
$error_message = "";

// Obtener datos actuales del usuario
try {
    $user_query = "SELECT * FROM users WHERE id = :id";
    $user_stmt = $db->prepare($user_query);
    $user_stmt->bindParam(":id", $_SESSION['user_id']);
    $user_stmt->execute();
    $user_data = $user_stmt->fetch();
} catch(PDOException $exception) {
    $error_message = "Error al cargar datos del usuario.";
    $user_data = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Token de seguridad inválido.";
    } else {
        $full_name = sanitize_input($_POST['full_name']);
        $email = sanitize_input($_POST['email']);
        $phone = sanitize_input($_POST['phone']);
        $address = sanitize_input($_POST['address']);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($full_name) || empty($email)) {
            $error_message = "El nombre y email son obligatorios.";
        } else {
            try {
                // Verificar si el email ya está en uso por otro usuario
                $check_email_query = "SELECT id FROM users WHERE email = :email AND id != :user_id";
                $check_email_stmt = $db->prepare($check_email_query);
                $check_email_stmt->bindParam(":email", $email);
                $check_email_stmt->bindParam(":user_id", $_SESSION['user_id']);
                $check_email_stmt->execute();
                
                if ($check_email_stmt->rowCount() > 0) {
                    $error_message = "El email ya está en uso por otro usuario.";
                } else {
                    $update_password = false;
                    $hashed_password = "";
                    
                    // Si se quiere cambiar la contraseña
                    if (!empty($new_password)) {
                        if (empty($current_password)) {
                            $error_message = "Debe ingresar su contraseña actual.";
                        } elseif (!password_verify($current_password, $user_data['password'])) {
                            $error_message = "La contraseña actual es incorrecta.";
                        } elseif ($new_password !== $confirm_password) {
                            $error_message = "Las nuevas contraseñas no coinciden.";
                        } elseif (strlen($new_password) < 6) {
                            $error_message = "La nueva contraseña debe tener al menos 6 caracteres.";
                        } else {
                            $update_password = true;
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        }
                    }
                    
                    if (empty($error_message)) {
                        if ($update_password) {
                            $update_query = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone, address = :address, password = :password WHERE id = :id";
                            $update_stmt = $db->prepare($update_query);
                            $update_stmt->bindParam(":password", $hashed_password);
                        } else {
                            $update_query = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone, address = :address WHERE id = :id";
                            $update_stmt = $db->prepare($update_query);
                        }
                        
                        $update_stmt->bindParam(":full_name", $full_name);
                        $update_stmt->bindParam(":email", $email);
                        $update_stmt->bindParam(":phone", $phone);
                        $update_stmt->bindParam(":address", $address);
                        $update_stmt->bindParam(":id", $_SESSION['user_id']);
                        
                        if ($update_stmt->execute()) {
                            $_SESSION['full_name'] = $full_name;
                            $_SESSION['email'] = $email;
                            $success_message = "Perfil actualizado correctamente.";
                            
                            // Recargar datos del usuario
                            $user_stmt->execute();
                            $user_data = $user_stmt->fetch();
                        } else {
                            $error_message = "Error al actualizar el perfil.";
                        }
                    }
                }
            } catch(PDOException $exception) {
                $error_message = "Error en el sistema.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boxing Store - Perfil</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>🥊 Boxing Store</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="products.php" class="nav-link">Productos</a>
                <a href="profile.php" class="nav-link active">Perfil</a>
                <a href="logout.php" class="nav-link logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Mi Perfil</h1>
            <p>Actualiza tu información personal</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="card">
                <div class="card-header">
                    <h3>Información Personal</h3>
                </div>
                <div class="card-content">
                    <form method="POST" action="" id="profileForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="form-group">
                            <label for="username">Usuario:</label>
                            <input type="text" id="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" disabled>
                            <small>El nombre de usuario no se puede cambiar</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Nombre Completo: *</label>
                            <input type="text" id="full_name" name="full_name" required
                                   value="<?php echo htmlspecialchars($user_data['full_name']); ?>">
                            <span class="error-message" id="fullNameError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email: *</label>
                            <input type="email" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($user_data['email']); ?>">
                            <span class="error-message" id="emailError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Teléfono:</label>
                            <input type="tel" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($user_data['phone']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Dirección:</label>
                            <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                        </div>
                        
                        <hr>
                        
                        <h4>Cambiar Contraseña (Opcional)</h4>
                        
                        <div class="form-group">
                            <label for="current_password">Contraseña Actual:</label>
                            <input type="password" id="current_password" name="current_password">
                            <span class="error-message" id="currentPasswordError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña:</label>
                            <input type="password" id="new_password" name="new_password">
                            <span class="error-message" id="newPasswordError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            <span class="error-message" id="confirmPasswordError"></span>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/validation.js"></script>
</body>
</html>

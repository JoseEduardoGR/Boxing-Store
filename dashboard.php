<?php
require_once 'config/database.php';
check_session();

$database = new Database();
$db = $database->getConnection();

$success_message = "";
$error_message = "";

// Obtener estadísticas del usuario
try {
    $stats_query = "SELECT COUNT(*) as total_products FROM products WHERE user_id = :user_id";
    $stats_stmt = $db->prepare($stats_query);
    $stats_stmt->bindParam(":user_id", $_SESSION['user_id']);
    $stats_stmt->execute();
    $stats = $stats_stmt->fetch();
    
    // Obtener productos recientes del usuario
    $products_query = "SELECT p.*, c.name as category_name FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.user_id = :user_id 
                      ORDER BY p.created_at DESC LIMIT 5";
    $products_stmt = $db->prepare($products_query);
    $products_stmt->bindParam(":user_id", $_SESSION['user_id']);
    $products_stmt->execute();
    $recent_products = $products_stmt->fetchAll();
    
} catch(PDOException $exception) {
    $error_message = "Error al cargar datos.";
}

if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'login':
            $success_message = "¡Bienvenido de vuelta!";
            break;
        case 'profile_updated':
            $success_message = "Perfil actualizado correctamente.";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boxing Store - Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2>🥊 Boxing Store</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="products.php" class="nav-link">Productos</a>
                <a href="profile.php" class="nav-link">Perfil</a>
                <a href="logout.php" class="nav-link logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
            <p>Panel de control de tu tienda de boxeo</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Información Personal</h3>
                </div>
                <div class="card-content">
                    <div class="user-info">
                        <p><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    </div>
                    <a href="profile.php" class="btn btn-secondary">Editar Perfil</a>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Estadísticas</h3>
                </div>
                <div class="card-content">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                            <div class="stat-label">Productos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">6</div>
                            <div class="stat-label">Categorías</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3>Productos Recientes</h3>
                    <a href="products.php" class="btn btn-primary">Ver Todos</a>
                </div>
                <div class="card-content">
                    <?php if (count($recent_products) > 0): ?>
                        <div class="products-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_products as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo $product['stock']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="no-data">No tienes productos registrados aún.</p>
                        <a href="products.php" class="btn btn-primary">Agregar Producto</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="js/validation.js"></script>
</body>
</html>

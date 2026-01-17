<?php
require_once 'config/database.php';
check_session();

$database = new Database();
$db = $database->getConnection();

$success_message = "";
$error_message = "";

// Manejar eliminación de producto
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    try {
        $delete_query = "DELETE FROM products WHERE id = :id AND user_id = :user_id";
        $delete_stmt = $db->prepare($delete_query);
        $delete_stmt->bindParam(":id", $product_id);
        $delete_stmt->bindParam(":user_id", $_SESSION['user_id']);
        
        if ($delete_stmt->execute()) {
            $success_message = "Producto eliminado correctamente.";
        } else {
            $error_message = "Error al eliminar el producto.";
        }
    } catch(PDOException $exception) {
        $error_message = "Error en el sistema.";
    }
}

// Manejar adición/edición de producto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = "Token de seguridad inválido.";
    } else {
        $name = sanitize_input($_POST['name']);
        $description = sanitize_input($_POST['description']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $stock = intval($_POST['stock']);
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        
        if (empty($name) || $price <= 0 || $category_id <= 0) {
            $error_message = "Por favor, complete todos los campos obligatorios.";
        } else {
            try {
                if ($product_id > 0) {
                    // Actualizar producto existente
                    $update_query = "UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id, stock = :stock WHERE id = :id AND user_id = :user_id";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bindParam(":name", $name);
                    $update_stmt->bindParam(":description", $description);
                    $update_stmt->bindParam(":price", $price);
                    $update_stmt->bindParam(":category_id", $category_id);
                    $update_stmt->bindParam(":stock", $stock);
                    $update_stmt->bindParam(":id", $product_id);
                    $update_stmt->bindParam(":user_id", $_SESSION['user_id']);
                    
                    if ($update_stmt->execute()) {
                        $success_message = "Producto actualizado correctamente.";
                    } else {
                        $error_message = "Error al actualizar el producto.";
                    }
                } else {
                    // Crear nuevo producto
                    $insert_query = "INSERT INTO products (name, description, price, category_id, stock, user_id) VALUES (:name, :description, :price, :category_id, :stock, :user_id)";
                    $insert_stmt = $db->prepare($insert_query);
                    $insert_stmt->bindParam(":name", $name);
                    $insert_stmt->bindParam(":description", $description);
                    $insert_stmt->bindParam(":price", $price);
                    $insert_stmt->bindParam(":category_id", $category_id);
                    $insert_stmt->bindParam(":stock", $stock);
                    $insert_stmt->bindParam(":user_id", $_SESSION['user_id']);
                    
                    if ($insert_stmt->execute()) {
                        $success_message = "Producto agregado correctamente.";
                    } else {
                        $error_message = "Error al agregar el producto.";
                    }
                }
            } catch(PDOException $exception) {
                $error_message = "Error en el sistema.";
            }
        }
    }
}

// Obtener categorías
try {
    $categories_query = "SELECT * FROM categories ORDER BY name";
    $categories_stmt = $db->prepare($categories_query);
    $categories_stmt->execute();
    $categories = $categories_stmt->fetchAll();
} catch(PDOException $exception) {
    $categories = [];
}

// Obtener productos del usuario
try {
    $products_query = "SELECT p.*, c.name as category_name FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.user_id = :user_id 
                      ORDER BY p.created_at DESC";
    $products_stmt = $db->prepare($products_query);
    $products_stmt->bindParam(":user_id", $_SESSION['user_id']);
    $products_stmt->execute();
    $products = $products_stmt->fetchAll();
} catch(PDOException $exception) {
    $products = [];
}

// Obtener producto para editar
$edit_product = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    try {
        $edit_query = "SELECT * FROM products WHERE id = :id AND user_id = :user_id";
        $edit_stmt = $db->prepare($edit_query);
        $edit_stmt->bindParam(":id", $edit_id);
        $edit_stmt->bindParam(":user_id", $_SESSION['user_id']);
        $edit_stmt->execute();
        $edit_product = $edit_stmt->fetch();
    } catch(PDOException $exception) {
        $edit_product = null;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boxing Store - Productos</title>
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
                <a href="products.php" class="nav-link active">Productos</a>
                <a href="profile.php" class="nav-link">Perfil</a>
                <a href="logout.php" class="nav-link logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Gestión de Productos</h1>
            <p>Administra tu inventario de artículos de boxeo</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="products-container">
            <div class="product-form-container">
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo $edit_product ? 'Editar Producto' : 'Agregar Producto'; ?></h3>
                    </div>
                    <div class="card-content">
                        <form method="POST" action="" id="productForm">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <?php if ($edit_product): ?>
                                <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="name">Nombre del Producto: *</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>">
                                <span class="error-message" id="nameError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Descripción:</label>
                                <textarea id="description" name="description" rows="3"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="price">Precio: *</label>
                                    <input type="number" id="price" name="price" step="0.01" min="0" required
                                           value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>">
                                    <span class="error-message" id="priceError"></span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="stock">Stock:</label>
                                    <input type="number" id="stock" name="stock" min="0"
                                           value="<?php echo $edit_product ? $edit_product['stock'] : '0'; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="category_id">Categoría: *</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                                <?php echo ($edit_product && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="error-message" id="categoryError"></span>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_product ? 'Actualizar' : 'Agregar'; ?> Producto
                                </button>
                                <?php if ($edit_product): ?>
                                    <a href="products.php" class="btn btn-secondary">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="products-list-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Mis Productos (<?php echo count($products); ?>)</h3>
                    </div>
                    <div class="card-content">
                        <?php if (count($products) > 0): ?>
                            <div class="products-grid">
                                <?php foreach ($products as $product): ?>
                                    <div class="product-card">
                                        <div class="product-info">
                                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?><?php echo strlen($product['description']) > 100 ? '...' : ''; ?></p>
                                            <div class="product-details">
                                                <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                                <span class="product-stock">Stock: <?php echo $product['stock']; ?></span>
                                            </div>
                                        </div>
                                        <div class="product-actions">
                                            <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-small btn-secondary">Editar</a>
                                            <a href="products.php?delete=<?php echo $product['id']; ?>" 
                                               class="btn btn-small btn-danger"
                                               onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-data">No tienes productos registrados aún.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/validation.js"></script>
</body>
</html>

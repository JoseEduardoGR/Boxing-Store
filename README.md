<table>
  <tr>
    <td width="150">
      <img src="assets/logo.png" width="120" alt="Boxing Store Logo">
    </td>
    <td>
      <h1>Boxing Store</h1>
      <em>"Tu tienda de artículos de boxeo"</em>
    </td>
  </tr>
</table>

![Banner](https://img.shields.io/badge/Boxing_Store-v1.0.0-red?style=for-the-badge)

![Powered by PHP](https://img.shields.io/badge/Powered%20by-PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![CSS3](https://img.shields.io/badge/Styled%20with-CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

## 💡 Sobre Boxing Store

**Boxing Store** es una tienda en línea especializada en artículos de boxeo. Desde guantes hasta equipamiento completo, todo lo que necesitas para entrenar como un campeón.

### ✨ Características Principales

* **Catálogo de Productos** - Guantes, sacos, protecciones
* **Sistema de Compras** - Carrito y checkout
* **Autenticación** - Login y registro seguros
* **Dashboard** - Panel de usuario personalizado

### 🔄 Flujo de Usuario

```mermaid
graph TD
    A[👤 Cliente] -->|Visita| B[🏠 Login/Home]
    B -->|Se registra| C[📝 Registro]
    C -->|Cuenta creada| B
    B -->|Inicia sesión| D[📊 Dashboard]
    D -->|Explora| E[🥊 Catálogo]
    E -->|Selecciona| F[📦 Producto]
    F -->|Agrega| G[🛒 Carrito]
    G -->|Compra| H[💳 Checkout]
    H -->|Confirma| I[✅ Pedido Exitoso]
    D -->|Gestiona| J[👤 Perfil]
    style D fill:#E53935,stroke:#333,stroke-width:2px,color:#fff
    style E fill:#C62828,stroke:#333,stroke-width:2px,color:#fff
```

---

## ✨ Características Destacadas

| ⚡ Funcionalidad | 📌 Detalle |
|-----------------|-----------|
| **Autenticación Segura** | Contraseñas hasheadas |
| **Catálogo Completo** | Equipamiento de boxeo |
| **Carrito de Compras** | Sistema funcional |
| **Perfil de Usuario** | Gestión de datos |
| **Diseño Responsive** | Adaptable a cualquier dispositivo |

---

## 🎨 Badges & Estado

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql)
![Status](https://img.shields.io/badge/Status-Completado-success?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

---

## ⚙️ Instalación y Configuración

### 1️⃣ Clonar el Repositorio

```bash
git clone https://github.com/JoseEduardoGR/Boxing-Store.git
cd Boxing-Store
```

### 2️⃣ Configurar Base de Datos

```sql
CREATE DATABASE boxing_store;
USE boxing_store;
SOURCE sql/database.sql;
```

### 3️⃣ Configurar Conexión

Edita `config/database.php`:

```php
private $host = "localhost";
private $db_name = "boxing_store";
private $username = "tu_usuario";
private $password = "tu_password";
```

### 4️⃣ Iniciar Servidor

```bash
php -S localhost:8000
```

Abre `http://localhost:8000` en tu navegador.

---

## 📂 Estructura del Proyecto

```
BOXING-STORE/
├── config/
│   └── database.php         # Conexión a BD
├── css/
│   └── styles.css           # Estilos
├── js/
│   └── validation.js        # Validaciones
├── sql/
│   └── database.sql         # Esquema BD
├── dashboard.php            # Panel de usuario
├── index.php                # Login
├── register.php             # Registro
├── logout.php               # Cerrar sesión
├── products.php             # Catálogo
├── profile.php              # Perfil
├── LICENSE                  # Licencia MIT
└── README.md                # Este archivo
```

---

## 🥊 Categorías de Productos

| Categoría | Descripción |
|-----------|-------------|
| **Guantes** | Entrenamiento y competencia |
| **Protecciones** | Careta, bucal, vendas |
| **Sacos** | Diferentes tamaños |
| **Ropa** | Shorts, playeras |
| **Accesorios** | Cuerdas, bolsas |

---

## 🛠️ Tecnologías Utilizadas

| Tecnología | Uso |
|------------|-----|
| **PHP 8+** | Backend |
| **MySQL** | Base de datos |
| **PDO** | Conexión segura |
| **CSS3** | Diseño |
| **JavaScript** | Validación |

---

## 🏆 Créditos

**JoseEduardoGR** – Desarrollo y diseño.

💻 Proyecto educativo de preparatoria.

---

## 📄 Licencia

Este proyecto está bajo la **Licencia MIT** - ver el archivo [LICENSE](LICENSE) para más detalles.

---

<div align="center">
  <p>🥊 Hecho con ❤️ por <a href="https://github.com/JoseEduardoGR">JoseEduardoGR</a></p>
  <p>Entrena como un campeón</p>
</div>

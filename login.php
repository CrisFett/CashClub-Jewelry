<?php
// login.php - comparación de contraseñas en texto plano (sin hash)
// Reemplaza solo este archivo si quieres quitar toda la lógica de hash.

$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "joyeria";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    // redirigir al login si falla la conexión
    header("Location: index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

// Obtener credenciales (acepta ambos names por compatibilidad)
$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = '';
if (isset($_POST['contrasena'])) $password = trim($_POST['contrasena']);
if (isset($_POST['contraseña'])) $password = trim($_POST['contraseña']);

if ($usuario === '' || $password === '') {
    header("Location: index.html");
    exit;
}

// Consultar contraseña almacenada (texto plano) y rol
$stmt = $conn->prepare("SELECT `contraseña_usu`, `rol_usu` FROM `usuarios` WHERE `usuario_usu` = ? LIMIT 1");
if (!$stmt) {
    header("Location: index.html");
    exit;
}
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    // usuario no encontrado
    $stmt->close();
    $conn->close();
    header("Location: index.html");
    exit;
}

$stmt->bind_result($stored_pass, $rol);
$stmt->fetch();
$stmt->close();
$conn->close();

// Comparación en texto plano (sin hash)
if ($stored_pass !== null && $password === $stored_pass) {
    // Autenticado: redirigir según rol
    if (intval($rol) === 2) {
        header("Location: admin.php");
        exit;
    } else {
        header("Location: bienvenido.html");
        exit;
    }
}

// Si falla, volver al login
header("Location: index.html");
exit;
?>
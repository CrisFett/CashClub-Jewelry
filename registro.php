<?php
// registro.php - versión que almacena la contraseña en texto plano (NO recomendado)
// Reemplaza solo este archivo si quieres que el registro no use hashing.

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "joyeria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_usu = $conn->real_escape_string(trim($_POST['usuario']));
    $correo_usu = $conn->real_escape_string(trim($_POST['correo']));
    $numero_usu = $conn->real_escape_string(trim($_POST['numero']));
    // Almacenar la contraseña tal cual (texto plano) según tu pedido
    $contraseña_usu = $conn->real_escape_string(trim($_POST['contraseña']));
    $rol_usu = 1;

    $sql = "INSERT INTO usuarios (usuario_usu, correo_usu, numero_usu, contraseña_usu, rol_usu) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssi", $usuario_usu, $correo_usu, $numero_usu, $contraseña_usu, $rol_usu);
        if ($stmt->execute()) {
            // Redirigir al login
            header("Location: /Proyecto/index.html");
            exit;
        } else {
            echo "<script>alert('Error al registrar usuario.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error en la consulta.'); window.history.back();</script>";
    }
}
$conn->close();
?>  
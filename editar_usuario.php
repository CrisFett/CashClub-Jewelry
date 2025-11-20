<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "joyeria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener id desde GET (cuando se abre la página) o desde POST (cuando se envía el formulario)
$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // aceptar tanto id_usuario (hidden) como id por compatibilidad
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

    $usuario_usu = isset($_POST['usuario_usu']) ? trim($_POST['usuario_usu']) : '';
    $correo_usu  = isset($_POST['correo_usu']) ? trim($_POST['correo_usu']) : '';
    $numero_usu  = isset($_POST['numero_usu']) ? trim($_POST['numero_usu']) : '';
    $rol_usu     = isset($_POST['rol_usu']) ? intval($_POST['rol_usu']) : 1;

    // Validaciones mínimas
    if ($id_usuario <= 0 || $usuario_usu === '' || $correo_usu === '') {
        echo "<script>alert('Datos inválidos.'); window.history.back();</script>";
        $conn->close();
        exit;
    }

    // Prepared UPDATE usando la columna correcta id_usu
    $upd = $conn->prepare("UPDATE usuarios SET usuario_usu = ?, correo_usu = ?, numero_usu = ?, rol_usu = ? WHERE id_usu = ?");
    if ($upd) {
        $upd->bind_param("sssii", $usuario_usu, $correo_usu, $numero_usu, $rol_usu, $id_usuario);
        if ($upd->execute()) {
            echo "<script>alert('Usuario actualizado correctamente.'); window.location.href='admin.php';</script>";
            $upd->close();
            $conn->close();
            exit;
        } else {
            $err = htmlspecialchars($upd->error);
            $upd->close();
            echo "<script>alert('Error al actualizar usuario: {$err}'); window.history.back();</script>";
            $conn->close();
            exit;
        }
    } else {
        $err = htmlspecialchars($conn->error);
        echo "<script>alert('Error en la consulta: {$err}'); window.history.back();</script>";
        $conn->close();
        exit;
    }
}

// Recuperar datos del usuario usando id_usu (prepared)
if ($id_usuario <= 0) {
    echo "<script>alert('ID de usuario inválido.'); window.location.href='admin.php';</script>";
    $conn->close();
    exit;
}

$stmt = $conn->prepare("SELECT id_usu, usuario_usu, correo_usu, numero_usu, rol_usu FROM usuarios WHERE id_usu = ? LIMIT 1");
if (!$stmt) {
    echo "<script>alert('Error en la consulta.'); window.location.href='admin.php';</script>";
    $conn->close();
    exit;
}
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    echo "<script>alert('Usuario no encontrado.'); window.location.href='admin.php';</script>";
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login-box.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>
    <div class="login-box" style="max-width: 470px;">
        <h2 style="color: #16513c; text-align:center;">Editar Usuario</h2>
        <form method="post" action="editar_usuario.php">
            <!-- mantenemos name id_usuario por compatibilidad con el resto -->
            <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($row['id_usu']); ?>">
            <div class="input-icon-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="usuario_usu" value="<?php echo htmlspecialchars($row['usuario_usu']); ?>" required placeholder="Usuario">
            </div>
            <div class="input-icon-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="correo_usu" value="<?php echo htmlspecialchars($row['correo_usu']); ?>" required placeholder="Correo">
            </div>
            <div class="input-icon-group">
                <i class="fa-solid fa-phone"></i>
                <input type="text" name="numero_usu" value="<?php echo htmlspecialchars($row['numero_usu']); ?>" required placeholder="Teléfono">
            </div>
            <div class="input-icon-group">
                <i class="fa-solid fa-user-shield"></i>
                <input type="number" name="rol_usu" value="<?php echo htmlspecialchars($row['rol_usu']); ?>" min="1" max="2" required placeholder="Rol">
            </div>
            <button type="submit">Actualizar</button>
            <a href="admin.php" style="display:block;text-align:center;margin-top:12px;color:#16513c;">Volver al panel</a>
        </form>
    </div>
</body>
</html>
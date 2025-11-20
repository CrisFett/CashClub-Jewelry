<?php
// admin.php - corregido: usa la columna real id_usu y previene inyección en DELETE.
// Reemplaza solo este archivo en tu proyecto.

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Ca$h Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login-box.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-title">Administrar Usuarios Ca$h Club</div>
        <form class="search-bar" method="get" action="" style="gap:10px;flex-wrap:wrap;">
            <input type="text" name="buscar" placeholder="Buscar por ID o nombre" value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
            <button type="submit">Buscar</button>
            <a href="admin.php" style="text-decoration:none;">
                <button type="button">Mostrar Todos</button>
            </a>
            <a href="index.html" style="text-decoration:none;">
                <button type="button" style="background:#c0392b;color:#fff;">Cerrar Sesión</button>
            </a>
        </form>
        <div id="tabla-usuarios">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "joyeria";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Borrar usuario (usamos id_usu que existe en la tabla)
            if (isset($_GET['eliminar'])) {
                $id = intval($_GET['eliminar']);
                $delStmt = $conn->prepare("DELETE FROM usuarios WHERE id_usu = ?");
                if ($delStmt) {
                    $delStmt->bind_param("i", $id);
                    $delStmt->execute();
                    $delStmt->close();
                }
                echo "<script>window.location='admin.php';</script>";
                exit;
            }

            // Filtro de búsqueda: usamos la columna real id_usu
            $filtro = "";
            if (isset($_GET['buscar']) && $_GET['buscar'] != "") {
                $busqueda = $conn->real_escape_string($_GET['buscar']);
                $filtro = "WHERE id_usu LIKE '%$busqueda%' OR usuario_usu LIKE '%$busqueda%'";
            }

            // Seleccionamos id_usu pero lo aliasamos a id_usuario para que el HTML no necesite cambiar
            $sql = "SELECT id_usu AS id_usuario, usuario_usu, correo_usu, numero_usu, rol_usu FROM usuarios $filtro ORDER BY id_usu ASC";
            $result = $conn->query($sql);

            echo "<table>";
            echo "<tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                  </tr>";

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Sanitizar salida
                    $id_show = htmlspecialchars($row['id_usuario']);
                    $usuario_show = htmlspecialchars($row['usuario_usu']);
                    $correo_show = htmlspecialchars($row['correo_usu']);
                    $numero_show = htmlspecialchars($row['numero_usu']);
                    $rol_show = htmlspecialchars($row['rol_usu']);

                    echo "<tr>";
                    echo "<td>{$id_show}</td>";
                    echo "<td>{$usuario_show}</td>";
                    echo "<td>{$correo_show}</td>";
                    echo "<td>{$numero_show}</td>";
                    echo "<td>{$rol_show}</td>";
                    echo "<td class='admin-actions'>
                            <form style='display:inline;' method='get' action='admin.php' onsubmit=\"return confirm('¿Eliminar usuario?');\">
                                <input type='hidden' name='eliminar' value='". $id_show ."'>
                                <button class='delete-btn' type='submit'>Eliminar</button>
                            </form>
                            <form style='display:inline;' method='get' action='editar_usuario.php'>
                                <input type='hidden' name='id' value='". $id_show ."'>
                                <button class='edit-btn' type='submit'>Actualizar</button>
                            </form>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;padding:18px;color:#16513c;'>No hay usuarios para mostrar.</td></tr>";
            }
            echo "</table>";

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
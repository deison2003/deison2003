<?php
session_start();
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];
    $rol = $_POST['rol'] ?? 'admin';
    $mysqli = new mysqli("localhost", "root", "", "hospital");
    if ($mysqli->connect_errno) {
        die("Fallo la conexión: " . $mysqli->connect_error);
    }
    // Verificar si ya existe el correo
    $stmt = $mysqli->prepare("SELECT id_usuario FROM usuarios WHERE correo=?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $error = 'El correo ya está registrado.';
    } else {
        // Si la clave es hash MD5 (32 caracteres hexadecimales), la guardamos tal cual, si no, la convertimos
        if (preg_match('/^[a-f0-9]{32}$/i', $clave)) {
            $clave_hash = $clave;
        } else {
            $clave_hash = md5($clave);
        }
        $stmt2 = $mysqli->prepare("INSERT INTO usuarios (correo, clave, rol) VALUES (?, ?, ?)");
        $stmt2->bind_param("sss", $correo, $clave_hash, $rol);
        if ($stmt2->execute()) {
            $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
        } else {
            $error = 'Error al registrar usuario.';
        }
        $stmt2->close();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card p-4 shadow" style="min-width:350px;">
        <h3 class="mb-3 text-center">Registro de Usuario</h3>
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <div class="text-center"><a href="login.php">Ir al login</a></div>
        <?php else: ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="text" name="clave" class="form-control" required>
                <div class="form-text">Puedes ingresar tu contraseña en texto plano o en hash MD5.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select">
                    <option value="admin">Administrador</option>
                    <option value="medico">Médico</option>
                    <option value="recepcion">Recepción</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
        </form>
        <div class="mt-3 text-center">
            <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

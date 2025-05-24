<?php
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $mysqli = new mysqli("localhost", "root", "", "hospital");
    if ($mysqli->connect_errno) {
        die("Fallo la conexión: " . $mysqli->connect_error);
    }
    // Buscar usuario por correo
    $stmt = $mysqli->prepare("SELECT id_usuario, clave, rol, intentos_fallidos, bloqueado FROM usuarios WHERE correo=?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id_usuario, $clave_db, $rol, $intentos, $bloqueado);
        $stmt->fetch();
        // Permitir login si la clave coincide en texto plano o hash
        $clave_ingresada_hash = (preg_match('/^[a-f0-9]{32}$/i', $clave)) ? $clave : md5($clave);
        if ($bloqueado) {
            $error = 'Usuario bloqueado por intentos fallidos. Contacte al administrador.';
        } elseif ($clave_db === $clave || $clave_db === $clave_ingresada_hash) {
            // Login correcto: resetear intentos y guardar IP
            $stmt2 = $mysqli->prepare("UPDATE usuarios SET intentos_fallidos=0, ip_ultimo_intento=? WHERE id_usuario=?");
            $stmt2->bind_param("si", $ip, $id_usuario);
            $stmt2->execute();
            $stmt2->close();
            $_SESSION['usuario'] = $correo;
            $_SESSION['rol'] = $rol;
            header('Location: index.php');
            exit;
        } else {
            // Login incorrecto: aumentar intentos y guardar IP
            $intentos++;
            $bloquear = $intentos >= 3 ? 1 : 0;
            $stmt2 = $mysqli->prepare("UPDATE usuarios SET intentos_fallidos=?, ip_ultimo_intento=?, bloqueado=? WHERE id_usuario=?");
            $stmt2->bind_param("isii", $intentos, $ip, $bloquear, $id_usuario);
            $stmt2->execute();
            $stmt2->close();
            if ($bloquear) {
                $error = 'Usuario bloqueado por 3 intentos fallidos.';
            } else {
                $error = 'Contraseña incorrecta. Intentos fallidos: ' . $intentos;
            }
        }
    } else {
        $error = 'Correo no registrado';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card p-4 shadow" style="min-width:350px;">
        <h3 class="mb-3 text-center">Iniciar Sesión</h3>
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="clave" class="form-control" required>
            </div>
            <button href="index.php" type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="mt-3 text-center">
            <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
        </div>
    </div>
</div>
</body>
</html>

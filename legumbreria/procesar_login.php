<?php
session_start();
require_once "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $message = "";
    
    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, nombre, password, intentos_fallidos, bloqueado_hasta FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $usuario_id = $usuario['id'];
            $intentos = $usuario['intentos_fallidos'];
            $bloqueado_hasta = $usuario['bloqueado_hasta'];
            
            // Verificar si la cuenta está bloqueada
            if ($bloqueado_hasta !== null && strtotime($bloqueado_hasta) > time()) {
                $message = "Cuenta bloqueada. Intente más tarde.";
            } else {
                // Verificar la contraseña
                if (password_verify($password, $usuario['password'])) {
                    // Restablecer intentos fallidos
                    $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?");
                    $stmt->execute([$usuario_id]);
                    
                    $_SESSION['id']     = $usuario['id'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $message = "Inicio de sesión exitoso.";
                } else {
                    // Incrementar intentos fallidos
                    $intentos++;
                    if ($intentos >= 4) {
                        $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = ?, bloqueado_hasta = NOW() + INTERVAL 5 MINUTE WHERE id = ?");
                        $stmt->execute([$intentos, $usuario_id]);
                        $message = "Cuenta bloqueada por múltiples intentos fallidos.";
                    } else {
                        $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = ? WHERE id = ?");
                        $stmt->execute([$intentos, $usuario_id]);
                        $message = "Contraseña incorrecta. Intento $intentos de 4.";
                    }
                }
            }
        } else {
            $message = "Usuario no encontrado.";
        }
    } else {
        $message = "Todos los campos son obligatorios.";
    }
    
    header('Content-Type: text/plain');
    echo $message;
    exit();
}
?>
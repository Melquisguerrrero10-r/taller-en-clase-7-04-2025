<?php
require_once "conexion.php";

if (isset($_POST['registro'])) {
    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        // Verificar si el correo ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 0) {
            // Encriptar la contraseña
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar el nuevo usuario
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$nombre, $email, $hash])) {
                // Redireccionar al login con un mensaje
                header("Location: login.php?mensaje=" . urlencode("Registro exitoso, inicie sesión"));
                exit();
            } else {
                echo "Error al guardar el registro.";
            }
        } else {
            echo "El correo ya está registrado.";
        }
    } else {
        echo "Todos los campos son obligatorios.";
    }
}
?>

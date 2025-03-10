<?php
session_start();

// Configuración de la base de datos
$servername = "db";
$username = "root";
$password = "P@ssw0rd.";
$dbname = "sistema_usuarios";
$socket = "/var/run/mysqld/mysqld.sock";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, null, $socket);

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Función para escapar datos y prevenir inyección SQL
function escapar($data) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars($data));
}

// Funciones de Internacionalización (i18n)
function cargarTextos($idioma) {
    $archivo = "lang/{$idioma}.json";
    if (file_exists($archivo)) {
        $json = file_get_contents($archivo);
        return json_decode($json, true);
    } else {
        return json_decode(file_get_contents("lang/es.json"),true); // Si no se encuentra el idioma, cargamos español.
    }
}

$textos = cargarTextos('es'); // we are going to load the texts by default in spanish.
if (isset($_SESSION["idioma"])) {
    $textos = cargarTextos($_SESSION["idioma"]); // if there is a sesion with an idioma, we change it.
}
// Function to set a new password for a user.
function setPassword($conn, $username, $newPassword) {
    global $textos;
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashedPassword, $username);

    if ($stmt->execute()) {
        $stmt->close();
        return ["success" => true, "message" => $textos["password_changed"], "textos"=>$textos];
    } else {
        $stmt->close();
        return ["success" => false, "message" => $textos["error_password_change"], "textos"=>$textos];
    }
}
// Función para verificar si es la primera vez que se accede a la web
function isFirstAccess($conn) {
    $sql = "SELECT COUNT(*) as count FROM usuarios WHERE username = 'admin' AND password IS NOT NULL AND password <> ''";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'] == 0;
    } else {
        return false;
    }
}

// Verificar si es el primer acceso
$firstAccess = isFirstAccess($conn);

if ($firstAccess) {
   if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "setPassword"){
      //procesar el cambio de contraseña.
        if (isset($_POST["newPassword"])) {
              $newPassword = escapar($_POST["newPassword"]);
              $result = setPassword($conn, "admin", $newPassword);
              echo json_encode($result);
              exit;
          } else {
              echo json_encode(["success" => false, "message" => $textos["error_request"], "textos"=>$textos]);
              exit;
          }
   }
} else {
    // Procesar el inicio de sesión
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "login") {
        $username = escapar($_POST["username"]);
        $password = escapar($_POST["password"]);
        $idioma = escapar($_POST["idioma"]);

        $sql = "SELECT * FROM usuarios WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Contraseña válida
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["rol"] = $row["rol"];
                $_SESSION["idioma"] = $idioma;
                 //actualizar el idioma si se cambia
                $sql = "UPDATE usuarios set idioma = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss",$idioma, $username);
                $stmt->execute();

                $textos = cargarTextos($_SESSION["idioma"]); //Recargar los textos para actualizarlos.

                // Redirigir a la página adecuada
                if ($row["rol"] == "admin") {
                    $redirect = "panel_admin";
                } else {
                    $redirect = "panel_usuario";
                }
                echo json_encode(["success" => true, "redirect" => $redirect, "textos" => $textos]);
                exit;
            } else {
                // Contraseña incorrecta
                echo json_encode(["success" => false, "message" => $textos["error_password"], "textos" => $textos]);
                exit;
            }
        } else {
            // Usuario no encontrado
            echo json_encode(["success" => false, "message" => $textos["error_usuario"], "textos" => $textos]);
            exit;
        }

        $stmt->close();
    }
    // Processing the password change request
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "setPassword") {
      if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["rol"] === "admin") {
        if (isset($_POST["newPassword"])) {
              $newPassword = escapar($_POST["newPassword"]);
              $result = setPassword($conn, "admin", $newPassword);
              echo json_encode($result);
              exit;
          } else {
              echo json_encode(["success" => false, "message" => $textos["error_request"], "textos"=>$textos]);
              exit;
          }
      } else {
          echo json_encode(["success" => false, "message" => $textos["not_authenticated"], "textos"=>$textos]);
          exit;
      }
    }

}

// Cerrar Sesión
if (isset($_GET["action"]) && $_GET["action"] == "logout") {
    session_destroy();
    header("location: ../index.html");
    exit;
}

$conn->close();
?>

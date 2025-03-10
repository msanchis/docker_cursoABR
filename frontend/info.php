<?php
phpinfo();


// Configuración de la base de datos
$servername = "db";
$username = "root";
$password = "P@ssw0rd.";
$dbname = "sistema_usuarios";
// the socket we found in the step 2.
$socket = "/var/run/mysqld/mysqld.sock";  

// Crear conexión with the socket parameter
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

//Mostrar por pantalla todos los usuarios de la base de datos
$sql = "SELECT username, rol FROM usuarios";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result(); 

//Mostrar la variable $result en la web 
//echo $result // It is an object, we have to show it in another way.
while ($row = $result->fetch_assoc()) {
    echo "Username: " . $row["username"] . ", Rol: " . $row["rol"] . "<br>";
}
?>
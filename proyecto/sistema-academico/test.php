<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host   = "sql201.byetcluster.com";
$dbname = "if0_42115615_sistema_gestion";
$user   = "if0_42115615";
$pass   = "Jeremu4550";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    echo "<h3>Tablas en la BD:</h3>";
    $tablas = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tablas as $t) echo "- $t<br>";

    echo "<h3>Columnas de 'usuarios':</h3>";
    $cols = $pdo->query("DESCRIBE usuarios")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) echo "- {$c['Field']} ({$c['Type']})<br>";

    echo "<h3>Columnas de 'roles':</h3>";
    $cols2 = $pdo->query("DESCRIBE roles")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols2 as $c) echo "- {$c['Field']} ({$c['Type']})<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

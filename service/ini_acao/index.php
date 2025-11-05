<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    die();
}

require_once __DIR__ . "/../utils.php";
$con = conecta('mysaas.com.br', 'limacabral', 'ro7n26', 'lcc_limacabral', 3309);

$query = "SELECT * FROM ini_acao;";
echo encodeJSON(mysqli_query_exec($query, $con, __file__, false));

?>
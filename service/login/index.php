<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Exposed-Header: Authorization");
header("Access-Control-Allow-Headers: Content-Type, Accept,  X-Auth-Token, Origin,  Authorization,  Client-Security-Token, Accept-Encoding, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    die();
}

require_once __DIR__ . "/../utils.php";
$con = conecta('mysaas.com.br', 'limacabral', 'ro7n26', 'lcc_limacabral', 3309);

$data = $_REQUEST;
error_log(print_r($data, true));

$query = "SELECT * FROM cad_usuario WHERE isnull(data_exc) AND ativo = 'S' AND email = '".$data['email']."' AND senha = '".$data['password']."';";
echo encodeJSON(mysqli_query_exec($query, $con, __file__, false));

?>
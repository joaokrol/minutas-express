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

$query = "INSERT INTO ini_pessoas 
            SET nome = '".$data['nome']."', nome_fantasia = '".$data['nome_fantasia']."', tipo_pessoa='".$data['tipo']."', cpfcnpj = '".$data['cpfcnpj']."', sexo = '".$data['sexo']."',
                rua = '".$data['rua']."', cep = '".$data['cep']."', endereco_numero = '".$data['numero']."', complemento = '".$data['complemento']."', cidade = '".$data['cidade']."',
                bairro = '".$data['bairro']."', email = '".$data['email']."', telefone = '".$data['telefone']."', estado_civil = '".$data['estado_civil']."', cargo = '".$data['cargo']."', 
                n_doc = '".$data['numero_documento']."', tipo_doc = '".$data['tipo_documento']."', data_inc = ".date('Ymd').", hora_inc = ".date('His').";";
$id = mysqli_query_exec($query, $con, __file__, false);
echo encodeJSON(array("insertId" => $id));
?>
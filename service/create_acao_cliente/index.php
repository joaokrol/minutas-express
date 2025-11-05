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

error_log($data['valor_causa']);
$data['valor_causa'] = str_replace("R$", "", str_replace(",", ".", str_replace(".", "", $data['valor_causa'])));
error_log($data['valor_causa']);

$query = "INSERT INTO ini_acoes_cliente
            SET id_reu = '".$data['id_reu']."', acao = '".$data['acao']."', credor = '".$data['credor']."', materia = '".$data['materia']."', grupo_trabalho = '".$data['grupo_trabalho']."',
            adv_responsavel = '".$data['responsavel']."', responsavel_ada = '".$data['responsavel_ada']."', comarca = '".$data['comarca']."', eletronico='".$data['eletronico']."',
            localizador = '".$data['localizador']."', contrato = '".$data['contrato']."', valor_causa = '".$data['valor_causa']."', garantia = '".$data['garantia']."', assunto = '".$data['assunto']."',
            data_kit = '".$data['data_kit']."', tipo_peticao = '".$data['peticao']['tipo_peticao']."', unidade='".$data['peticao']['unidade']."', jurisprudencia = '".$data['jurisprudencia']."',
            arquivo_ajuizamento = '".$data['url_file']."', arquivo_peticao = '".$data['url_arquivo_gerado']."', arquivo_peticao_editado = '".$data['url_arquivo_editado']."', pa = '".$data['pa']."', agencia = '".$data['agencia']."';";
$id = mysqli_query_exec($query, $con, __file__, false);

foreach ($data['peticao']['contratos'] as $contrato) {
    $query = "INSERT INTO ini_contratos
                SET id_acao = $id, numero = '".$contrato['numero_contrato']."', data_contrato = '".$contrato['data_contrato']."',
                qtd_parcelas = '".$contrato['quantidade_parcelas']."', valor_parcela = '".$contrato['valor_parcela']."', valor_contrato = '".$contrato['valor_contrato']."',
                data_vencimento_parcela = '".$contrato['data_vencimento_parcela']."', antecipado = '".$contrato['antecipado']."', clausula = '".$contrato['clausula']."',
                aceite = '".$contrato['aceite']."';";
    mysqli_query_exec($query, $con, __file__, false);
}

foreach ($data['peticao']['anexos'] as $anexo) {
    $query = "INSERT INTO ini_anexos SET numero = '".$anexo['id']."', descricao = '".$anexo['label']."', arquivo = '".$anexo['nome']."', id_acao = $id;";
    mysqli_query_exec($query, $con, __file__, false);
}


foreach ($data['avalistas'] as $avalista) {
    $query = " INSERT INTO ini_avalistas
                    SET tipo_pessoa = '".$avalista['tipo_pessoa']."', nome = '".$avalista['nome']."', cpfcnpj = '".$avalista['cpfcnpj']."',
                    rua = '".$avalista['rua']."', numero = '".$avalista['numero']."', bairro = '".$avalista['bairro']."',
                    complemento = '".$avalista['complemento']."', cep = '".$avalista['cep']."', cidade = '".$avalista['cidade']."',
                    email = '".$avalista['email']."', telefone = '".$avalista['telefone']."', id_acao = '".$data['id_acao_cliente']."',
                    documento = '".$avalista['tipo_doc']."', n_doc = '".$avalista['n_doc']."', profissao = '".$avalista['profissao']."',
                    estado_civil = '".$avalista['estado_civil']."', nome_fantasia = '".$avalista['nome_fantasia']."';";
    mysqli_query_exec($query, $con, __file__, false);
}

?>
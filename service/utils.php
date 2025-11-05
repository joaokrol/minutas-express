<?php
    // AJUSTA TEMPO DE EXECUÇÃO
    set_time_limit(0);

    function conecta($host, $user, $pass, $banco, $porta = 3306){
        $link = mysqli_connect($host, $user, $pass, $banco, $porta) or die("Error " . mysqli_connect_error());
        mysqli_query($link, "SET NAMES UTF8;");
        return $link;
    }

    function mysqli_query_exec($query, $con, $arquivo, $log = false){
        $result = mysqli_query($con, $query);
        $insert_id = mysqli_insert_id($con);
        
        if(mysqli_error($con) != ""){
            $erro = mysqli_error($con);
            $id = notificaErro($arquivo, $query, $erro, $con, $arquivo);
            throw new Exception("Ocorreu um erro ao efetuar operação no banco de dados: ".date('Y-m-d H:i:s')." # ".(" # $id\nErro: ".$erro."\nQuery: ".$query));
        }else if($log){
            $query_log = "INSERT INTO log SET data = ".date('YmdHis').", envio = 0, itens = 0, usuario = '$arquivo', origem = 80, modulo = '$arquivo', str_sql = '".mysqli_real_escape_string($con, $query)."';";
            mysqli_query($con, $query_log);
        }

        $query_log = "INSERT INTO log_erp SET data = ".date('YmdHis').", usuario = 'SERVIDOR', arquivo = '$arquivo', str_sql = '".mysqli_real_escape_string($con, $query)."';";
        mysqli_query($con, $query_log);

        return $insert_id > 0 ? $insert_id : $result;
    }

    function notificaErro($arquivo, $query, $msg_erro, $con, $usuario){
        $query_erro = "INSERT INTO log_erros_web SET data_time = '".date('Y-m-d H:i:s')."', tela = '$arquivo', query_sql = '".mysqli_real_escape_string($con, $query)."', erro_sql = '".str_replace("'", '', $msg_erro)."';"; 
        mysqli_query($con, $query_erro);
        if(mysqli_error($con) != "")
            throw new Exception(mysqli_error($con), 99);
        
        $id = mysqli_insert_id($con);
        $query = "INSERT INTO log_erp SET data = ".date('YmdHis').", usuario = 'SERVIDOR', tempo = 0, arquivo = '$arquivo', str_sql = '$query', erro = '$msg_erro';";
        mysqli_query($con, $query_erro);
        if(mysqli_error($con) != "")
            throw new Exception(mysqli_error($con), 99);
    }

    function encodeJSON($result){
        $retorno = array();
        
        if(is_a($result, 'mysqli_result')){
            while($row = mysqli_fetch_assoc($result))
                $retorno[] = $row;
        }
        else{
            $retorno = $result;
        }
        
        return json_encode($retorno);
    }

    function get_sequencia($origem,$tabela,$con){
        echo "<pre>";
        echo "(origem= '$origem', tabela='$tabela')";
        echo "</pre>";
        
        $query = "SELECT idt, origem FROM seq WHERE origem = '$origem' AND tabela = '$tabela';";
        $result = mysqli_query_exec($query, $con, $tabela, false);
        $result = mysqli_fetch_assoc($result);
        
        $nume_new = ($result['idt'] + 1);

        $idt = $nume_new.$result['origem'];

        $query_log = "UPDATE seq SET idt = $nume_new WHERE origem = '$origem' AND tabela = '$tabela';";
        $result = mysqli_query_exec($query_log, $con, $tabela, false);

        return $idt;
    }

    function ddmmaaaa($data) {
            if(strpos($data , "-") === false)
                $data = substr($data, 0, 4)."-".substr($data, 4, 2)."-".substr($data, 6, 2);
            list($ano,$mes,$dia) = explode('-',$data);
            if($ano && $mes && $dia)
                if($ano!='0000' && $mes!='00' && $dia !='00')
                    return $dia.".".$mes.".".$ano;
    }

    function formatValor($value){
        return '<font color="red">'.str_replace("BRL", "", number_format($value, 3, ",", "")).'</font>';
    }

    function geraTmpFile($dados, $pdf){
        $temp_file = tempnam(sys_get_temp_dir(), 'tmp');
        $arquivo = fopen($temp_file, "a");
        fwrite($arquivo, $dados);
        fclose($arquivo);
        $pdf->addPDF($temp_file);
    }

?>

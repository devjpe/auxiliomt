<?php
	$clientes = array();

	$pdoConn = new PDO('');
	$pdoConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dataInicial = '2019-02-01 00:00:00';
    $dataFinal   = '2019-02-07 23:59:59';

    //Consuta total
	$jp = $pdoConn->query("SELECT * FROM cadastro_clientes WHERE  cliente_horario BETWEEN '{$dataInicial}' AND '{$dataFinal}'");

    $quantiaContrato = 0;
    $quantiaNaotemdireito = 0;
    $quantiaJudicial = 0;

    $quantiaContratoN = 0;
    $quantiaNaotemdireitoN = 0;
    $quantiaJudicialN = 0;

    while($row = $jp->fetch(PDO::FETCH_OBJ)) {
        if ($row->cliente_categoria != 'Contrato') {
            $quantiaContrato++;
            $clientes[$quantiaContrato] = $row->cliente_idunico;
        }

        if ($row->cliente_categoria != 'Não tem direito') {
            $quantiaNaotemdireito++;
            $clientesNtd[$quantiaNaotemdireito] = $row->cliente_idunico;
        }

        if ($row->cliente_categoria != 'Judicial') {
            $quantiaJudicial++;
            $clientesJd[$quantiaJudicial] = $row->cliente_idunico;
        }

        if ($row->cliente_categoria == 'Contrato') {
            $quantiaContratoN++;
        }

        if ($row->cliente_categoria == 'Não tem direito') {
            $quantiaNaotemdireitoN++;
        }

        if ($row->cliente_categoria == 'Judicial') {
            $quantiaJudicialN++;
        }
    }

    // Contrato
        $clientes = array_unique($clientes);
        $a = 0;
        $cli = '';
        foreach ($clientes as $key => $value){
    		$cli .= $value.',';
        }
       	$cli = rtrim($cli, ',');
    // 

    //Nao tem direito
        $clientesNtd = array_unique($clientesNtd);
        $a = 0;
        $cliNtd = '';
        foreach ($clientesNtd as $key => $value){
            $cliNtd .= $value.',';
        }
        $cliNtd = rtrim($cliNtd, ',');
    //

    //Judicial
        $clientesJd = array_unique($clientesJd);
        $a = 0;
        $cliJd = '';
        foreach ($clientesJd as $key => $value){
            $cliJd .= $value.',';
        }
        $cliJd = rtrim($cliJd, ',');
    //
    

    // Contrato
        $mudou = $pdoConn->query("SELECT * FROM historico_user_categoria WHERE cliente_id IN (".$cli.")");
        $mudouCont = 0;
        $clientesContC = array();
        while($row = $mudou->fetch(PDO::FETCH_OBJ)) {
            if (!empty($row->mudou) && $row->mudou == 'Contrato' && $row->para != 'Contrato') {
                //echo $id++ ." - ". $row->mudou.' -> '.$row->para.' |ID -> '.$row->cliente_id."\n";
                $mudouCont++;
                $clientesContC[$mudouCont] = $row->cliente_id;
            }
        }

        $clientesContC = array_unique($clientesContC);
        $mudouContC = 0;
        foreach ($clientesContC as $key => $value){
            $mudouContC++;
        }

        //echo "Contratos ------- > Mudou -> " . $mudouContC . " Sem alteração -> " . $quantiaContratoN ."\n";
    // 


    //Nao tem direito
        $mudou = $pdoConn->query("SELECT * FROM historico_user_categoria WHERE cliente_id IN (".$cliNtd.")");
        $mudouCont = 0;
        $clientesContN = array();
        while($row = $mudou->fetch(PDO::FETCH_OBJ)) {
            if (!empty($row->mudou) && $row->mudou == 'Não tem direito' && $row->para != 'Não tem direito') {
                //echo $id++ ." - ". $row->mudou.' -> '.$row->para.' |ID -> '.$row->cliente_id."\n";
                $mudouCont++;
                $clientesContN[$mudouCont] = $row->cliente_id;
            }
        }

        $clientesContN = array_unique($clientesContN);
        $mudouContN = 0;
        foreach ($clientesContN as $key => $value){
            $mudouContN++;
        }

        //echo "Não tem direito - > Mudou -> " . $mudouContN . " Sem alteração -> " . $quantiaNaotemdireitoN ."\n";
    //


    //Judicial
        $mudou = $pdoConn->query("SELECT * FROM historico_user_categoria WHERE cliente_id IN (".$cliJd.")");
        $mudouCont = 0;
        $clientesContJ = array();
        while($row = $mudou->fetch(PDO::FETCH_OBJ)) {
            if (!empty($row->mudou) && $row->mudou == 'Judicial' && $row->para != 'Judicial') {
                //echo $id++ ." - ". $row->mudou.' -> '.$row->para.' |ID -> '.$row->cliente_id."\n";
                $mudouCont++;
                $clientesContJ[$mudouCont] = $row->cliente_id;
            }
        }

        $clientesContJ = array_unique($clientesContJ);
        $mudouContJ = 0;
        foreach ($clientesContJ as $key => $value){
            $mudouContJ++;
        }

        //echo "Judicial ---------> Mudou -> " . $mudouContJ . " Sem alteração -> " . $quantiaJudicialN ."\n";
    //

    $totalR = $mudouContC + $quantiaContratoN +  $mudouContN + $quantiaNaotemdireitoN + $mudouContJ + $quantiaJudicialN;
    $totalContratos = $mudouContC + $quantiaContratoN;
    $totalNtd = $mudouContN + $quantiaNaotemdireitoN;
    $totalJudicial = $mudouContJ + $quantiaJudicialN;

    ///////////////////////////////////////////////////////////////////////////////////////////////////

    $atendentes = $pdoConn->query("SELECT * FROM historico_user_categoria WHERE horario BETWEEN '{$dataInicial}' AND '{$dataFinal}'");
    $cliatendentes = array();
    $idw = 0;
    while($row = $atendentes->fetch(PDO::FETCH_OBJ)) {
        if (!empty($row->mudou) && $row->mudou != 'Adicionou ADV:' && $row->mudou != 'Removeu ADV:') {
            $idw++;
            $cliatendentes[$idw] = $row->cliente_id;
        }
    }

    $cliatendentes = array_unique($cliatendentes);
    $cliU = '';
    foreach ($cliatendentes as $key => $value){
        $cliU .= $value.',';
    }

    $cliU = rtrim($cliU, ',');

    $atendentesU = $pdoConn->query("SELECT cliente_categoria FROM cadastro_clientes WHERE cliente_idunico IN (".$cliU.")");

    $atendido = 0;
    $agendado = 0;
    $online = 0;
    $reprovado = 0;
    $n_tem_direito = 0;
    $gravida = 0;

    while($row = $atendentesU->fetch(PDO::FETCH_OBJ)) {
        if ($row->cliente_categoria == 'Atendido') {
            $atendido++;
        }elseif ($row->cliente_categoria == 'Agendado') {
            $agendado++;
        }elseif ($row->cliente_categoria == 'Online') {
            $online++;
        }elseif ($row->cliente_categoria == 'Reprovado') {
            $reprovado++;
        }elseif ($row->cliente_categoria == 'Não tem direito') {
            $n_tem_direito++;
        }elseif ($row->cliente_categoria == 'Grávida') {
            $gravida++;
        }
    }

    $totalRs = $atendido+$agendado+$online+$reprovado+$n_tem_direito+$gravida;

    // Retornos
    $resultadoAtendimento = array(
        'atendido'      => $atendido,
        'agendado'      => $agendado,
        'online'        => $online,
        'reprovado'     => $reprovado,
        'naoTemDireito' => $n_tem_direito,
        'gravida'       => $gravida,
        'total'         => $totalRs
    );

    $resultadoTotal = array(
        'contrato'      => $totalContratos,
        'naoTemDireito' => $totalNtd,
        'judicial'      => $totalJudicial,
        'total'         => $totalR
    );

    //Resultado
    print_r($resultadoAtendimento);
    print_r($resultadoTotal);
    //

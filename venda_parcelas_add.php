<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";
    require_once "classes/asaas/asaas_clientes.php";
    require_once "venda_gravar_valida.php";
    require_once "venda_gravar_parcelas.php";
    
    function gravarParcela($parcelasData) {
        try {
            $beginTrans = false;

            $idCliente = $parcelasData['id_cliente'];
            if (!$idCliente) throw new Exception("Cliente nao informado.");

            $idVenda = $parcelasData['id_venda'];
            if (!$validaParcelas['validou']) throw new Exception("ID da venda nao informado.");
            
            $validaParcelas = validaVendaParcelas($parcelasData['parcelas']);
            if (!$validaParcelas['validou']) throw new Exception($validaParcelas['error']);

            $conn = bd_connect_livel();

            if (!$conn) throw new Exception("Nao foi possivel conectar ao banco de dados.");

            mysqli_begin_transaction($conn);
            $beginTrans = true;

            $retVendaParcelas = vendaGravarParcelas($idCliente, $idVenda, $parcelasData['parcelas'], $conn);
            if (!$retVendaParcelas['vendaParcelas']) throw new Exception($retVendaParcelas['error']);

            mysqli_commit($conn);

            http_response_code(200);
            return ["validou" => true, "error" => false];

        } catch(Exception $e) {
            if ($beginTrans) mysqli_rollback($conn);

            http_response_code(400);
            return ["validou" => false, "error" => $e->getMessage()];
        }
    }

    $parcelasPost = file_get_contents('php://input');
    $parcelasPost = utf8_decode($parcelasPost);
    $parcelasData = json_decode($parcelasPost, true); //getPost();

    $retParcelas = gravarParcela($parcelasData);
    echo json_encode($retParcelas, JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
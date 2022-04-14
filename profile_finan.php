<?php

    function AlunoProfileFinan($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_vendas = array();
            $arr_transacoes = array();

            $str_sql = "SELECT 
                lo_vendas.lo_id_venda AS VendaID, 
                lo_vendas.lo_venda_perfis Perfis, 
                lo_vendas.lo_id_venda_tipo AS VendaTipoID,
                UCASE(lo_venda_tipos.lo_venda_tipo_descricao) AS VendaTipo, 
                
                (CASE 
                    WHEN lo_vendas.lo_venda_vigencia = 1 THEN 'MENSAL' 
                    WHEN lo_vendas.lo_venda_vigencia = 2 THEN 'BIMESTRAL'
                    WHEN lo_vendas.lo_venda_vigencia = 3 THEN 'TRIMESTRAL'
                    WHEN lo_vendas.lo_venda_vigencia = 6 THEN 'SEMESTRAL'
                    WHEN lo_vendas.lo_venda_vigencia = 12 THEN 'ANUAL'
                    ELSE CONCAT(lo_vendas.lo_venda_vigencia,'MESES')
                    END
                ) AS VendaVigenciaDescricao,
                
                
                lo_vendas.lo_venda_data AS VendaData,
                lo_venda_status.lo_venda_status_descricao AS VendaStatus,
                
                (SELECT 
                MIN(lo_venda_itens.lo_item_vigencia_inicio) 
                FROM 
                lo_venda_itens 
                WHERE 
                lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda 
                ) AS VendaVigenciaInicial,
                
                (SELECT 
                MAX(lo_venda_itens.lo_item_vigencia_fim) 
                FROM 
                lo_venda_itens 
                WHERE 
                lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda 
                ) AS VendaVigenciaFinal,
                
                (CASE
                    WHEN lo_vendas.lo_venda_vigencia > 1 AND lo_vendas.lo_id_venda_tipo = 2 THEN 0
                    ELSE 1
                    END
                ) AS VendaCancelamento,

                (SELECT
                COUNT(lo_transacoes.lo_id_transacao)
                FROM lo_transacoes
                WHERE lo_transacoes.lo_transacao_vencimento < DATE(NOW()) 
                AND (lo_transacoes.cs019b_id_spay_transacao_status NOT IN (1,31) OR lo_transacoes.cs019b_id_spay_transacao_status IS NULL)
                AND lo_transacoes.lo_id_venda = lo_vendas.lo_id_venda
                ) AS VendaPendenciaFinan
                
                FROM 
                lo_vendas 
                INNER JOIN lo_venda_tipos ON lo_vendas.lo_id_venda_tipo = lo_venda_tipos.lo_id_venda_tipo 
                INNER JOIN lo_venda_status ON lo_venda_status.lo_id_venda_status = lo_vendas.lo_id_venda_status
                WHERE 
                lo_vendas.c001_id_aluno_lo = " . $id_aluno . "
                ORDER BY lo_vendas.lo_venda_data DESC";

            $rs_venda = mysqli_query($conn, $str_sql);	   
            $num_venda = mysqli_num_rows($rs_venda);  

            $i=0;

            while($r = mysqli_fetch_assoc($rs_venda)) {
                

                $id_venda = $r['VendaID'];
                
                $arr_vendas[] = $r;

                $arr_transacoes = array();
                
                $str_sql = "SELECT
                    lo_transacoes.lo_id_transacao,
                    lo_transacoes.lo_id_recorrencia,
                    cs009e_cartao_administradora.cs009e_admnistradora_nome,
                    cs019a_spay_cartoes.cs009e_id_admnistradora,
                    lo_aluno_cc.lo_cc_numero,
                    lo_aluno_cc.lo_cc_validade,
                    lo_transacoes.lo_transacao_parcela,
                    lo_transacoes.lo_transacao_vencimento,
                    lo_transacoes.lo_transacao_periodo_ini,
                    lo_transacoes.lo_transacao_periodo_fim,
                    lo_transacoes.cs019b_id_spay_transacao_status,
                    cs019b_spay_transacao_status.cs019b_status,
                    lo_transacoes.cs019c_id_recorrencia_status,
                    cs019c_recorrencia_status.cs019c_status_descricao,
                    lo_transacoes.cs019e_id_retorno,
                    cs019e_retorno_operadoras.cs019e_retorno_codigo,
                    cs019e_retorno_operadoras.cs019e_retorno_descricao,
                    cs019e_retorno_operadoras.cs019e_retorno_loja,
                    cs019e_retorno_operadoras.cs019e_retorno_cliente,
                    cs019e_retorno_operadoras.cs019e_retorno_status_final,
                    lo_transacoes.lo_transacao_last_try,
                    lo_transacoes.lo_transacao_aut_data,
                    lo_transacoes.lo_transacao_status_final
                    FROM
                    lo_transacoes
                    LEFT OUTER JOIN cs019b_spay_transacao_status ON lo_transacoes.cs019b_id_spay_transacao_status = cs019b_spay_transacao_status.cs019b_id_spay_transacao_status
                    INNER JOIN cs019c_recorrencia_status ON lo_transacoes.cs019c_id_recorrencia_status = cs019c_recorrencia_status.cs019c_id_recorrencia_status
                    INNER JOIN lo_aluno_cc ON lo_transacoes.lo_id_aluno_cc = lo_aluno_cc.lo_id_aluno_cc
                    INNER JOIN cs019a_spay_cartoes ON lo_aluno_cc.cs019a_id_spay_cartao = cs019a_spay_cartoes.cs019a_id_spay_cartao
                    LEFT OUTER JOIN cs019e_retorno_operadoras ON lo_transacoes.cs019e_id_retorno = cs019e_retorno_operadoras.cs019e_id_retorno AND cs019a_spay_cartoes.cs009g_id_operadora = cs019e_retorno_operadoras.cs009g_id_operadora
                    INNER JOIN cs009e_cartao_administradora ON cs019a_spay_cartoes.cs009e_id_admnistradora = cs009e_cartao_administradora.cs009e_id_admnistradora
                    WHERE
                    lo_transacoes.lo_id_venda = "  .  $id_venda . "
                    ORDER BY
                    lo_transacoes.lo_transacao_vencimento";

                $rs_transacoes = mysqli_query($conn, $str_sql);	   
                $num_parcelas = mysqli_num_rows($rs_transacoes);  

                while($rt = mysqli_fetch_assoc($rs_transacoes)) {
                    
                    //Descrição - Recorrência ou Parcela
                    if ($rt['lo_id_recorrencia']) {
                        $TransacaoDesc = "Período: " . $rt['lo_transacao_periodo_ini'] . " a " . $rt['lo_transacao_periodo_fim'];

                    } else {
                        $TransacaoDesc = "Parcela " . $rt['lo_transacao_parcela'] . " de " . $num_parcelas;
                    }

                    //Vencimento e Status transação
                    $dias_vencimento = DateDifDays($rt['lo_transacao_vencimento']);

                    if ($dias_vencimento < 0)  {
                        if ($rt['cs019b_id_spay_transacao_status']==1 || $rt['cs019b_id_spay_transacao_status']==31) {
                            $parcela_status = "Quitada";
                            $parcela_pendente = false;
                            $trocar_cartao = false;    
                            
                        } else {
                            $parcela_status = ($rt['cs019e_retorno_cliente'] ? $rt['cs019e_retorno_cliente'] : $rt['cs019b_status']);
                            if (!$parcela_status) $parcela_status = $rt['cs019c_status_descricao'];

                            $parcela_pendente = true;
                            $trocar_cartao = ($rt['lo_transacao_status_final']==1 ? true : false);                               
                        }
                    } else {
                        $parcela_status = "A vencer";
                        $parcela_pendente = false;
                        $trocar_cartao = false;

                    }
                    
                    $cartao_bandeira_img = 'http://fitgroup.com.br/livel_fitbox/asses/cc_bandeiras/' . $rt['cs009e_id_admnistradora'] . '_32.png';
                    
                    $cartao_validade = date_create($rt['lo_cc_validade']);
                    $cartao_validade  = date_format($cartao_validade, 'm/Y');

                    $cartao_vencido = (DateDifDays($rt['lo_cc_validade']) < 0 ? true : false);

                    $arr_transacoes[] = array('TransacaoID'=>$rt['lo_id_transacao'], 
                                              'CartaoBandeira'=>$cartao_bandeira_img,
                                              'CartaoNumeroFinal'=>substr(CryptString('DECRYPT',$rt['lo_cc_numero']),-4),
                                              'CartaoValidade'=>  $cartao_validade,
                                              'CartaoVencido'=>$cartao_vencido,
                                              'TransacaoDescricao'=>$TransacaoDesc,
                                              'TransacaoVencimento'=>$rt['lo_transacao_vencimento'],
                                              'TransacaoStatus'=>$parcela_status,
                                              'TransacaoPendente'=>$parcela_pendente,
                                              'TrocarCartao'=>$trocar_cartao);

                    //$arr_vendas[$i]['Transacoes'][] = $rt;
                }

                $arr_vendas[$i]['Transacoes'] = $arr_transacoes;

                $i++;                
            }                                     
        }

        $arr_result = array('Registros'=>$num_venda,'Vendas'=>$arr_vendas, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>
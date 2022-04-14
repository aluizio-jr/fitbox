<?php
    //$id_academia = $_GET['id_academia'];
    $arquivo_foto = $_GET['arquivo_foto'];

    $img_aluno = $_GET['arquivo_foto'];;

    if (file_exists("../img/" . $img_aluno)) {
        $arr_img = getimagesize('http://fitgroup.com.br/livel_fitbox/img/'.$img_aluno);
        $resize = ($arr_img[0] > $arr_img[1]) ? 'width':'height';
                    
        echo "<div><img style='" . $resize . ": 100%; object-fit: contain;' src='http://fitgroup.com.br/livel_fitbox/img//".$img_aluno."'></div>";

    } else {
        echo "<div align-'center' style='font-family: Arial, Helvetica, sans-serif;font-weight:plain;font-size: 12px;color:#333333;line-height:1.5em;'>Foto não disponível</div>";
    }

    //style=""font-family: Arial, Helvetica, sans-serif;font-weight:plain;font-size: 12px;color:#333333;line-height:1.5em;

?>
<?php
    error_reporting(0);	

    $Action = $_GET['Action'];
    $ExercNome = $_GET['ExercNome'];
    $ExercArquivo = $_GET['ExercArquivo'];
    $ExercTipo = $_GET['ExercTipo'];

    if ($Action=="check_file") {
        $arquivo_found = file_exists("../app/assets/exercicios/$ExercArquivo");
        echo $arquivo_found;

    } else {
        exibirExercicio($ExercNome, $ExercArquivo, $ExercTipo);
    
    }

    function exibirExercicio($exerc_nome, $exerc_arquivo, $exerc_tipo) {

        if (file_exists("../../app/assets/exercicios/$exerc_arquivo")) {

            if ($exerc_tipo == 'A') {

                $str_obj = "<object type='application/x-shockwave-flash' width='90%' height='90%' data='http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/$exerc_arquivo'>
                            <param name='movie' value='http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/$exerc_arquivo'></param>
                            </object>";

                //$str_obj='http://localhost:8080/academia_arquivos/animacoes/$exerc_arquivo';

            } else if ($exerc_tipo == 'V') {
                //$str_obj = "<video width='90%' height='90%' autoplay muted>
                //            <source src='http://localhost:8080/academia_arquivos/animacoes/$exerc_arquivo'>
                //            </video>";
                $str_obj = "<div style='width=200px;margin-left: auto;margin-right: auto;'>
                            <video width='60%' height='60%' muted>
                            <source src='http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/$exerc_arquivo' type='video/mp4'>
                            <param name='wmode' value='opaque' />
                            <embed src='http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/$exerc_arquivo' wmode='opaque'>
                            <param name='wmode' value='opaque' />
                            </embed>
                            </video>
                            </div>";            

            } else {
                $arr_img = getimagesize('http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/'.$exerc_arquivo);
                $resize = ($arr_img[0] > $arr_img[1]) ? 'width':'height';
                $str_obj = "<div ><img style='$resize: 100%; object-fit: contain; margin-bottom:10px' src='http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/$exerc_arquivo'></div>";
                //list($width, $height, $type, $attr) = getimagesize('http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/'.$exerc_arquivo);
                
            }

            echo $str_obj; //"<div style='padding:1; position:relative; overflow:hidden; clear:both; background-color: green'>" .  $str_obj . "</div>";
            
        } else {
            echo "<div></div>";

        }

    }


?>
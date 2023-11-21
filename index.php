<?php

require_once 'conexao.php';

$json = file_get_contents("response05.json");
$data = json_decode($json);

$dados = $data->data;

foreach ($dados as $key => $dado) {

    $cpfClear = str_replace(".", "", str_replace("-", "", $dado->pessoaCpf));

    $nome = $dado->pessoaNome;
    $cpf = $cpfClear;
    $logradouro = $dado->pessoaEndereco->logradouro;
    $numero = $dado->pessoaEndereco->numero;
    $complemento = $dado->pessoaEndereco->complemento;
    $bairro = $dado->pessoaEndereco->bairro;
    $cep = $dado->pessoaEndereco->cep;
    $cidade = $dado->pessoaEndereco->cidade;
    $estado = $dado->pessoaEndereco->estado;
    $dataEnvioAssistencia = $dado->dataEnvioAssistencia;
    $dataSolicitacaoCancelamento = ($dado->dataSolicitacaoCancelamento == null || $dado->dataSolicitacaoCancelamento == '') ? null : $dado->dataSolicitacaoCancelamento;
    $dataCancelamento = ($dado->dataCancelamento == null || $dado->dataCancelamento == '') ? null : $dado->dataCancelamento;
    $situacao = ($dado->situacao == 'Ativo') ? 1 : 3;

    $sqlGetcpf = "SELECT cpf FROM lifes WHERE cpf = '$cpf'";
    $result = mysqli_query($conn, $sqlGetcpf);

    if (mysqli_num_rows($result) > 0) {
        echo "CPF já cadastrado: " . $cpf . "<br>";
    } else {
        $sqlPostAddress = "INSERT INTO `address` (
            `id`,
            `zip_code`,
            `address`,
            `number`,
            `complement`,
            `district`,
            `city`,
            `uf`
        ) VALUES (
            uuid(),
            '$cep',
            '$logradouro',
            '$numero',
            '$complemento',
            '$bairro',
            '$cidade',
            '$estado'
        )";
        $resultPostAddress = mysqli_query($conn1, $sqlPostAddress);

        $sqlGetIdAddress = "SELECT id FROM `address` WHERE zip_code = '$cep' AND `number` = '$numero'";
        $resultGetIdAddress = mysqli_query($conn1, $sqlGetIdAddress);
        $row = mysqli_fetch_assoc($resultGetIdAddress);

        $idAddress =  $row['id'];

        echo $idAddress . "<br>";

        $sqlPostLife = "INSERT INTO `lifes` (
            `id`,
            `name`,
            `cpf`,
            `addressId`,
            `clientId`,
            `userId`,
            `dateSendAssistance`,
            `dateRequestCancel`,
            `dateCancel`,
            `situation`
        ) VALUES (
            uuid(),
            '$nome',
            '$cpf',
            '$idAddress',
            '15bccc3c-7f36-11ee-9691-525400cca73c',
            '21743936-da34-4286-b3a9-bd46d537b6b3',
            '$dataEnvioAssistencia',
            '$dataSolicitacaoCancelamento',
            '$dataCancelamento',
            '$situacao'
        )";
        $resultPostLife = mysqli_query($conn1, $sqlPostLife);
    }
}
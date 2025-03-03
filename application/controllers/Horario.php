<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horario extends CI_Controller {
    //Atributos privados da classe
    private $codigo;
    private $descricao;
    private $horaInicial;
    private $horaFinal;
    private $estatus;

    //Getters dos atributos
    public function getCodigo(){
        return $this->codigo;
    }

    public function getHoraInicial(){
        return $this->horaInicial;
    }

    public function getHoraFinal(){
        return $this->horaFinal;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getEstatus(){
        return $this->estatus;
    }

    //Setters dos atributos
    public function setCodigo($codigoFront){
        $this->codigo = $codigoFront;
    }

    public function setHoraInicial($horaInicialFront){
        $this->horaInicial = $horaInicialFront;
    }

    public function setHoraFinal($horaFinalFront){
        $this->horaFinal = $horaFinalFront;
    }

    public function setDescricao($descricaoFront){
        $this->descricao = $descricaoFront;
    }

    public function setEstatus($estatusFront){
        $this->estatus = $estatusFront;
    }

    public function inserir(){
        //Horário Inicial e Horário Final
        //recebido via json e colocados em variaveis
        //retornos possíveis:
        //1 - horario cadastrado corretamente (Banco)
        //2 - faltou informar a descricao (frontend)
        //3 - faltou informar o horario inicial (frontend)
        //4 - faltou informar o horario final (frontend)
        //5 - horario ja cadastrado no sistema
        //5 - houve algum problema no insert da tabela (banco)

        try {
            //dados recebido via json
            //e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            //Array com os dados que deverão vir do front
            $lista = array(
                "descricao" => '0',
                "horaInicial" => '0',
                "horaFinal" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this -> setDescricao($resultado->$descricao);
                $this -> setHoraInicial($resultado->$horaInicial);
                $this -> setHoraFinal($resultado->$horaFinal);

                //Faremos uma validacao para sabermos se todos os dados foram enviados
                if (trim($this->getDescricao()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Descricao não informada.');
                }elseif (trim($this->getHoraInicial()) == '') {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'Hora inicial não informada.');
                }elseif (trim($this->getHoraFinal()) == '') {
                    $retorno = array('codigo' => 4,
                                    'msg' => 'Hora Final não informada.');
                }else{
                    //Realizo a instância da Model
                    $this->load->model('M_horario');

                    //atributo $retorno recebe array com informações
                    //da validacao do acesso
                    $retorno = $this->M_horario-> inserir($this->getDescricao(),
                                                        $this->getHoraInicial(),
                                                        $this->getHoraFinal());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o método de login. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array('codigo' => 0,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                                $e -> getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);

    }
}
<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Mapa extends CI_Controller
{
    //Atributos privados da classe
    private $codigo;
    private $dataReserva;
    private $codigo_sala;
    private $codigo_horario;
    private $codigo_turma;
    private $codigo_professor;
    private $estatus;

    //Getters dos atributos
    public function getCodigo(){
        return $this->codigo;
    }
    
    public function getDataReserva(){
        return $this->dataReserva;
    }

    public function getCodigoSala(){
        return $this->codigo_sala;
    }

    public function getCodigoHorario(){
        return $this->codigo_horario;
    }

    public function getCodigoTurma(){
        return $this->codigo_turma;
    }

    public function getProfessor(){
        return $this->codigo_professor;
    }

    public function getEstatus(){
        return $this->estatus;
    }

    //setters dos atributos
    public function setCodigo($codigoFront){
        $this -> codigo = $codigoFront;
    }

    public function setDataReserva($dataReservaFront){
        $this -> dataReserva = $dataReservaFront;
    }

    public function setCodigoSala($codigo_salaFront){
        $this -> codigo_sala = $codigo_salaFront;
    }

    public function setCodigoHorario($codigo_horarioFront){
        $this -> codigo_horario = $codigo_horarioFront;
    }

    public function setCodigoTurma($codigo_turmaFront){
        $this -> codigo_turma = $codigo_turmaFront;
    }

    public function setProfessor($codigo_professorFront){
        $this -> codigo_professor = $codigo_professorFront;
    }

    public function setEstatus($estatusFront){
        $this -> estatus = $estatusFront;
    }

    public function inserir(){
        //Data de reserva, codigo da sala, codigo do horario, codigo da turma
        //recebidos via json e colcoados em variaveis
        //retornos possiveis 
        //1 - reserva cadastrada corretamente (banco)
        //2 - Faltou informar a Data (frontend)
        //3 - Faltou informar a Sala (frontend)
        //4 - Faltou informar o Horario (frontend)
        //5 - Faltou informar a Turma (frontend)
        //6 - faltou informar o professor (frontend)
        //7 - Agendamento já cadastrado no sistema
        //8 - Agendamento desativado no sistema
        //9 - houve algum problema no insert da tabela (banco)

        try {
            //Dados recebidos via json e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);


            //Array com os dados que deverão vir do front
            $lista = array(
                "dataReserva" => '0',
                "codSala" => '0',
                "codHorario" => '0',
                "codTurma" => '0',
                "codProfessor" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this -> setDataReserva($resultado -> dataReserva);
                $this -> setCodigoSala($resultado -> codSala);
                $this -> setCodigoHorario($resultado -> codHorario);
                $this -> setCodigoTurma($resultado -> codTurma);
                $this -> setProfessor($resultado -> codProfessor);

                //Faremos uma validacao para sabermos se todos os dados foram enviados
                if (trim($this -> getDataReserva()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Data não informada.');
                } elseif (trim($this -> getCodigoSala()) == '') {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'Sala não informada.');
                } elseif (trim($this -> getCodigoHorario()) == '') {
                    $retorno = array('codigo' => 4,
                                    'msg' => 'Horario não informado.');
                } elseif (trim($this -> getCodigoTurma()) == '') {
                    $retorno = array('codigo' => 5,
                                    'msg' => 'Turma não informada.');
                } elseif (trim($this -> getProfessor()) == '') {
                    $retorno = array('codigo' => 6,
                                    'msg' => 'Professor não informado.');
                } else {
                    //Realizo a instancia da Model
                    $this -> load -> model('M_mapa');

                    //atributo $retorno recebe array com informações
                    //da validacao do acesso
                    $retorno = $this -> M_mapa -> inserir($this -> getDataReserva(),
                                                        $this -> getCodigoSala(),
                                                        $this -> getCodigoHorario(),
                                                        $this -> getCodigoTurma(),
                                                        $this -> getProfessor());
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

    public function consultar(){
        //Codigo, data de reserva, codigo da sala, codigo de horario, codigo da turma
        //recebidos via json e colocados em variaveis
        //retornos possiveis
        //1 - dados consultados corretamente (banco)
        //6 - dados não encontrados (banco)

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //array com os dados que deverão vir do front
            $lista = array(
                "codigo" => '0',
                "dataReserva" => '0',
                "codSala" => '0',
                "codHorario" => '0',
                "codTurma" => '0',
                "codProfessor" => '0'
            );

            if (verificarParam($resultado, $lista) == 1){
                //Fazendo os setters
                $this->setCodigo($resultado->codigo);
                $this->setDataReserva($resultado->dataReserva);
                $this->setCodigoSala($resultado->codSala);
                $this->setCodigoHorario($resultado->codHorario);
                $this->setCodigoTurma($resultado->codTurma);
                $this->setProfessor($resultado->codProfessor);

                //realizo a instancia da model
                $this->load->model('M_mapa');

                //atributo $retorno recebe array com informacoes da consulta dos dados
                $retorno = $this->M_mapa->consultar($this->getCodigo(),
                                                    $this->getDataReserva(),
                                                    $this->getCodigoSala(),
                                                    $this->getCodigoHorario(),
                                                    $this->getCodigoTurma(),
                                                    $this->getProfessor());
            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o método de login. Verifique.'
                );
            }

        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                $e -> getMessage()
            );
        }
    
        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function alterar(){
        //Codigo, data de reserva, codigo da sala, codigo de horario, codigo da turma
        //recebidos via json e colocados em variaveis
        //retornos possiveis
        //1 - Reserva alterada corretamente (banco)
        //2 - faltou informar o codigo de reserva (frontend)
        //3 - faltou informar data (frontend)
        //4 - faltou informar a sala (frontend)
        //5 - faltou informar o horario (frontend)
        //6 - faltou informar a turma (frontend)
        //7 - faltou informar o professor (frontend)
        //8 - agendamento nao cadastrado no sistema
        //9 - houve algum problema no salvamento dos dados (banco)

        try {
            //Dados recebidos via Json
            //e colocados em atributo
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverao vir do front
            $lista = array(
                "codigo" => '0',
                "dataReserva" => '0',
                "codSala" => '0',
                "codHorario" => '0',
                "codTurma" => '0',
                "codProfessor" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this -> setCodigo($resultado -> codigo);
                $this -> setDataReserva($resultado -> dataReserva);
                $this -> setCodigoSala($resultado -> codSala);
                $this -> setCodigoHorario($resultado -> codHorario);
                $this -> setCodigoTurma($resultado -> codTurma);
                $this -> setProfessor($resultado -> codProfessor);

                //faremos uma validacao para sabermos se todos os dados foram enviados
                if (trim($this -> getCodigo()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Codigo não informado.');

                } elseif (trim($this -> getDataReserva()) == '') {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'Data não informada.');

                } elseif (trim($this -> getCodigoSala()) == '') {
                    $retorno = array('codigo' => 4,
                                    'msg' => 'Sala não informada.');

                } elseif (trim($this -> getCodigoHorario()) == '') {
                    $retorno = array('codigo' => 5,
                                    'msg' => 'Horario não informado.');
                                  
                } elseif (trim($this -> getCodigoTurma()) == '') {
                    $retorno = array('codigo' => 6,
                                    'msg' => 'Turma não informada.');

                } elseif (trim($this -> getProfessor()) == '') {
                    $retorno = array('codigo' => 7,
                                    'msg' => 'Professor não informado.');
                
                } else {
                    //Realizo a instancia da Model
                    $this -> load -> model('M_mapa');

                    //Atributos $retorno recebe array com informacoes
                    //da validacao do acesso
                    $retorno = $this -> M_mapa -> alterar($this -> getCodigo(),
                    $this -> getDataReserva(),
                    $this -> getCodigoSala(),
                    $this -> getCodigoHorario(),
                    $this -> getCodigoTurma(),
                    $this -> getProfessor());
                                                        
                }
            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o metodo de login. Verifique.');
            }

        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                $e -> getMessage()
            );
        }
    
        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function desativar(){
        //usuario recebido via json e colcoado em variavel
        //retornos possiveis
        //1 - agendamento desativado corretamente (banco)
        //2 - Codigo do curso nao informado
        //5 - Houve algum problema n desativacao do horario
        //6 - dados nao encontrados (banco)

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverao vir do front
            $lista = array(
                "codigo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1){
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);

                //Fazendo os setters
                $this -> setCodigo($resultado -> codigo);
                //validacao para o usuario que nao deverao ser branco
                if (trim($this -> getCodigo()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Codigo do agendamento nao informado.');
                } else {
                    //Realizo a instancia da model
                    $this -> load ->model('M_mapa');

                    //atributo $retorno recebe array com informacoes
                    $retorno = $this -> M_mapa -> desativar($this -> getCodigo());
                }
            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o metodo de login. Verifique.'
                );
            }
        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                $e -> getMessage()
            );
        }
    
        //Retorno no formato JSON
        echo json_encode($retorno);
    }
}
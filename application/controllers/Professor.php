<?php

defined('BASEPATH') or exit('no direct script acess allowed');

class Professor extends CI_Controller {
    //Atributos privados da classe
    private $codigo;
    private $nome;
    private $cpf;
    private $tipo;
    private $estatus;

    //Getters dos atributos
    public function getCodigo(){
        return $this->codigo;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getCpf(){
        return $this->cpf;
    }

    public function getTipo(){
        return $this->tipo;
    }

    public function getEstatus(){
        return $this->estatus;
    }

    //Setters dos atributos
    public function setCodigo($codigoFront){
        $this->codigo = $codigoFront;
    }

    public function setNome($nomeFront){
        $this->nome = $nomeFront;
    }

    public function setCpf($cpfFront){
        $this->cpf = $cpfFront;
    }

    public function setTipo($tipoFront){
        $this->tipo = $tipoFront;
    }

    public function setEstatus($estatusFront){
        $this->estatus = $estatusFront;
    }

    public function inserir(){
        //Codigo, Nome, CPf e Tipo
        //recebidos via JSON e colocados em variaveis
        //retornos possívei:
        // 1 - Professor cadastrado corretamente (Banco)
        // 2 - Faltou informar o Nome (FrontEnd)
        // 3 - Faltou informar o CPF (FrontEnd)
        // 4 - Faltou informar o Tipo (FrontEnd)
        // 5 - Professor já cadastrado no sistema
        // 6 - Houve algum problema no insert da tabela (Banco)

        try {
            //dados recebido via json
            //e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            //Array com os dados que deverão vir do front
            $lista = array(
                "nome" => '0',
                "cpf" => '0',
                "tipo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this -> setNome($resultado->nome);
                $this -> setCpf($resultado->cpf);
                $this -> setTipo($resultado->tipo);

                //Faremos uma validacao para sabermos se todos os dados foram enviados
                if (trim($this->getNome()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Nome não informado.');
                }elseif (trim($this->getCpf()) == '') {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'CPF não informado.');
                }elseif (trim($this->getTipo()) == '') {
                    $retorno = array('codigo' => 4,
                                    'msg' => 'Tipo não informado.');
                }else{
                    //Realizo a instância da Model
                    $this->load->model('M_professor');

                    //atributo $retorno recebe array com informações
                    //da validacao do acesso
                    $retorno = $this->M_professor-> inserir($this->getNome(),
                                                        $this->getTipo(),
                                                        $this->getCpf());
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
        //Codigo, Nome, CPF e Tipo
        //recebidos via JSON e colocados
        //em variaveis
        //retornos possiveis
        //1 - dados consultados corretamente (Banco)
        //6 - dados não encontrados (Banco)

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Fazendo os setters
            $this->setCodigo($resultado->codigo);
            $this->setNome($resultado->nome);
            $this->setCpf($resultado->cpf);
            $this->setTipo($resultado->tipo);

            //array com os dados que deverão vir do front
            $lista = array(
                "codigo" => '0',
                "nome" => '0',
                "cpf" => '0',
                "tipo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {

                //realizo a instancia da model
                $this->load->model('M_professor');

                //atributo $retorno recebe array com informacoes da consulta dos dados
                $retorno = $this->M_professor->consultar($this->getCodigo(),
                                                    $this->getNome(),
                                                    $this->getCpf(),
                                                    $this->getTipo());
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o método de login. Verifique.'
                );
            }
        }catch (Exception $e) {
            $retorno = array('codigo' => 0,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                                $e -> getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function alterar(){
        //Codigo, Nome, Cpf e Tipo
        //recebidos via JSON e colocados
        //em variaveis
        //Retornos possíveis
        //1 - Dados alterados corretamente (Banco)
        //2 - Codigo não informado ou zerado
        //3 - Pelo menos um parametro deve ser passado
        //5 - Dados nao encontrados (Banco)

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //array com os dados que deverão vir do front
            $lista = array(
                "codigo" => '0',
                "nome" => '0',
                "cpf" => '0',
                "tipo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this->setCodigo($resultado->codigo);
                $this->setNome($resultado->nome);
                $this->setCpf($resultado->cpf);
                $this->setTipo($resultado->tipo);

                //codigo é obrigatorio
                if (trim($this->getCodigo() == '')) {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Codigo não informado.');
                    //Nome, senha ou tipo de usuario 
                    //pelo menos 1 deles precisa ser informado.
                }elseif (trim($this->getNome() == '') && trim($this->getTipo() == '')
                        && trim($this->getCpf() == '')) {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'Pelo menos um parametro precisa ser 
                                    passado para atualizacao');
                }else{
                    //realizo a instancia da model
                    $this->load->model('M_professor');
                    
                    //atributo $retorno recebe array com informacoes
                    //da alteracao dos dados
                    $retorno = $this->M_professor->alterar($this->getCodigo(),
                                                        $this->getNome(),
                                                        $this->getCpf(),
                                                        $this->getTipo());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o método de login. Verifique.'
                );
            }
        }catch (Exception $e) {
            $retorno = array('codigo' => 0,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                                $e -> getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno);
    }

    public function desativar(){
        //usuario recebido via json e colocado em variavel
        //retornos possíveis:
        //1 - horario desativado corretamente (Banco)
        //2 - Codigo do horario nao informado
        //5 - Houve algum problema na desativacao do horario
        //6 - dados nao encontrados (banco)
        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //array com os dados que deverao vir do front 
            $lista = array(
                "codigo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);

                //fazendo os setters
                $this->setCodigo($resultado->codigo);

                //codigo é obrigatorio
                if (trim($this->getCodigo() == '')) {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Codigo nao informado.');
                }else{
                    //realizo a instancia da model
                    $this->load->model('M_professor');

                    //atributo $retorno recebe array com informacoes
                    $retorno = $this->M_professor->desativar($this->getCodigo());
                }
            }else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindo do frontend nao representam o metodo de login. Verifique.'
                );
            }

        }catch (Exception $e) {
            $retorno = array('codigo' => 0,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                                $e -> getMessage());
        }

        //Retorno no formato JSON
        echo json_encode($retorno); 
    }
}
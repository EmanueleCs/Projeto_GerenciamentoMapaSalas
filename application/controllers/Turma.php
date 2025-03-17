<?php 
defined('BASEPATH') OR exit('NO direct script acess allowed');

class Turma extends CI_Controller{
    //Atributos privados da classe
    private $codigo;
    private $descricao;
    private $capacidade;
    private $dataInicio;
    private $estatus;

    //Getters dos atributos
    public function getCodigo(){
        return $this->codigo;
    }

    public function getDataInicio(){
        return $this->dataInicio;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getCapacidade(){
        return $this->capacidade;
    }

    public function getEstatus(){
        return $this->estatus;
    }

    //Setters dos atributos
    public function setCodigo($codigoFront){
        $this->codigo = $codigoFront;
    }

    public function setDataInicio($dataInicio){
        $this->dataInicio = $dataInicio;
    }

    public function setDescricao($descricaoFront){
        $this->descricao = $descricaoFront;
    }

    public function setCapacidade($capacidadeFront){
        $this->capacidade = $capacidadeFront;
    }

    public function setEstatus($estatusFront){
        $this->tipoUsuario = $estatusFront;
    }

    public function inserir(){
        //descricao, e capacidade
        //recebidos via JSON e colocados em variáveis
        //retornos possíveis:
        //1 - Turma cadastrada corretamente (banco)
        //2 - faltou informar a descricao (frontend)
        //3 - faltou informar a capacidade (frontend)
        //4 - Faltou informar a data de inicio da turma (frontend)
        //5 - turma ja cadastrada no sistema
        //6 - houve algum problema no insert da tabela (banco)

        try {
            //dados recebidos via json e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            $lista = array(
                "descricao" => '0',
                "capacidade" => '0',
                "dataInicio" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this -> setDescricao($resultado -> descricao);
                $this -> setCapacidade($resultado -> capacidade);
                $this -> setDataInicio($resultado -> dataInicio);

                //Faremos uma validação para sabermos se todos os dados foram enviados
                if (trim($this -> getDescricao()) == ''){
                    $retorno = array('codigo' => 2,
                                     'msg' => 'Descrição não informada.');

                }elseif (trim($this -> getCapacidade()) == '') {
                    $retorno = array('codigo' => 3, 'msg' => 'Capacidade não informada.');

                }elseif (trim($this -> getDataInicio()) == '') {
                    $retorno = array('codigo' => 4, 'msg' => 'Data de início não informada.');

                }else {
                    //Reaalizo a instânca da Model
                    $this -> load -> model('M_turma');

                    //Atributos $retorno recebe array com informações da validação do acesso
                    $retorno = $this -> M_turma -> inserir ($this -> getDescricao(),
                                                            $this -> getCapacidade(),
                                                            $this -> getDataInicio());
                };
            }else{
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de Inserção. Verifique.'
                );
            }

        }catch(Exception $e) {
            $retorno = array('codigo' => 0,
                                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->',
                                $e -> getMessage());
        }
        //Retorno no formato JSON
        echo json_encode($retorno);

    }

    public function consultar(){
        //Código, descrição e capacidade 
        //recebidos via JSON e colocados
        //em variáveis
        //Retornos possíveis:
        // 1 - Dados consultados corretamente (Banco)
        // 6 - Dados não encontrados (Banco)
        try{
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0',
                "descricao" => '0',
                "capacidade" => '0', 
                "dataInicio" => '0'
            );

            if (verificarParam($resultado, $lista) == 1){
                $this -> setCodigo($resultado -> codigo);
                $this -> setDescricao($resultado -> descricao);
                $this -> setCapacidade($resultado -> capacidade);
                $this -> setDataInicio($resultado -> dataInicio);
                
                //Realizo a instância da Model
                $this -> load->model('M_turma');

                //Atributos $retorno recebe array com informações
                // da consulta dos dados
                $retorno = $this -> M_turma -> consultar($this->getCodigo(),
                                                        $this->getDescricao(),
                                                        $this->getCapacidade(),
                                                        $this->getDataInicio());
            }else{
                $retorno = array('codigo' => 99,
                                'msg' => 'Os campos vindos do Frontend não 
                                representam o método de Consulta. Verifique.'
                            );
            }
        }catch(Exception $e){
            $retorno = array('codigo' => 0,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', 
                            $e->getMessage());
        }

        //retorno no formato json
        echo json_encode($retorno);
    }

    public function alterar(){
        //Código, descrição e capacidade
        //recebidos via JSON e colocados em variaveis
        //retornos possíveis:
        //1 - dados alterados corretamente (banco)
        //2 - codigo não informado ou zerado
        //3 - pelo menos um parametro deve ser passado
        //5 - dados não encontrados (banco)

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0',
                "descricao" => '0',
                "capacidade" => '0',
                "dataInicio" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this -> setCodigo($resultado->codigo);
                $this -> setDescricao($resultado->descricao);
                $this -> setCapacidade($resultado -> capacidade);
                $this -> setDataInicio($resultado -> dataInicio);

                //Validacao para passagem de atributo ou campo vazio
                if (trim($this->getCodigo()) == '') {
                    $retorno = array(
                        'codigo' => 2,
                        'msg' => 'Código não informado'
                    );
                    // Nome senha ou tipo usuario, pelo menos 1 deles precisa ser informado
                }elseif (trim($this->getDescricao()) == '' && trim($this->getCapacidade()) == '' &&
                            trim($this -> getDataInicio()) == '') {

                                $retorno = array('codigo' => 3,
                                'msg' => 'Pelo menos um parâmetro precisa ser passado para atualização');
                }else{
                    //Realizo a instância da Model
                    $this -> load->model('M_turma');

                    //Atributo $retorno recebe array com informações
                    // da alteração dos dados
                    $retorno = $this -> M_turma-> alterar($this -> getCodigo(),
                                                $this -> getDescricao(),
                                                $this -> getCapacidade(),
                                                $this -> getDataInicio());
                }
            }else{
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de Alteração.
                            Verifique.'
                );
            }

        } catch (Exception $e) {
            $retorno = array('codigo' => 0,
                            'msg' => 'ATENÇÃO: O seguintee erro aconteceu ->',
                            $e -> getMessage());
        }
        //retorno no formato JSON
        echo json_encode($retorno);
    }

    public function desativar(){
        //Codigo da turma recebido via JSON e colocado em variável
        //Retorno possíveis:
        //1 - Turma desativada corretamente (Banco)
        //2 - Código da turma não informado
        //5 - Houve algum problema na desativação da sala
        //6 - Dados não encontrados (Banco)

        try{
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            //Array com os dados que deverão vir do Front
            $lista = array(
                "codigo" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);

                //Fazendo os setters
                $this -> setCodigo($resultado-> codigo);

                //Validacao para do usuário que não haverá ser branco
                if (trim($this->getCodigo() == '')) {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Código não informado');
                }else{
                    //Realizo a instância da Mode
                    $this -> load -> model('M_turma');

                    //Atributo $retorno recebe array com informações
                    $retorno = $this->M_turma->desativar($this -> getCodigo());
                }

            }else{
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do FrontEnd não representam o método de login, Verifique.'
                );
            }
        }catch(Exception $e){
            $retorno = array('codigo' => 0,
                            'msg' => 'ATENÇÃO: O seguintee erro aconteceu ->',
                            $e -> getMessage());
        }
        //retorno no formato JSON
        echo json_encode($retorno);
    }
}
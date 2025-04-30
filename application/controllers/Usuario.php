<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller{
    //Atributos privados da classe
    private $idUsuario;
    private $nome;
    private $email;
    private $usuario;
    private $senha;

    //getters dos atributos
    public function getIdUsuario(){
        return $this->idUsuario;
    }
    
    public function getNome(){
        return $this->nome;
    }
    
    public function getEmail(){
        return $this->email;
    }
    
    public function getUsuario(){
        return $this->usuario;
    }
    
    public function getSenha(){
        return $this->senha;
    }
    
    //setters dos atributos
    public function setIdUsuario($idUsuarioFront){
        $this->idUsuario = $idUsuarioFront;
    }

    public function setNome($nomeFront){
        $this->nome = $nomeFront;
    }
    
    public function setEmail($emailFront){
        $this->email = $emailFront;
    }
    
    public function setUsuario($usuarioFront){
        $this->usuario = $usuarioFront;
    }
    
    public function setSenha($senhaFront){
        $this->senha = $senhaFront;
    }
    
    public function inserir(){
        //Nome, Usuario e senha
        //recebidos via JSON e colocados em variaveis
        //retornos possíveis
        //1 - Usuario cadastrado corretamente (banco)
        //2 - Faltou informar o nome (FrontEnd)
        //3 - Faltou informar o email (FrontEnd)
        //4 - Faltou informar o usuario (FrontEnd)
        //5 - Faltou informar a senha (FrontEnd)

        try {
            //usuario e senha recebidos via json e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do Front
            $lista = array(
                "nome" => '0',
                "email" => '0',
                "usuario" => '0',
                "senha" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this->setNome($resultado->nome);
                $this->setEmail($resultado->email);
                $this->setUsuario($resultado->usuario);
                $this->setSenha($resultado->senha);

                //Faremos uma validacao para sabermos se todos os dados foram enviados
                if (trim($this->getNome()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Nome não informado.');
                } elseif (trim($this->getEmail()) == '') {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'Email não informado.');
                } elseif (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $retorno = array('codigo' => 6,
                                    'msg' => 'Email em formato invalido');
                } elseif (trim($this->getUsuario()) == '') {
                    $retorno = array('codigo' => 4,
                                    'msg' => 'Usuario não informado.');
                } elseif (trim($this->getSenha()) == '') {
                    $retorno = array('codigo' => 5,
                                    'msg' => 'Senha não informada.');
                }else {
                    //realizo a instancia da model
                    $this->load->model('M_usuario');

                    //atributo $retorno recebe array com informações da validacao do acesso
                    $retorno = $this->M_usuario->inserir($this->getNome(),
                                                          $this->getEmail(),
                                                          $this->getUsuario(),
                                                          $this->getSenha());
                }

            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o metodo de inserir. Verifique.'
                );
            }

        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //retorno no formato JSON
        echo json_encode($retorno);
    }

    public function consultar(){
        //nome, comum e usuario
        //recebidos via json e colocados
        //em variaveis
        //Retornos possíveis
        //1 - dados consultados corretamente (banco)
        //6 - dados não encontrados (banco)

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do Front
            $lista = array(
                "nome" => '0',
                "email" => '0',
                "usuario" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //fazendo os setters
                $this-> setNome($resultado->nome);
                $this-> setEmail($resultado->email);
                $this-> setUsuario($resultado->usuario);

                //realizo a instância da Model
                $this->load->model('M_usuario');

                    //atributo $retorno recebe array com informações da validacao do acesso
                    $retorno = $this->M_usuario->consultar($this->getNome(),
                                                          $this->getEmail(),
                                                          $this->getUsuario());

            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o metodo de inserir. Verifique.'
                );
            }

        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //retorno no formato JSON
        echo json_encode($retorno);
    }

    public function alterar(){
        //idUsuario, nome, email e senha
        //recebidos via json e colocados em variaveis
        //retornos possíveis:
        //1 - Dados alterados corretamente (Banco)
        //2 - IdUsuario em Branco ou zerado
        //3 - Nenhum parametro de alteração informado
        //5 - Dados não encontrados (Banco)

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do Front
            $lista = array(
                "idUsuario" => '0',
                "nome" => '0',
                "email" => '0',
                "senha" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this->setIdUsuario($resultado->idUsuario);
                $this->setNome($resultado->nome);
                $this->setEmail($resultado->email);
                $this->setSenha($resultado->senha);

                if (trim($this->getIdUsuario()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Id do Usuario não informado.');
                  //Nome, senha ou email pelo menos 1 deles precisa ser informado
                } elseif (trim($this->getNome()) == '' &&
                          trim($this->getSenha()) == '' &&
                          trim($this->getEmail() == '')) {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'Pelo menos um parametro precisa 
                                        ser passado para atualizacao');

                //verificacao de email valido
                } elseif (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $retorno = array('codigo' => 6,
                                    'msg' => 'Email em formato invalido');
                }else {
                    //realizo a instancia da model
                    $this->load->model('M_usuario');

                    //atributo $retorno recebe array com informações da validacao do acesso
                    $retorno = $this->M_usuario->alterar($this->getIdUsuario(),
                                                          $this->getNome(),
                                                          $this->getEmail(),
                                                          $this->getSenha());
                }

            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o metodo de inserir. Verifique.'
                );
            }

        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //retorno no formato JSON
        echo json_encode($retorno);
    }

    public function desativar(){
        //Usuario recebido via json e colocado em variavel
        //retornos possíveis
        // 1 - usuario desativado corretamente(banco)
        // 2 - Usuario em branco
        // 3 - Usuario inexistente na base de dados
        // 4 - usuario ja desativado na base de dados

        try {
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do Front
            $lista = array(
                "idUsuario" => '0'
            );

            if (verificarParam($resultado, $lista) == 1){
                $json = file_get_contents('php://input');
                $resultado = json_decode($json);
                
                //fazendo os setters
                $this->setIdUsuario($resultado->idUsuario);
                
                //validacao para o usuario que nao devera ser branco
                if (trim($this->getIdUsuario()) == ''){
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Id do Usuario não informado.');
                }else{
                    //Realizo a instancia da model
                    $this->load->model('M_usuario');
                    
                    //atributo $retorno recebe array com informações da validacao do acesso
                    $retorno = $this->M_usuario->desativar($this->getIdUsuario());
                }

            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o metodo de inserir. Verifique.'
                );
            }

        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //retorno no formato JSON
        echo json_encode($retorno);
    }
    
    public function logar(){
        //Recebimento via JSON o Usuário e senha 
        //Retornos possíveis: 
        //1 - Usuário e senha validados corretamente (Banco) 
        //2 - Faltou informar o usuário (FrontEnd) 
        //3 - Faltou informar a senha (FrontEnd) 
        //4 - Usuário ou senha inválidos (Banco) 
        //5 - Usuário deletado Status (Banco) 
        //99 - Os campos vindos do FrontEnd não representam o método de login
        try {
            //usuario e senha recebidos via json
            //e colocados em atributos
            $json = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Array com os dados que deverão vir do Front
            $lista = array(
                "usuario" => '0',
                "senha" => '0'
            );

            if (verificarParam($resultado, $lista) == 1) {
                //Fazendo os setters
                $this->setUsuario($resultado->usuario);
                $this->setSenha($resultado->senha);

                if (trim($this->getUsuario()) == '') {
                    $retorno = array('codigo' => 2,
                                    'msg' => 'Usuario não informado.');
                } elseif (trim($this->getSenha()) == '') {
                    $retorno = array('codigo' => 3,
                                    'msg' => 'Senha não informada.');
                }else{
                    //Realizo a instancia da model
                    $this->load->model('M_usuario');
                    
                    //atributo $retorno recebe array com informações da validacao do acesso
                    $retorno = $this->M_usuario->validaLogin($this->getUsuario(),
                                                            $this->getSenha());
                }

            } else {
                $retorno = array(
                    'codigo' => 99,
                    'msg' => 'Os campos vindos do frontend não representam o metodo. Verifique.'
                );
            }

        } catch (Exception $e) {
            $retorno = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //retorno no formato JSON
        echo json_encode($retorno);
    }
}
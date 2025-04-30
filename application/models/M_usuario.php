<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_usuario extends CI_Model{
    public function inserir($nome, $email, $usuario, $senha){
        try {
            //verificar o status do usuario antes de fazer o insert
            $retornoUsuario = $this->validaUsuario($usuario);
            if ($retornoUsuario['codigo']==4) {
                //query de inserção dos dados
                $this->db->query("insert into tbl_usuario (nome, email, usuario, senha)
                                values('$nome', '$email', '$usuario', md5('$senha'))");
                //verificar se a inserção ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Usuario cadastrado corretamente.');
                }else{
                    $dados = array('codigo' => 2,
                                    'msg' => 'Houve algum problema na inserção na tabela usuário.');
                }
            } else{
                $dados = array(
                    'codigo' => $retornoUsuario['codigo'],
                    'msg' => $retornoUsuario['msg']
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //envia o array $dados com as informações tratadas acima pela estrutura de decisao if/else
        return $dados;
    }
    
    public function consultar($nome, $email, $usuario){
        //Funcao servirá para três tipos de consulta
        // * Para todos os usuários;
        // * Para um determinado usuario;
        // * Para nomes de usuarios;

        try {
            //query para consultar dados de acordo com os parametro passados
            $sql = "select id_usuario, nome, usuario, email
                    from tbl_usuario
                    where estatus != 'D'";

            if (trim($nome) != '') {
                $sql = $sql . "and nome like '%$nome%' ";
            }

            if (trim($email) != '') {
                $sql = $sql . "and email = '$email' ";
            }

            if (trim($usuario) != '') {
                $sql = $sql . "and usuario = '%$usuario%' ";
            }

            $retorno = $this->db->query($sql);

            //verificar se a consulta ocorrey com sucesso

            if ($retorno->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso',
                                'dados' => $retorno->result());
            } else {
                $dados = array('codigo' => 6,
                                'msg' => 'Dados não encontrados.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //envia o array $dados com as informações tratadas acima pela estrutura de decisao if/else
        return $dados;
    }

    public function alterar($idUsuario, $nome, $email, $senha){
        try {
            //verificar o status do usuario antes de fazer o update
            $retornoUsuario = $this-> validaIdUsuario($idUsuario);

            if ($retornoUsuario['codigo'] == 1) {
                //Inicio a query para atualização
                $query = "update tbl_usuario set ";

                //vamos comparar os itens
                if ($nome !== '') {
                    $query .= "nome = '$nome', ";
                }

                if ($email !== '') {
                    $query .= "email = '$email', ";
                }

                if ($senha !== '') {
                    $query .= "senha = md5('$senha'), ";
                }

                //termino a concatenação da query
                $queryFinal = rtrim($query, ", ") . " where id_usuario = $idUsuario";

                //Executo a query de atualização dos dados
                $this->db->query($queryFinal);
                
                //verificar se a atualizacao ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Usuario alterado corretamente.');
                } else {
                    $dados = array('codigo' => 2,
                                    'msg' => 'Houve algum problema na alteração na tabela usuário.');
                }
            } else{
                $dados = array(
                    'codigo' => $retornoUsuario['codigo'],
                    'msg' => $retornoUsuario['msg']
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //envia o array $dados com as informações tratadas acima pela estrutura de decisao if/else
        return $dados;
    }

    public function desativar($idUsuario){
        try {
            //verificar o status do usuario antes de fazer o update
            $retornoUsuario = $this->validaIdUsuario($idUsuario);

            if ($retornoUsuario['codigo'] == 1) {
                //query de atualizacao dos dados
                $this->db->query("update tbl_usuario set estatus = 'D'
                                 where id_usuario = $idUsuario");
                
                //verificar se a atualizacao ocorrey com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Usuario DESATIVADO corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 2,
                        'msg' => 'Houve algum problema na DESATIVAÇÃO do usuário.'
                    );
                }
            } else{
                $dados = array(
                    'codigo' => $retornoUsuario['codigo'],
                    'msg' => $retornoUsuario['msg']);
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //envia o array $dados com as informações tratadas acima pela estrutura de decisao if/else
        return $dados;
    }

    private function validaUsuario($usuario){
        try {
            //Atributo retorno recebe o resultado do select
            //sem status pois teremos que validar para verificar se está deletado virtualmente ou não

            $retorno = $this->db->query("select * from tbl_usuario 
                                            where usuario = '$usuario'");

            //verifica se a quantidade de linhas trazidas na consulta é superior a 0
            // vinculamos o resultado da query para tratarmos o resultado do status
            $linha = $retorno->row();

            if ($retorno->num_rows() == 0) {
                $dados = array(
                    'codigo'=> 4,
                    'msg' => 'Usuário não existe na base de dados.'
                );
            } else {
                if (trim($linha->estatus) == 'D') {
                    $dados = array('codigo' => 5,
                                    'msg' => 'Usuario DESATIVADO NA BASE DE DADOS, não pode ser utlizado!');
                } else {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Usuario correto.');
                }
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //envia o array $dados com as informações tratadas acima pela estrutura de decisao if/else
        return $dados;
    }

    private function validaIdUsuario($idUsuario){
        try {
            //atributo retorno recebe o resultado do select
            //sem status pois terremos que validar para verificar se está
            //deletado virtualmente ou não

            $retorno = $this->db->query("select * from tbl_usuario
                                            where id_usuario = $idUsuario");
            //verifica se a quantidade de linhas trazidas na consulta é superior a 0
            // vinculamos o resultado da query para tratarmos o resultado do status
            $linha = $retorno->row();

            if ($retorno->num_rows() == 0) {
                $dados = array(
                    'codigo'=> 4,
                    'msg' => 'Usuário não existe na base de dados.'
                );
            } else {
                if (trim($linha->estatus) == 'D') {
                    $dados = array('codigo' => 5,
                                    'msg' => 'Usuario DESATIVADO NA BASE DE DADOS!');
                } else {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Usuario correto.');
                }
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //envia o array $dados com as informações tratadas acima pela estrutura de decisao if/else
        return $dados;
    }

    public function validaLogin($usuario, $senha){
        try {
            /*atributo retorno recebe o resultado do select realizado na tabela
            de usuarios lembrando da funcao MD5() por causa da criptografia, e sem status pois teremos
            que validar para verificar se está deletado virtualmente ou não*/

            $retorno = $this->db->query("select * from tbl_usuario
                                            where usuario = '$usuario'
                                            and senha = md5('$senha')");
            //verifica se a quantidade de linhas trazidas na consulta é superior a 0
            // vinculamos o resultado da query para tratarmos o resultado do status
            $linha = $retorno->row();

            if ($retorno->num_rows() == 0) {
                $dados = array(
                    'codigo'=> 4,
                    'msg' => 'Usuário ou senha invalidos'
                );
            } else {
                if (trim($linha->estatus) == 'D') {
                    $dados = array('codigo' => 5,
                                    'msg' => 'Usuario DESATIVADO NA BASE DE DADOS!');
                } else {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Usuario correto.');
                }
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->', $e->getMessage()
            );
        }

        //envia o array $dados com as informações tratadas acima pela estrutura de decisao if/else
        return $dados;
    }
}
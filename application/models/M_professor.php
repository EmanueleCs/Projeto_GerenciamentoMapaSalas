<?php

defined('BASEPATH') or exit('no direct script acess allowed');

class M_professor extends CI_Model {
    public function inserir($nome, $tipo, $cpf){
        try {
            //verifico se o horario ja esta cadastrado
            $retornoConsulta = $this->consultaProfessorCpf($cpf);

            if ($retornoConsulta['codigo'] != 1) {
                //query de insercao dos dados
                $this->db->query("insert into tbl_professor (nome, tipo, cpf) 
                                values ('$nome','$tipo', '$cpf')");
                
                //verificar se a insercao ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Professor cadastrado corretamente.');
                }else{
                    $dados = array('codigo' => 6,
                                    'msg' => 'Houve algum problema na insercao na tabela de professor.');
                }
            }else{
                $dados = array('codigo' => 5,
                                'msg' => 'Professor ja cadastrado no sistema.');
            }

        }catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }          
        
        //envia o array $dados com as informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function consultaProfessorCpf($cpf){
        try {
            //Query para consultar dados de acordo com os parametros passados
            $sql = "select * from tbl_professor where cpf = '$cpf'";

            $retornoProfessor = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso
            if ($retornoProfessor->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso.');
            }else{
                $dados = array('codigo' => 6,
                                'msg' => 'Professor não encontrado.');
            }

        }catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }          
        
        //envia o array $dados com as informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function consultaProfessorCod($codigo){
        try {
            //Query para consultar dados de acordo com os parametros passados
            $sql = "select * from tbl_professor where codigo = '$codigo' and estatus = ''";

            $retornoProfessor = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso
            if ($retornoProfessor->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso.');
            }else{
                $dados = array('codigo' => 6,
                                'msg' => 'Professor não encontrado.');
            }

        }catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }          
        
        //envia o array $dados com as informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function consultar($codigo, $nome, $cpf, $tipo){
        try {
            //query para consultar dados de acordo com parametros passados
            $sql = "select * from tbl_professor where estatus = ''";

            if (trim($codigo) != '') {
                $sql = $sql . "and codigo = $codigo ";
            }

            if (trim($cpf) != '') {
                $sql = $sql . "and cpf = $cpf ";
            }

            if (trim($nome) != '') {
                $sql = $sql . "and nome like '%$nome%' ";
            }

            if (trim($tipo) != '') {
                $sql = $sql . "and hora_fim = $tipo ";
            }

            $sql = $sql . " order by nome ";
            $retorno = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                    'msg' => 'Consulta efetuada com sucesso.',
                                    'dados' => $retorno->result());
            }else {
                $dados = array('codigo' => 6,
                                'msg' => 'Professor não encontrado.');
            }
        }catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }          
        
        //envia o array $dados com as informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function alterar($codigo, $nome, $cpf, $tipo){
        try {
            //verifico se o professor ja está cadastrado
            $retornoConsulta = $this->consultaProfessorCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                //inicio a query para atualização
                $query = "update tbl_professor set ";

                //vamos comparar os itens
                if ($nome !== '') {
                    $query.= "nome = '$nome', ";
                }

                if ($cpf !== '') {
                    $query.= "cpf = '$cpf', ";
                }

                if ($tipo !== '') {
                    $query.= "tipo = '$tipo', ";
                }

                //termino a concatenação da query
                $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";

                //executo a query de atualizacao dos dados
                $this->db->query($queryFinal);

                //verificar se a atualizacao ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Professor atualizado corretamente.');
                }else{
                    $dados = array('codigo' => 6,
                                    'msg' => 'Houve algum problema na atualização na tabela de horario.');
                }
            }else{
                $dados = array('codigo' => 5,
                                    'msg' => 'Professor não cadastrado no sistema.');
            }
            
        } catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }          
        
        //envia o array $dados com as informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function desativar($codigo){
        try {
            //verifico se o horario ja esta cadastrado
            $retornoConsulta = $this->consultaProfessorCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                //query de atualização dos dados
                $this->db->query("update tbl_professor set estatus = 'D' where codigo = $codigo");

                //verificar se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Professor DESATIVADO corretamente');
                }else{
                    $dados = array('codigo' => 5,
                                    'msg' => 'Houve algum problema na DESATIVAÇÃO do Professor.');
                }
            }else {
                $dados = array('codigo' => 6,
                                'msg' => 'Professor não cadastrado no Sistema, não pode excluir.');
            }
        }catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }          
        
        //envia o array $dados com as informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }
}
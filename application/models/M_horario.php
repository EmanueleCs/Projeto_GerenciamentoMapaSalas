<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_horario extends CI_Model{
    public function inserir($descricao, $horaInicial, $horaFinal){
        try {
            //verifico se o horario ja esta cadastrado
            $retornoConsulta = $this->consultaHorario($descricao, $horaInicial, $horaFinal);

            if ($retornoConsulta['codigo'] != 1) {
                //query de insercao dos dados
                $this->db->query("insert into tbl_horario (descricao, hora_ini, hora_fim) 
                                values ('$descricao','$horaInicial', '$horaFinal')");
                
                //verificar se a insercao ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Horario cadastrado corretamente.');
                }else{
                    $dados = array('codigo' => 6,
                                    'msg' => 'Houve algum problema na insercao na tabela de horario.');
                }
            }else{
                $dados = array('codigo' => 5,
                                'msg' => 'Horario ja cadastrado no sistema.');
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
    
    private function consultaHorario($descricao, $horaInicial, $horaFinal){
        try {
            //query para consultar dados de acordo com os parametros passado
            $sql = "select * from tbl_horario 
                    where descricao = '$descricao' and hora_ini = '$horaInicial'
                            and hora_fim = '$horaFinal' and estatus = ''";

            $retornoHorario = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso
            if ($retornoHorario->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso.');
            }else{
                $dados = array('codigo' => 4,
                                'msg' => 'Horario não encontrado.');
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

    public function consultaHorarioCod($codigo){
        try {
            //query para consultar dados de acordo com parametros passados
            $sql = "select * from tbl_horario where codigo = $codigo and estatus = ''";

            $retornoHorario = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso
            if ($retornoHorario->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso.');
            }else{
                $dados = array('codigo' => 6,
                'msg' => 'Horario não encontrado.');
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

    public function consultar($codigo, $descricao, $horaInicial, $horaFinal){
        try {
            //query para consultar dados de acordo com parametros passados
            $sql = "select * from tbl_horario where estatus != 'D'";

            if (trim($codigo) != '') {
                $sql = $sql . "and codigo = $codigo ";
            }

            if (trim($horaInicial) != '') {
                $sql = $sql . "and hora_ini = $horaInicial ";
            }

            if (trim($descricao) != '') {
                $sql = $sql . "and descricao like '%$descricao%' ";
            }

            if (trim($horaFinal) != '') {
                $sql = $sql . "and hora_fim = $horaFinal ";
            }

            $retorno = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $linha = $retorno->row();

                if (trim($linha->estatus) == "D") {
                    $dados - array('codigo' => 7,
                                    'msg' => 'Horario desativado no sistema.');
                }else {
                    $dados - array('codigo' => 1,
                                    'msg' => 'Consulta efetuada com sucesso.',
                                    'dados' => $retorno->result());
                }
            }else{
                $dados = array('codigo' => 6,
                                'msg' => 'Horario não encontrado.');
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

    public function alterar($codigo, $descricao, $horaInicial, $horaFinal){
        try {
            //verifico se a sala ja está cadastrada
            $retornoConsulta = $this->consultaHorarioCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                //inicio a query para atualização
                $query = "update tbl_horario set ";

                //vamos comparar os itens
                if ($descricao !== '') {
                    $query.= "descricao = '$descricao', ";
                }

                if ($horaInicial !== '') {
                    $query.= "hora_ini = '$horaInicial', ";
                }

                if ($horaFinal !== '') {
                    $query.= "hora_fim = '$horaFinal', ";
                }

                //termino a concatenação da query
                $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";

                //executo a query de atualizacao dos dados
                $this->db->query($queryFinal);

                //verificar se a atualizacao ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Horario atualizado corretamente.');
                }else{
                    $dados = array('codigo' => 5,
                                    'msg' => 'Houve algum problema na atualização na tabela de horario.');
                }
            }else{
                $dados = array('codigo' => 4,
                                    'msg' => 'Horario não cadastrado no sistema.');
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
            $retornoConsulta = $this->consultaHorarioCod($codigo);

            if ($retornoConsulta['codigo'] == 1) {
                //query de atualização dos dados
                $this->db->query("update tbl_horario set estatus = 'D' where codigo = $codigo");

                //verificar se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Horario DESATIVADO corretamente');
                }else{
                    $dados = array('codigo' => 4,
                                    'msg' => 'Houve algum problema na DESATIVAÇÃO do Horario.');
                }
            }else {
                $dados = array('codigo' => 3,
                                'msg' => 'Horario não cadastrado no Sistema, não pode excluir.');
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
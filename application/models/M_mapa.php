<?php

defined('BASEPATH') or exit ('No direct script access allowed');

include_once("M_sala.php");
include_once("M_horario.php");
include_once("M_turma.php");
include_once("M_professor.php");

class M_mapa extends CI_Model
{
    public function inserir($dataReserva, $codSala, $codHorario, $codTurma, $codProfessor){

        try {
            //verifico se o professor já está cadastrado
            $retornoConsulta = $this -> consultaReservaTotal($dataReserva, $codSala, $codHorario, 
                                                            $codTurma, $codProfessor);
            if ($retornoConsulta['codigo'] == 6 || $retornoConsulta['codigo'] == 7) {
                //chamo o objeto sala para validacao
                $salaObj = new M_sala();

                //chamar o metodo de verificacao 
                $retornoConsultaSala = $salaObj->consultar($codSala, '', '', '');

                if ($retornoConsultaSala['codigo'] == 1) {
                    //chamo o objeto sala para validacao
                    $horarioObj = new M_horario();

                    //chamar o metodo de verificacao 
                    $retornoConsultaHorario = $horarioObj->consultaHorarioCod($codHorario);

                    if ($retornoConsultaHorario['codigo'] == 1) {
                        //chamo o objeto sala para verificacao
                        $turmaObj = new M_turma();

                        //chamar o metodo de verificao
                        $retornoConsultaTurma = $turmaObj->consultaTurmaCod($codTurma);

                        if ($retornoConsultaTurma['codigo'] == 1) {
                            //chamo o objeto sala para verificacao
                            $professorObj = new M_professor();

                            //chamar o metodo de verificao
                            $retornoConsultaProfessor = $professorObj->consultaProfessorCod($codProfessor);

                            if ($retornoConsultaProfessor['codigo'] == 1) {
                                //query de insercao dos dados
                                $this->db->query("insert into tbl_mapa (datareserva, sala, codigo_horario, 
                                                codigo_turma, codigo_professor) 
                                                values ('" . $dataReserva . "', '" . $codSala . "', '" . $codHorario . "', 
                                                '" . $codTurma . "', '" . $codProfessor . "')");

                                //verificar se a insercao ocorreu com sucesso
                                if ($this->db->affected_rows() > 0) {
                                    $dados = array('codigo' => 1,
                                                    'msg' => 'Agendamento cadastrado corretamente.');

                                }else{
                                    $dados = array('codigo' =>  8,
                                                    'msg' => 'Agendamento já cadastrado no sistema.');
                                }

                            }else {
                                $dados = array(
                                    'codigo' => $retornoConsultaProfessor['codigo'],
                                    'msg' => $retornoConsultaProfessor['msg']
                                );
                            }

                        } else {
                            $dados = array(
                                'codigo' => $retornoConsultaTurma['codigo'],
                                'msg' => $retornoConsultaTurma['msg']
                            );
                        }

                    }else {
                        $dados = array(
                            'codigo' => $retornoConsultaHorario['codigo'],
                            'msg' => $retornoConsultaHorario['msg']
                        );
                    }
                } else {
                    $dados = array(
                        'codigo' => $retornoConsultaSala['codigo'],
                        'msg' => $retornoConsultaSala['msg']
                    );

                }
            } else {
                $dados = array(
                    'codigo' => 7,
                    'msg' => 'Agendamento já cadastrado no sistema'
                );
            }
        }catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }

        //envia o array $dados com informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    private function consultaReservaTotal($dataReserva, $codSala, $codHorario){

        try {
            //query para verificar a hora inicial e final daquele determinado horario
            $sql = "select * from tbl_horario
                    where codigo = $codHorario";

            $retornoHorario = $this->db->query($sql);

            if ($retornoHorario->num_rows() > 0) {
                $linhaHr = $retornoHorario->row();
                $horaInicial = $linhaHr -> hora_ini;
                $horaFinal = $linhaHr -> hora_fim;

                //query para consultar dados de acordo com parametros passados
                $sql = "select * from tbl_mapa m, tbl_horario h
                        where m.datareserva = '" . $dataReserva . "'
                            and m.sala = '" . $codSala . "'
                            and m.codigo_horario = h.codigo
                            and (h.hora_fim >= '" . $horaInicial . "'
                            and h.hora_ini <= '" . $horaFinal . "')";

                $retornoMapa = $this->db->query($sql);

                //verificar se a consulta ocorreu com sucesso
                if($retornoMapa->num_rows() > 0){
                    $linha = $retornoMapa->row();

                    if (trim($linha->estatus) == "D") {
                        $dados = array('codigo' => 7,
                                        'msg' => 'Agendamento desativado no sistema.');
                    } else {
                        $dados = array('codigo' => 1,
                                        'msg' => 'A data de' . $dataReserva . ' está ocupada para esta sala.');

                    }
                } else {
                    $dados = array(
                        'codigo' => 6,
                        'msg' => 'Reserva não encontrada.'
                    );
                }
            } else {
                $dados = array('codigo' => 6, 'msg' => 'Horário não encontrado.');
            }

        } catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }

        //envia o array $dados com informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function consultar($codigo, $dataReserva, $codSala, $codHorario, $codTurma, $codProfessor){
        try {
            $sql = "select m.codigo, date_format(m.datareserva, '%d-%m-%Y') datareservabra, datareserva,
                    m.sala, s.descricao descsala, m.codigo_horario,
                    h.descricao deshorario, m.codigo_turma, t.descricao descturma,
                    m.codigo_professor, p.nome nome_professor
                    from tbl_mapa m, tbl_professor p, tbl_horario h, tbl_turma t, tbl_sala s
                    where m.estatus = ''
                        and m.codigo_professor = p.codigo
                        and m.codigo_horario = h.codigo
                        and m.codigo_turma = t.codigo
                        and m.sala = s.codigo ";

            if (trim($codigo) != '') {
                $sql = $sql . "and m.codigo = $codigo ";
            }

            if (trim($dataReserva) != '') {
                $sql = $sql . "and m.datareserva = '" . $dataReserva . "' ";
            }

            if (trim($codSala) != '') {
                $sql = $sql . "and m.sala = $codSala ";
            }

            if (trim($codHorario) != '') {
                $sql = $sql . "and m.codigo_horario = $codHorario ";
            }

            if (trim($codTurma) != '') {
                $sql = $sql . "and m.codigo_turma = $codTurma ";
            }

            if (trim($codProfessor) != '') {
                $sql = $sql . "and m.codigo_professor = $codProfessor ";
            }

            $sql = $sql . " order by m.datareserva, h.hora_ini, m.codigo_horario, m.sala";

            $retorno = $this->db->query($sql);

            //verificar se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso.',
                                'dados' => $retorno->result());
            } else {
                $dados = array('codigo' => 6,
                                'msg' => 'Agendamentos não encontrados.');
            }

        } catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }

        //envia o array $dados com informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function alterar($codigo, $dataReserva, $codSala, $codHorario, $codTurma, $codProfessor){
        try {
            //verifico se o professor já está cadastrado
            $retornoConsultaCodigo = $this -> consultar(
                $codigo,
                "",
                "",
                "",
                "",
                ""
            );

            if ($retornoConsultaCodigo['codigo'] == 1) {
                //inicio a query para atualizacao
                $query = "update tbl_mapa set ";

                if ($dataReserva != "") {
                    $query .= "datareserva = '$dataReserva', ";
                }

                if ($codSala != "") {
                    //Chamo o objeto sala para validacao
                    $salaObj = new M_sala();

                    //chamar o metodo de verificacao
                    $retornoConsultaSala = $salaObj-> consultar($codSala, '', '', '');

                    if ($retornoConsultaSala['codigo'] == 1) {
                        $query .= "sala = $codSala, ";
                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaSala['codigo'],
                            'msg' => $retornoConsultaSala['msg']
                        );
                    }
                }

                if ($codHorario != "") {
                    //Chamo o objeto sala para validacao
                    $horarioObj = new M_horario();

                    //chamar o metodo de verificacao
                    $retornoConsultaHorario = $horarioObj-> consultaHorarioCod($codHorario);

                    if ($retornoConsultaHorario['codigo'] == 1) {
                        $query .= "codigo_horario = $codHorario, ";
                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaHorario['codigo'],
                            'msg' => $retornoConsultaHorario['msg']
                        );
                    }
                }

                if ($codTurma != "") {
                    //Chamo o objeto sala para validacao
                    $turmaObj = new M_turma();

                    //chamar o metodo de verificacao
                    $retornoConsultaTurma = $turmaObj-> consultaTurmaCod($codTurma);

                    if ($retornoConsultaTurma['codigo'] == 1) {
                        $query .= "codigo_turma = $codTurma, ";
                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaTurma['codigo'],
                            'msg' => $retornoConsultaTurma['msg']
                        );
                    }
                }

                if ($codProfessor != "") {
                    //Chamo o objeto sala para validacao
                    $professorObj = new M_professor();

                    //chamar o metodo de verificacao
                    $retornoConsultaProfessor = $professorObj-> consultaProfessorCod($codProfessor);

                    if ($retornoConsultaProfessor['codigo'] == 1) {
                        $query .= "codigo_professor = $codProfessor, ";

                        //termino a concatenação da query
                        $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";

                        //Executo a query de atualizacao dos dados
                        $this-> db -> query($queryFinal);
                        
                        //verificar se a atualização ocorreu com sucesso
                        if ($this->db->affected_rows() > 0) {
                            $dados = array(
                                'codigo' => 1,
                                'msg' => 'Agendamento alterado corretamente.'
                            );
                        } else {
                            $dados = array(
                                'codigo' => 8,
                                'msg' => 'Houve algum problema na alteracao na tabela de agendamento.'
                            );
                        }

                    } else {
                        $dados = array(
                            'codigo' => $retornoConsultaProfessor['codigo'],
                            'msg' => $retornoConsultaProfessor['msg']
                        );
                    }
                }
            } else {
                $dados = array(
                    'codigo' => 8,
                    'msg' => 'Agendamento não cadastrado no sistema.'
                );
            }

        } catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }

        //envia o array $dados com informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }

    public function desativar($codigo) {
        try {
            //verifico se o agendamento ja esta cadastrado 
            $retornoConsulta = $this -> consultar(
                $codigo,
                "",
                "",
                "",
                "",
                ""
            );

            if ($retornoConsulta['codigo'] == 1) {
                //Query de atualizacao dos dados
                $this->db->query("delete from tbl_mapa
                                 where codigo = $codigo");

                //verificar se a atualizacao ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Agendamento DESATIVADO corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 5,
                        'msg' => 'Houve algum problema na DESATIVAÇÃO do agendamento.'
                    );
                }
            } else {
                $dados = array(
                    'codigo' => 6,
                    'msg' => 'Agendamento não cadastrado no sistema, não pode excluir.'
                );
            }
        } catch (Exception $e) {
            $dados = array('codigo' => 00,
                            'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ',
                            $e->getMessage(), "\n");
        }

        //envia o array $dados com informacoes tratadas
        //acima pela estrutura de decisao if
        return $dados;
    }
}
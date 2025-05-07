<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Funcoes extends CI_Controller{

    public function index(){
        $this->load->view('login');
    }

    public function indexPagina(){
        $this->load->view('Index');
    }

    public function abreSala(){
        $this->load->view('Sala');
    }

    public function encerraSistema(){
        //redireciona o usuario para a pgina de login
        header('Location: ' . base_url());
    }
}
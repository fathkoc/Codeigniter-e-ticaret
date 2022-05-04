<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Etiket extends CI_Controller
{

    function __construct()
    {
        parent:: __construct();
        $this->result = new StdClass();
        $this->result->status = false;
        $this->load->model('admin/' . $this->router->fetch_class() . '_model', 'model');

    }

    public function response()
    {
        echo json_encode($this->result);
        exit();
    }
    public function insertetiket()
    {if (!empty($this->session->userdata('user'))){
        $data = new stdClass();
        if (!empty($_POST)) {
            $this->form_validation->set_rules('name', 'name', 'required|xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->name = $this->input->post('name', true);
                if ($this->model->insertetiket($post)){
                    $this->result->url = site_url('yonetim-paneli/etiket-ekle');
                    $this->result->status = true;
                    $this->response();
                }
                else {
                    $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                    $this->response();
                }
            } else {
                $this->result->error = validation_errors();
                $this->response();
            }

        }
        $this->load->view('admin/header');
        $this->load->view('admin/etiketekle');
        $this->load->view('admin/footer');
    }else{
        redirect(site_url('yonetim-paneli/giris'));
    }}
    public function etiketlist(){
        if (!empty($this->session->userdata('user'))){
        $data= new stdClass();
        $data->etiket=$this->model->etiketlist();
        $this->load->view('admin/header',$data);
        $this->load->view('admin/etiketliste');
        $this->load->view('admin/footer');

    }else{
            redirect(site_url('yonetim-paneli/giris'));
        }}

}

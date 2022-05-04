<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tag extends CI_Controller
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

    public function taginsert()
    {
        if (!empty($this->session->userdata('user'))) {
            $data = new stdClass();
            if (!empty($_POST)) {

                $this->form_validation->set_rules('ticaret', 'ticaret', 'required|xss_clean');
                $this->form_validation->set_rules('adres', 'adres', 'required|xss_clean');
                $this->form_validation->set_rules('mail', 'mail', 'required|xss_clean');
                $this->form_validation->set_rules('marka', 'marka', 'required|xss_clean');
                $this->form_validation->set_rules('mersis', 'mersis', 'required|xss_clean');
                $this->form_validation->set_rules('kep', 'kep', 'required|xss_clean');
                $this->form_validation->set_rules('tel', 'tel', 'required|xss_clean');

                if ($this->form_validation->run() != FALSE) {
                    $post = new stdClass();
                    $post->ticaret = $this->input->post('ticaret', true);
                    $post->adres = $this->input->post('adres', true);
                    $post->mail = $this->input->post('mail', true);
                    $post->marka = $this->input->post('marka', true);
                    $post->mersis = $this->input->post('mersis', true);
                    $post->kep = $this->input->post('kep', true);
                    $post->tel = $this->input->post('tel', true);

                    if ($this->model->taginsert($post)) {
                        $this->result->url = site_url('yonetim-paneli/tag-ekle');
                        $this->result->status = true;
                        $this->response();
                    } else {
                        $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                        $this->response();
                    }
                } else {
                    $this->result->error = validation_errors();
                    $this->response();
                }
            }
            $this->load->view('admin/header');
            $this->load->view('admin/kunyeekle');
            $this->load->view('admin/footer');

        } else {
            redirect(site_url('yonetim-paneli/giris'));
        }
    }

    public function taglist()
    {
        $data = new stdClass();
        $data->tag = $this->model->taglist();

        $this->load->view('admin/header', $data);
        $this->load->view('admin/kunyeliste');
        $this->load->view('admin/footer');

    }

    public function deletetag()
    {
        $this->form_validation->set_rules('id', 'id', 'xss_clean');
        if ($this->form_validation->run() != FALSE) {
            $post = new stdClass();
            $post->id = $this->input->post('id', true);
            if ($this->model->updatetag($post)) {

                $this->result->status = true;
                $this->response();
            } else {
                $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                $this->response();
            }
        } else {
            $this->result->error = validation_errors();
            $this->response();
        }

    }

    public function contactlist()
    {
        $data = new stdClass();
        $data->contact = $this->model->listcontact();
        $this->load->view('admin/header', $data);
        $this->load->view('admin/iletisim');
        $this->load->view('admin/footer');
    }
    public function contactekle(){
        $data=new stdClass();
        if ($_POST){
        $this->form_validation->set_rules('no','no','required|xss_clean');
        $this->form_validation->set_rules('adres','adres','required|xss_clean');
        $this->form_validation->set_rules('mail','mail','required|xss_clean');
        $this->form_validation->set_rules('map','map','required|xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->no=$this->input->post('no',true);
            $post->adres=$this->input->post('adres',true);
            $post->mail=$this->input->post('mail',true);
            $post->map=$this->input->post('map',true);
            if ($this->model->contactekle($post)) {
                $this->result->status = true;
                $this->result->url='iletisimekle';
                $this->response();
            } else {
                $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                $this->response();
            }
        }
        else {
            $this->result->error = validation_errors();
            $this->response();
        }}
        $this->load->view('admin/header', $data);
        $this->load->view('admin/iletisimekle');
        $this->load->view('admin/footer');

    }

}
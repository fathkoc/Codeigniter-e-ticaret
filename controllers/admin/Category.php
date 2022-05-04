<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
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
    public function categoryinsert()

    { if (!empty($this->session->userdata('user'))){

        if (!empty($_POST)) {

        $this->form_validation->set_rules('name','name','required|xss_clean');
        if ($this->form_validation->run() != FALSE) {
            $post = new stdClass();
            $post->name = $this->input->post('name', true);
            $post->slug=url_seo($post->name);
            if ($this->model->categoryinsert($post)) {
                $this->result->url = site_url('yonetim-paneli/category-ekle');
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
        $this->load->view('admin/kategoriekle');
        $this->load->view('admin/footer');
    }else{
        redirect(site_url('yonetim-paneli/giris'));
    }}
    public function categorylist(){
        if (!empty($this->session->userdata('user'))){
        $data=new stdClass();
        if (!empty($_POST)) {
            $this->form_validation->set_rules('new_order', 'orders', 'xss_clean|integer|trim');
            $this->form_validation->set_rules('id', 'id', 'xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->position = $this->input->post('new_order', true);
                $post->id= $this->input->post('id',true);
                if ($this->model->categoryupdate($post)) {
                    $this->result->url = site_url('yonetim-paneli/category-liste');
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
        $data->category=$this->model->categorylist();
        $this->load->view('admin/header',$data);
        $this->load->view('admin/kategorilistele');
        $this->load->view('admin/footer');
    }else{
            redirect(site_url('yonetim-paneli/giris'));
        }
    }
    public function categoryupdate($id=''){
        $data = new stdClass();
        $data->kategori = $this->model->categoryliste($id);
        if (!empty($_POST)){
            $this->form_validation->set_rules('name','name','required|xss_clean');
            $this->form_validation->set_rules('id','id','required|xss_clean');

            if ($this->form_validation->run() !=FALSE){
                $post=new stdClass();
                $post->name=$this->input->post('name',true);
                $post->id=$this->input->post('id',true);
                if ($this->model->categoryupdate($post)){
                    $this->result->url = site_url('yonetim-paneli/category-liste');
                    $this->result->status = true;
                    $this->response();
                }
                else {
                    $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                    $this->response();
                }
            }
            else {
                $this->result->error = validation_errors();
                $this->response();
            }
        }
        $this->load->view('admin/header',$data);
        $this->load->view('admin/kategoriupdate');
        $this->load->view('admin/footer');

    }

    public function statuscategory(){


        $this->form_validation->set_rules('status','status','xss_clean|required|integer');
        $this->form_validation->set_rules('id','id','xss_clean|required|integer');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            $post->status=$this->input->post('status',true);
            if ($this->model->categoryupdate($post)){
                $this->result->url = site_url('yonetim-paneli/slider-liste');
                $this->result->status = true;
                $this->response();
            }
            else{
                $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                $this->response();
            }
        } else{
            $this->result->error = validation_errors();
            $this->response();
        }
    }
    public function deletecategory(){
        $this->form_validation->set_rules('id','id','xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            if ($this->model->deletetupdate($post)){

                $this->result->status = true;
                $this->response();
            } else{
                $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                $this->response();
            }
        }
        else {
            $this->result->error = validation_errors();
            $this->response();
        }

    }
}
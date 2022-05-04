<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Blog extends CI_Controller
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
    public function insertblog(){
        if (!empty($this->session->userdata('user'))){
        $data=new stdClass();
        $data->categori=$this->model->categorylist();
        if (!empty($_POST)) {
            $this->form_validation->set_rules('name', 'name', 'required|xss_clean');
            $this->form_validation->set_rules('content', 'content', 'required|xss_clean');
            $this->form_validation->set_rules('cat_id', 'cat_id', 'required|xss_clean');
            $this->form_validation->set_rules('etiket','etiket','required|xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->name = $this->input->post('name', true);
                $post->content = $this->input->post('content', true);
                $post->cat_id = $this->input->post('cat_id', true);
                $post->slug=url_seo($post->name);
                $post->add_date=date('Y-m-d H:i:s');
                if ($this->session->userdata('images')) {
                    $post->img_pet = $this->session->userdata('images')[0];
                    $this->session->unset_userdata('images');
                }
                if ($this->model->insertblog($post)) {
                    $this->result->url = site_url('yonetim-paneli/blog-ekle');
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
        $this->load->view('admin/header',$data);
        $this->load->view('admin/blogekle');
        $this->load->view('admin/footer');
    }
        else{
            redirect(site_url('yonetim-paneli/giris'));
        }}
    public function listblog(){
        if (!empty($this->session->userdata('user'))){
        $data = new stdClass();
        $data->blog = $this->model->listblog();

        if (!empty($_POST)) {
            $this->form_validation->set_rules('new_order', 'orders', 'xss_clean|integer|trim');
            $this->form_validation->set_rules('id', 'id', 'xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->position = $this->input->post('new_order', true);
                $post->id= $this->input->post('id',true);
                if ($this->model->blogposition($post)) {
                    $this->result->url = site_url('yonetim-paneli/blog-liste');
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
        $this->load->view('admin/header', $data);
        $this->load->view('admin/blogliste');
        $this->load->view('admin/footer');


    }
        else{
            redirect(site_url('yonetim-paneli/giris'));
        }}
    public function statusblog(){
        $this->form_validation->set_rules('status','status','xss_clean|required|integer');
        $this->form_validation->set_rules('id','id','xss_clean|required|integer');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            $post->status=$this->input->post('status',true);
            if ($this->model->blogupdate($post)){
                $this->result->url = site_url('yonetim-paneli/blog-liste');
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
    public function deleteblog(){
        $this->form_validation->set_rules('id','id','xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            if ($this->model->blogupdate($post)){

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
    public function updateblog($id=''){
        $data = new stdClass();
        $data->blogs = $this->model->bloglistele($id);

        if (!empty($_POST)){
            $this->form_validation->set_rules('name','name','required|xss_clean');
            $this->form_validation->set_rules('id','id','required|xss_clean');
            $this->form_validation->set_rules('content','content','required|xss_clean');

            if ($this->form_validation->run() !=FALSE){
                $post=new stdClass();
                $post->name=$this->input->post('name',true);
                $post->id=$this->input->post('id',true);
                $post->content=$this->input->post('content',true);
                $post->add_date=date('Y-m-d H:i:s');
                if ($this->session->userdata('images')) {
                    $post->img_pet = $this->session->userdata('images')[0];
                    $this->session->unset_userdata('images');
                }
                if ($this->model->blogupdates($post)){
                    $this->result->url = site_url('yonetim-paneli/blog-liste');
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
        $this->load->view('admin/blogupdate');
        $this->load->view('admin/footer');

    }

}

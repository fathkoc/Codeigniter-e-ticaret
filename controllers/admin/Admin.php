<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
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

    public function index()
    {
        $this->load->view('admin/header');
        $this->load->view('admin/index');
        $this->load->view('admin/footer');
    }

    public function sliderinsert()
    {      if (!empty($this->session->userdata('user'))){
        $data = new stdClass();

      if (!empty($_POST)) {
            $this->form_validation->set_rules('title', 'title', 'required|xss_clean');
            $this->form_validation->set_rules('content', 'content', 'required|xss_clean');
          $this->form_validation->set_rules('buton', 'buton', 'xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->title = $this->input->post('title', true);
                $post->content = $this->input->post('content', true);
                $post->buton = $this->input->post('buton', true);
                if ($this->session->userdata('images')) {
                    $post->img_pet = $this->session->userdata('images')[0];
                    $this->session->unset_userdata('images');
                }
                if ($this->model->sliderinsert($post)) {
                    $this->result->url = site_url('yonetim-paneli/slider-liste');
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
        $this->load->view('admin/sliderekle');
        $this->load->view('admin/footer');
}else{
        redirect(site_url('yonetim-paneli/giris'));
    }
    }

    public function add_image()
    {
        $uploaded_images = [];
        $config['upload_path'] = 'assets/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = TRUE;
        $this->load->library('Upload', $config);
        if ($this->upload->do_upload('file')) {
            $image_session = $this->session->userdata('images');
            if ($image_session == false) {
                $uploaded_images = [];
            } else {
                $uploaded_images = $image_session;
            }
            $uploaded_images[] = 'assets/uploads/' . $this->upload->data('file_name');
            $this->session->set_userdata('images', $uploaded_images);
            pre($this->session->userdata('images'));
        } else {
            $this->output->set_status_header('404');
            print strip_tags($this->upload->display_errors());
            exit;
        }
    }

    public function sliderlist()
    {
        if (!empty($this->session->userdata('user'))){
        $data = new stdClass();
        $data->slider = $this->model->sliderlist();

        if (!empty($_POST)) {
            $this->form_validation->set_rules('new_order', 'orders', 'xss_clean|integer|trim');
            $this->form_validation->set_rules('id', 'id', 'xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->position = $this->input->post('new_order', true);
                $post->id= $this->input->post('id',true);
                if ($this->model->sliderupdate($post)) {
                    $this->result->url = site_url('panel');
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
        $this->load->view('admin/sliderlist');
        $this->load->view('admin/footer');
    }
        else{
            redirect(site_url('yonetim-paneli/giris'));
        }}
    public function sliderupdate($id=''){
        if (!empty($this->session->userdata('user'))){
        $data=new stdClass();

        $data->slider = $this->model->sliderliste($id);
        if (!empty($_POST)){
            $this->form_validation->set_rules('title','title','required|xss_clean');
            $this->form_validation->set_rules('content','content','required|xss_clean');
            if ($this->form_validation->run() !=FALSE){
                $post=new stdClass();
                $post->title=$this->input->post('title',true);
                $post->content=$this->input->post('content',true);
                $post->id=$this->input->post('id',true);
                if ($this->session->userdata('images')) {
                    $post->img_pet = $this->session->userdata('images')[0];
                    $this->session->unset_userdata('images');
                }
                if ($this->model->sliderupdate($post)){
                    $this->result->url = site_url('yonetim-paneli/slider-liste');
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

        $this->load->view('admin/header', $data);
        $this->load->view('admin/sliderguncelle');
        $this->load->view('admin/footer');

    }else{
            redirect(site_url('yonetim-paneli/giris'));
        }}
    public function statusSlider(){
        $this->session->userdata('user');

     $this->form_validation->set_rules('status','status','xss_clean|required|integer');
     $this->form_validation->set_rules('id','id','xss_clean|required|integer');
     if ($this->form_validation->run() !=FALSE){
         $post=new stdClass();
         $post->id=$this->input->post('id',true);
         $post->status=$this->input->post('status',true);
         if ($this->model->sliderupdate($post)){
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
    public function deleteslider(){
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
    public function logoutpanel(){
        $this->session->sess_destroy('user');
        redirect(site_url('yonetim-paneli/giris'));

    }

}
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Comment extends CI_Controller
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

    public function comment(){
        $data=new stdClass();
        $data->yorum=$this->model->yorumlist();
        $this->load->view('admin/header',$data);
        $this->load->view('admin/yorum');
        $this->load->view('admin/footer');

    }
    public function status(){
        $this->form_validation->set_rules('status','status','xss_clean|required');
        $this->form_validation->set_rules('id','id','xss_clean|required');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            $post->status=$this->input->post('status',true);
            if ($this->model->statusupdate($post)){
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


}

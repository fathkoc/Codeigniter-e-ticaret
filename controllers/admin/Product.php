<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends CI_Controller
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
    public function productinsert(){
        if (!empty($this->session->userdata('user'))){
        $data=new stdClass();
        $data->catagory=$this->model->catlist();
        if (!empty($_POST)) {
            $this->form_validation->set_rules('name', 'name', 'required|xss_clean');
            $this->form_validation->set_rules('content', 'content', 'required|xss_clean');
            $this->form_validation->set_rules('price', 'price', 'required|xss_clean');
            $this->form_validation->set_rules('prices', 'prices','xss_clean');
            $this->form_validation->set_rules('stok', 'stok', 'required|xss_clean');
            $this->form_validation->set_rules('cat_id', 'cat_id', 'required|xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->name = $this->input->post('name', true);
                $post->content = $this->input->post('content', true);
                $post->price = convert_payment($this->input->post('price', true));
                $post->prices = convert_payment($this->input->post('prices', true));
                $post->stok = $this->input->post('stok', true);
                $post->cat_id = $this->input->post('cat_id', true);
                $post->slug=url_seo($post->name);
                if ($this->session->userdata('images')) {
                    $post->img_pet = $this->session->userdata('images')[0];
                    $this->session->unset_userdata('images');
                }
                if ($this->model->productinsert($post)) {
                    $this->result->url = site_url('yonetim-paneli/urun-ekle');
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
        $this->load->view('admin/urunekle');
        $this->load->view('admin/footer');

    }else{
            redirect(site_url('yonetim-paneli/giris'));
        }}
    public function productlist(){
        if (!empty($this->session->userdata('user'))){
        $data = new stdClass();
        $data->product = $this->model->productlist();

        if (!empty($_POST)) {
            $this->form_validation->set_rules('new_order', 'orders', 'xss_clean|integer|trim');
            $this->form_validation->set_rules('id', 'id', 'xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->position = $this->input->post('new_order', true);
                $post->id= $this->input->post('id',true);
                if ($this->model->productpostiiton($post)) {
                    $this->result->url = site_url('yonetim-paneli/urun-liste');
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
        $this->load->view('admin/urunlistele');
        $this->load->view('admin/footer');

    }else{
            redirect(site_url('yonetim-paneli/giris'));
        }}
    public function productupdate($id=''){
        $data=new stdClass();

        $data->product = $this->model->productliste($id);
        if (!empty($_POST)){
            $this->form_validation->set_rules('name','name','required|xss_clean');
            $this->form_validation->set_rules('content','content','required|xss_clean');
            $this->form_validation->set_rules('price','price','required|xss_clean');
            $this->form_validation->set_rules('prices','prices','required|xss_clean');
            $this->form_validation->set_rules('stok','stok','required|xss_clean');
            if ($this->form_validation->run() !=FALSE){
                $post=new stdClass();
                $post->name=$this->input->post('name',true);
                $post->content=$this->input->post('content',true);
                $post->price=convert_payment($this->input->post('price',true));
                $post->prices=convert_payment($this->input->post('prices',true));
                $post->stok=$this->input->post('stok',true);
                $post->id=$this->input->post('id',true);
                if ($this->session->userdata('images')) {
                    $post->img_pet = $this->session->userdata('images')[0];
                    $this->session->unset_userdata('images');
                }
                if ($this->model->productupdates($post)){
                    $this->result->url = site_url('yonetim-paneli/urun-liste');
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
        $this->load->view('admin/urunguncelle');
        $this->load->view('admin/footer');
    }
    public function statusproduct(){


        $this->form_validation->set_rules('status','status','xss_clean|required|integer');
        $this->form_validation->set_rules('id','id','xss_clean|required|integer');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            $post->status=$this->input->post('status',true);
            if ($this->model->productupdate($post)){
                $this->result->url = site_url('yonetim-paneli/urun-liste');
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
    public function deletesproduct(){
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
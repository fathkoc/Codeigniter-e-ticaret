<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Indirim extends CI_Controller
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
    public function indirim(){
        $data=new stdClass();
        $data->product=$this->model->product();
        //prex($data->product);
        $this->load->view('admin/header',$data);
        $this->load->view('admin/indirim');
        $this->load->view('admin/footer');

    }
    public function indirimekle(){
        if ($_POST){
        $this->form_validation->set_rules('indirim_ad','indirim_ad','required|xss_clean');
        $this->form_validation->set_rules('sepet_tutar','sepet_tutar','required|xss_clean');
        $this->form_validation->set_rules('indirim_kodu','indirim_kodu','required|xss_clean');
        $this->form_validation->set_rules('indirim_turu','indirim_turu','required|xss_clean');
        $this->form_validation->set_rules('indirim_miktari','indirim_miktari','required|xss_clean');
        $this->form_validation->set_rules('indirim_tarih','indirim_tarih','required|xss_clean');
        $this->form_validation->set_rules('yarar','yarar','required|xss_clean');
        $this->form_validation->set_rules('indirim_urunler[]','indirim_urunler','xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post2=[];
            $post->indirim_ad=$this->input->post('indirim_ad',true);
            $post->sepet_tutar=$this->input->post('sepet_tutar',true);
            $post->indirim_kodu=$this->input->post('indirim_kodu',true);
            $post->indirim_turu=$this->input->post('indirim_turu',true);
            $post->indirim_miktari=$this->input->post('indirim_miktari',true);
            $post->indirim_tarih=$this->input->post('indirim_tarih',true);
            $post->yarar=$this->input->post('yarar',true);
            $post2=$this->input->post('indirim_urunler[]',true);
            //prex($post2);
            $array2=[];
            if ($indirim_id=$this->model->indirimekle($post)) {
                foreach ($post2 as $value){
                   $array=[
                       'product_id' => $value,
                        'indirim_id'=> $indirim_id

                       ];
                    array_push($array2, $array);
                }
                //prex($array2);
                $this->model->indirimurunekle($array2);
                $this->result->status = true;
                $this->result->url = "indirim-ekle";
                $this->response();
            }
        }else{
            $this->result->error = validation_errors();
            $this->response();
        }
    }


    }
    public function indirimliste(){
        $data=new stdClass();
        $data->indirim=$this->model->indirimliste();
       //prex($data->indirim);


        $this->load->view('admin/header',$data);
        $this->load->view('admin/indirimliste');
        $this->load->view('admin/footer');

    }
    public function indirimstatus(){
        $this->form_validation->set_rules('status','status','xss_clean|required');
        $this->form_validation->set_rules('id','id','xss_clean|required');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            $post->status=$this->input->post('status',true);
            //prex($post);
            if ($this->model->indirimstatus($post)){
                $this->result->url = site_url('yonetim-paneli/indirim-liste');
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
    public function deleteindirim(){
        $this->form_validation->set_rules('id','id','xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->id=$this->input->post('id',true);
            if ($this->model->indirimdelete($post)){

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
    public function indirimupdate($id=''){
        $data = new stdClass();
        $data->indirim = $this->model->indirimlist($id);
        $data->product=$this->model->product();
        $post=new stdClass();
        $data->secili=$this->model->secili($id);
        $data->id=$id;
        //prex($data->secili);
        if (!empty($_POST)){
            $this->form_validation->set_rules('indirim_ad','indirim_ad','required|xss_clean');
            $this->form_validation->set_rules('sepet_tutar','sepet_tutar','required|xss_clean');
            $this->form_validation->set_rules('indirim_kodu','indirim_kodu','required|xss_clean');
            $this->form_validation->set_rules('indirim_turu','indirim_turu','required|xss_clean');
            $this->form_validation->set_rules('indirim_miktari','indirim_miktari','required|xss_clean');
            $this->form_validation->set_rules('indirim_tarih','indirim_tarih','required|xss_clean');
            $this->form_validation->set_rules('yarar','yarar','required|xss_clean');
            $this->form_validation->set_rules('indirim_urunler[]','indirim_urunler','xss_clean');
            $this->form_validation->set_rules('id','id','xss_clean|required');

            if ($this->form_validation->run() !=FALSE){
                $post2=[];
                $post->indirim_ad=$this->input->post('indirim_ad',true);
                $post->sepet_tutar=$this->input->post('sepet_tutar',true);
                $post->indirim_kodu=$this->input->post('indirim_kodu',true);
                $post->indirim_turu=$this->input->post('indirim_turu',true);
                $post->indirim_miktari=$this->input->post('indirim_miktari',true);
                $post->indirim_tarih=$this->input->post('indirim_tarih',true);
                $post->yarar=$this->input->post('yarar',true);
                $post2=$this->input->post('indirim_urunler[]',true);
                $post->id=$this->input->post('id',true);
                $array2=[];
                foreach ($post2 as $value){
                    $array=[
                        'product_id' => $value,
                        'indirim_id'=> $post->id
                    ];
                    array_push($array2, $array);
                }
                if ($this->model->indirimguncelle($post) || $this->model->indirimtoplu($array2) ) {
                    $this->result->status = true;
                }
                else {
                    $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                    $this->response();
                }
                $this->response();
            }

            else {
                $this->result->error = validation_errors();
                $this->response();
            }
        }
        $this->load->view('admin/header',$data);
        $this->load->view('admin/indirimguncel');
        $this->load->view('admin/footer');

    }





}
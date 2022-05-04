<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends CI_Controller
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

public function orderlist(){
        $data=new stdClass();
        $data->siptakip=$this->model->siptakip();
        //prex($data->siptakip);
        $data->durum=$this->model->sipatakip($data->siptakip);
        //prex($data->durum);



        $this->load->view('admin/header',$data);
        $this->load->view('admin/siparistakip');
        $this->load->view('admin/footer');

}
public function orderdetails($id=''){
    $data=new stdClass();
    //prex($_POST);
    $data->durm=$this->model->orddurumrow($id);
    $data->sipdetay=$this->model->orddetail($id);
    $data->sipdetays=$this->model->faturadeail($id);
    //prex($data->sipdetay);
    $data->user=$this->session->userdata('users');
    //prex($data->sipdetays);
    if (!empty($_POST)){

        $data=new stdClass();
         $sip=$this->model->ords($id);
            $post=new stdClass();
            $durum=$_POST['id'];
            $order_id=$_POST['order_id'];
            $post->durum=$durum;
            $post->order_id=$order_id;
            $data->sipdurum=$this->model->sipdurum($post);
            $this->result->status=true;
            $this->response();
        }
        //prex($data->sipdetay);
        //prex($data->user);
         //prex($data->sipdetays);

        $this->load->view('admin/header',$data);
        $this->load->view('admin/siparisdetay');
        $this->load->view('admin/footer');
}
public function bling_detail($id=''){
    $data=new stdClass();
    $data->sipdetay=$this->model->orddetail($id);
    $data->sipdetays=$this->model->faturadeail($id);
    $data->user=$this->session->userdata('users');

    $this->load->view('admin/header',$data);
    $this->load->view('admin/faturadetay');
    $this->load->view('admin/footer');
 
}
public function talep(){
    if ($_POST){
        $data=new stdClass();
        $post=new stdClass();
        $talep=$_POST['id'];
        $order_id=$_POST['order_id'];
        $post->talep=$talep;
        $post->order_id=$order_id;
        $this->model->talepinsert($post);
        $this->result->status=true;
        $this->response();
    }

}



}
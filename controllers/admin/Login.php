<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    function __construct()
    {
        parent:: __construct();

        $this->load->model('admin/' . $this->router->fetch_class() . '_model', 'model');

    }
    public function login()
    {   if (empty($this->session->userdata('user'))){
        $data=new stdClass();
        $this->form_validation->set_rules('username','username','required|xss_clean');
        $this->form_validation->set_rules('password','password','required|xss_clean');
        if ($this->form_validation->run() != FALSE){
            $post=new stdClass();
            $post->username=$this->input->post('username',true);
            $post->password=$this->input->post('password',true);

            if (!empty($person=$this->model->login($post))) {

                if (password_verify($post->password,$person->password)){
                    $this->session->set_userdata('user',$person);
                    redirect(site_url('yonetim-paneli/slider-liste'));

                }
                else{
                    $data->error="KULLANCII ADI VEYA ŞİFRE HATALI";
                }
            }
            else{
                $data->error="HATALI İSİM SİFRE";
            }
        }
        $data->csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        $this->load->view('admin/login',$data);
    }else{
        redirect(site_url('yonetim-paneli/slider-ekle'));
    }


    }

}
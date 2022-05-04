<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Homepage extends Veripay_Controller
{

    function __construct()
    {
        parent:: __construct();
        $this->result = new StdClass();
        $this->result->status = false;
        $this->load->model( $this->router->fetch_class() . '_model', 'model');

    }
    public function response()
    {
        echo json_encode($this->result);
        exit();
    }

    public function index(){
        $data= new stdClass();
        $data->slider=$this->model->sliderlist();
        $data->category=$this->model->categorylist();
        $data->product=$this->model->productlist();
        $data->blog=$this->model->bloglist();
        $data->cart=$this->cart->contents();
        $data->user=$this->session->userdata('users');
        foreach ($data->blog as $vals){
            $metin=$vals->add_date;
            $dizi=explode('-',$metin);
        }
        $data->dizi=$dizi;
        $data->tag=$this->model->taglist();
        $data->iletisim=$this->model->iletisimlist();
        $this->load->view('header',$data);
        $this->load->view('index');
        $this->load->view('footer');
    }

    public function blogdetails($slug=''){
        $data=new stdClass();
        $data->category=$this->model->categorylistt();
        $data->details=$this->model->blogdetail($slug);
        $data->cart=$this->cart->contents();
        //prex($data->details);
        $data->iletisim=$this->model->iletisimlist();
        $this->load->view('header',$data);
        $this->load->view('blog-detail');
        $this->load->view('footer');
    }
    public function insertbasket(){
        $id=$_POST['id'];
        if ($basket=$this->model->basketlist($id)){
            $data = array(
                'id' => $basket->id,
                'qty' => 1,
                'price' => $basket->price,
                'name' => $basket->name,
                'image'=>$basket->img_pet,
                'options'=>array('slug'=>$basket->slug)
            );
            //prex($basket);
            $this->cart->insert($data);
             $metin='';
                foreach ($this->cart->contents() as $values ){
                  $metin .=

                      '
                      <div class="shopping-cart__product-content siltablo'. $values['rowid'].'" data-id="'.$values['rowid'].'">
            <div class="shopping-cart__product-content-item">
                <div class="img-wrapper">
                    <img src="'.site_url($values['image']).'" alt="product" />
                </div>
                <div class="text-content">
                    <h5 class="font-body--md-400">'.$values['name'].'</h5>
                    <p class="font-body--md-400">5kg x <'.$values['qty'].'<span class="font-body--md-500">'.number_format($values['price']/100,2).'TL</span></p>
                </div>
            </div>
            <button data-rowid="'.$values['rowid'].'" class="delete-item sil">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 23C18.0748 23 23 18.0748 23 12C23 5.92525 18.0748 1 12 1C5.92525 1 1 5.92525 1 12C1 18.0748 5.92525 23 12 23Z"
                        stroke="#CCCCCC" stroke-miterlimit="10" />
                    <path d="M16 8L8 16" stroke="#666666" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M16 16L8 8" stroke="#666666" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>
                
                  ';
                }

            $this->result->minibasket=$metin;
            $this->result->items=$this->cart->total_items();
            $this->result->uruns=number_format($this->cart->total()/100,2)."tl";
            $this->result->status=true;
            $this->response();
        }
    }
    public function basket(){
        $data=new stdClass();
        $data->category=$this->model->categorylist();
        $data->basket=$this->cart->contents();
        $data->totals=$this->cart->total();
        if (!empty($this->session->userdata('indirim'))) {
            $session = $this->session->userdata('indirim');
            $indurunler = $this->model->get_indirim($session->id);
            $urun = [];
            foreach ($indurunler as $vals) {
                array_push($urun, $vals->product_id);
            }
            $array2 = [];
            foreach ($data->basket as $values) {
                if (in_array($values['id'], $urun)) {
                    array_push($array2, $values['subtotal']);
                }
            }
            $yarartoplam = array_sum($array2);
            if (!empty($data->indirim = $this->session->userdata('indirim'))) {
                if ($data->indirim->indirim_turu == 1) {
                    if ($data->indirim->yarar == 2) {
                        $data->indirilen = $data->indirim->indirim_miktari;
                        $data->sonuc = $data->totals / 100 - doubleval($data->indirilen);
                    } else {
                        $data->indirilen = doubleval($data->indirim->indirim_miktari);
                        $data->sonuc = $data->totals / 100 - doubleval($data->indirim->indirim_miktari);
                    }
                    //$data->tutar=doubleval($data->indirim->indirim_miktari);
                    //$data->duscek=$data->totals - doubleval($data->indirim->indirim_miktari);
                } elseif ($data->indirim->indirim_turu == 2) {
                    if ($data->indirim->yarar == 2) {
                        $data->indirilenyuzde = $yarartoplam / $data->indirim->indirim_miktari;
                        $data->sonuc = $data->totals / 100 - $data->indirilenyuzde;
                    } else {
                        $data->indirilenyuzde = ($data->totals - $yarartoplam) / $data->indirim->indirim_miktari;
                        $data->sonuc = $data->totals / $data->indirim->indirim_miktari;
                    }
                    //$data->yuzde=doubleval($data->totals*($data->indirim->indirim_miktari/100));
                    //prex($data->yuzde);
                    //$data->duscek=$data->totals-($data->totals*($data->indirim->indirim_miktari/100));
                }
            }
        }
        //prex($data->basket);
        $data->cart=$this->cart->contents();
        if (!empty($_POST)) {
            $id = $_POST['id'];
            $rowid = $_POST['rowid'];
            $qty = $_POST['qty'];
            $data = array(
                'rowid' => $rowid,
                'qty' => $qty

            );
            $this->result->url='basket';
            $urun=$this->model->productsliste($id);
            if ($urun->stok >= $data['qty']){
                $this->cart->update($data);
                $this->result->status = true;
            }else{
                $this->result->error="STOKTAKI ÜRÜN MİKTARINI AŞTINIZ";
            }
            $this->result->totals = $this->cart->total();
            //$this->cart->destroy();
            $this->response();
        }
        $data->iletisim=$this->model->iletisimlist();
        $this->load->view('header',$data);
        $this->load->view('basket');
        $this->load->view('footer');
    }
    public function deletebasket(){
          $id=$_POST['rowid'];
          $this->form_validation->set_rules('rowid','rowid','xss_clean');
          if ($this->form_validation->run() !=FALSE){
              $this->result->status = true;
              $this->cart->remove($id);
              //$this->result->id=$id;
          }
        $this->result->items=$this->cart->total_items();
        $this->result->uruns=number_format($this->cart->total())."tl";
        $this->response();
    }
    public function deletebaskets(){
        $id=$_POST['rowid'];
        $this->form_validation->set_rules('rowid','rowid','xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $this->result->status = true;
            $this->cart->remove($id);
            //$this->result->id=$id;
        }
        $this->response();
    }
    public function qtyupdate(){
            $id=$_POST['id'];
            $rowid = $_POST['rowid'];
            $qty = $_POST['qty'];
            $data = array(
                'rowid' => $rowid,
                'qty' => $qty
            );
        $urun=$this->model->productsliste($id);
        if ($urun->stok >= $data['qty']){
            $this->cart->update($data);
            $this->result->status = true;
        }else{
            $this->result->error="STOKTAKI ÜRÜN MİKTARINI AŞTINIZ";
        }

            $this->result->url='basket';
        $this->response();
    }
    public function allpro($aranan=''){
        $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
        $data->category = $this->model->categorylist();
        //$data->categorys=$this->model->allcategor();
        //prex($data->category);
        $gelen=$aranan;
        $filtre=[];
        $filtre2=[];
        //$data->aranan=$aranan;
        $keyword=$this->input->get('aranan',true);


        $pagination_config=new stdClass();
        $this->load->library('pagination');
        $page = (int)$this->input->get('per_page', true);

        if (empty($page)) {
            $data->current_page = 1;
        } else {
            $data->current_page = $page;
        }
        if (!empty($this->input->get('g'))){
            $filtre2['filtre']=$this->input->get('g');
        }
        if (!empty($this->input->get('maximum')) && !empty($this->input->get('minimum'))) {
            $filtre['maximum'] = $this->input->get('maximum', true);
            $filtre['minimum'] = $this->input->get('minimum', true);
        }
        //prex($filtre2);
        $pagination_model_config = ['page' => 4,'current_page' => $data->current_page];
        $data->list = $this->model->allcategor($pagination_model_config,$filtre,$filtre2,$keyword);
        $pagination_config->url         = site_url('tumurunler');
        $pagination_config->page        = $pagination_model_config['page'];
        $pagination_config->total_rows  = $data->list->total_rows;
        $this->pagination->initialize(front_pagination_get($pagination_config));
        $data->pagination_text = $this->pagination->create_links();

        $this->load->view('header',$data);
        $this->load->view('allpro');
        $this->load->view('footer');

    }
    public function categorylist($slug=''){
        $data=new stdClass();
        $data->category=$this->model->categorylist();
        $data->slug=$slug;

        //prex($data->category);
        $filtre=[];
        $filtre2=[];

        $pagination_config=new stdClass();
        $this->load->library('pagination');
        $page = (int)$this->input->get('per_page', true);
          //$filtre='';
        if (empty($page)) {
            $data->current_page = 1;
        } else {
            $data->current_page = $page;
        }
        //prex($this->input->get('minimum'));
        //prex($this->input->get('g'));
           if (!empty($this->input->get('g'))){
               $filtre2['filtre']=$this->input->get('g');
           }
            if (!empty($this->input->get('maximum')) && !empty($this->input->get('minimum'))) {
                $filtre['maximum'] = $this->input->get('maximum', true);
                $filtre['minimum'] = $this->input->get('minimum', true);
            }
        $pagination_model_config = ['page' => 3,'current_page' => $data->current_page];
        $data->list = $this->model->catdetail($pagination_model_config,$slug,$filtre,$filtre2);
        $pagination_config->url         = site_url('category-listele/'.$slug);
        $pagination_config->page        = $pagination_model_config['page'];
        $pagination_config->total_rows  = $data->list->total_rows;
        $this->pagination->initialize(front_pagination_get($pagination_config));
        $data->pagination_text = $this->pagination->create_links();





        $data->iletisim=$this->model->iletisimlist();
        $this->load->view('header',$data);
        $this->load->view('products');
        $this->load->view('footer');
    }
    public function login(){
        $data=new stdClass();
        $data->category=$this->model->categorylist();
        $data->iletisim=$this->model->iletisimlist();
        if (empty($this->session->userdata('users'))) {
            $this->form_validation->set_rules('mail', 'mail', 'required|xss_clean|valid_email');
            $this->form_validation->set_rules('password', 'password', 'required|xss_clean');
            $this->form_validation->set_rules('hatirla', 'hatirla', 'xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $hatirla=$this->input->post('hatirla',true);
                $post = new stdClass();
                $post->mail = $this->input->post('mail', true);
                $post->password = $this->input->post('password', true);
                if (!empty($person = $this->model->login($post))) {
                    if (password_verify($post->password, $person->password)) {
                        $this->session->set_userdata('users', $person);
                        if (!empty($hatirla)){
                            $post2=new stdClass();
                            $post2->token=uniqid(md5($person->id));
                            $post2->user_id=$person->id;
                            $post2->tarih=date('Y-m-d');
                            $post2->ipadress=getIpAddress();
                            setcookie('hatirla',$post2->token,time() +(86400*30),'/');
                            $this->model->inserttoken($post2);
                        }
                        redirect(site_url());
                    } else {
                        $data->errors="hatalı k.adi felan";
                    }
                }
            }
            $data->csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            $this->load->view('header', $data);
            $this->load->view('login');
            $this->load->view('footer');
        }else{
            redirect(site_url('Anasayfa'));

        }
    }
    public function register(){
       $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
       $this->form_validation->set_rules('nick','nick','required|xss_clean');
        $this->form_validation->set_rules('mail','mail','required|xss_clean');
        $this->form_validation->set_rules('tel','tel','required|xss_clean');
        $this->form_validation->set_rules('password','password','required|xss_clean');
        $this->form_validation->set_rules('passrpr','passrpr','required|xss_clean');
        $this->form_validation->set_rules('user','user','required|xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->nick=$this->input->post('nick',true);
            $post->mail=$this->input->post('mail',true);
            $post->tel=$this->input->post('tel',true);
            $post->user=$this->input->post('user',true);
            $password=$this->input->post('password',true);
            $passrpr=$this->input->post('passrpr',true);
            $post->password=password_hash($password,PASSWORD_DEFAULT);
            $this->model->register($post);
            $this->result->status = true;
            $this->result->url='login';
            $this->response();
        }
        $this->load->view('header');
        $this->load->view('register');
        $this->load->view('footer');
    }
    public function account(){
        $this->user_control();
            $data = new stdClass();
            $data->iletisim=$this->model->iletisimlist();
            $data->user = $this->session->userdata('users');
            $data->users=$this->model->regilist($data->user->id);
            //prex($data->users);
            if ($_POST){
            $this->form_validation->set_rules('user', 'user', 'required|xss_clean');
            $this->form_validation->set_rules('nick', 'nick', 'required|xss_clean');
            $this->form_validation->set_rules('mail', 'mail', 'required|xss_clean');
            $this->form_validation->set_rules('tel', 'tel', 'required|xss_clean');
            $this->form_validation->set_rules('sifrecheck', 'sifrecheck', 'xss_clean');
            $this->form_validation->set_rules('eskisifre', 'eskisifre', 'xss_clean');
            $this->form_validation->set_rules('yenisifre', 'yenisifre', 'xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->user=$this->input->post('user',true);
                $post->nick = $this->input->post('nick', true);
                $post->mail = $this->input->post('mail', true);
                $post->tel = $this->input->post('tel', true);
                $post->id =$data->user->id;
                $sifrecheck=$this->input->post('sifrecheck');
                $eskisifre=$this->input->post('eskisifre',true);
                if (!empty($sifrecheck)){
                    $passwords=$this->input->post('yenisifre');
                    //pre($eskisifre);
                    //pre($data->users->password);
                    //prex(password_verify($eskisifre,$data->users->password));
                    if (password_verify($eskisifre,$data->users->password)){
                        $post->password = password_hash($passwords,PASSWORD_DEFAULT);
                    }
                }
                if ($this->model->loginupdate($post)) {
                    $this->session->set_userdata('users', $post);
                    $this->result->status = true;
                    $this->result->url = 'account';
                }
                $this->result->status = true;
                $this->response();
            }}
            $data->category = $this->model->categorylist();
            //prex($this->session->userdata('users'));
            $this->load->view('header', $data);
            $this->load->view('account');
            $this->load->view('footer');

    }
    public function logout(){
        $this->session->unset_userdata('users');
        setcookie('hatirla','' , time() - 3600, "/");
        redirect(site_url('Anasayfa'));
    }
    public function myadress(){
        $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
        $data->category=$this->model->categorylist();
        $post=new stdClass();
        $user=$this->session->userdata('users');
        $data->adres=$this->model->adreslist($user);
        $data->cities=$this->model->citieslist();
        //prex($data->counties);
        if ($_POST){
        $this->form_validation->set_rules('user','user','required|xss_clean');
        $this->form_validation->set_rules('nick','nick','required|xss_clean');
        $this->form_validation->set_rules('mail','mail','required|xss_clean|valid_email');
        $this->form_validation->set_rules('tel','tel','required|xss_clean');
        $this->form_validation->set_rules('city','city','required|xss_clean');
        $this->form_validation->set_rules('counties','counties','required|xss_clean');
        $this->form_validation->set_rules('adress','adress','required|xss_clean');
        $this->form_validation->set_rules('user_id','user_id','required|xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post->user=$this->input->post('user',true);
            $post->nick=$this->input->post('nick',true);
            $post->mail=$this->input->post('mail',true);
            $post->tel=$this->input->post('tel',true);
            $post->city=$this->input->post('city',true);
            $post->user_id=$this->input->post('user_id',true);
            $post->counties=$this->input->post('counties',true);
            $post->adress=$this->input->post('adress',true);
            $post->kargo = $this->input->post('kargo', true);
            $post->fatura = $this->input->post('fatura', true);
            if (!empty($post->kargo)) {
                $this->model->adresupdate($post);
            }
            if (!empty($post->fatura)){
                $this->model->adresupdatefatura($post);
            }
            if ($this->model->adres($post)) {
                $this->result->status = true;
                $this->response();
            }else {
                $this->result->error = "Ekleme İşlemi Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin.";
                $this->response();
            }
        }else {
            $this->result->error = validation_errors();
            $this->response();
        }

        }
        $data->cart=$this->cart->contents();
        $data->user=$this->session->userdata('users');
        $this->load->view('header',$data);
        $this->load->view('my-address');
        $this->load->view('footer');
    }
    public function getcounties(){
        $citiesid=$_POST['id'];
        $counties=$this->model->counties($citiesid);
        $this->result->counties=$counties;
        $this->result->status=true;
        $this->response();
    }

    public function taksits(){
        $this->load->library('Iyzico');
        $data=new stdClass();

        if (!empty($_POST)) {

            $taksit=$_POST['taksit'];

            $taksit = substr($taksit,0,7);
            $data->taksit = str_replace(' ','',$taksit);
            if (!empty($data->indirim=$this->session->userdata('indirim'))){
                if ($data->indirim->indirim_turu==1){
                    $data->total=$this->cart->total() - doubleval($data->indirim->indirim_miktari);
                }elseif ($data->indirim->indirim_turu==2){
                    $data->total=$this->cart->total()-($this->cart->total()*($data->indirim->indirim_miktari/100));
                }
            }else{
                $data->total=$this->cart->total();
            }

            $deneme=$this->iyzico->taksit($data);
           // prex($deneme);
            $resultjson=json_decode($deneme,true);
            //prex($resultjson);
            $status=$resultjson['status'];
            if ($status=="success"){
                $taksitler=$resultjson['installmentDetails'][0]['installmentPrices'];
                //prex($taksitler);
                $metin='';
                foreach ($taksitler as $value){
                 $metin .=  '
                                                            
                                                                <div class="flex-box">
                                                                    <div class="pi-left">
                                                                        <label for="'.$value['installmentNumber'].'"
                                                                            class="control-2 control--checkbox-2">'.$value['installmentNumber'].'x'.number_format($value['installmentPrice']/100,2).'
                                                                            <input id="'.$value['installmentNumber'].'" type="radio"
                                                                                name="installment" checked="checked"
                                                                                value="'.$value['installmentNumber'].'">
                                                                            <div class="control__indicator-2"></div>
                                                                        </label>
                                                                    </div>
                                                                    <div class="pi-right">'.number_format($value['totalPrice']/100,2).'
                                                                      <input type="hidden" value="'.$value['totalPrice'].'" name="installment_price" >
                                                                    </div>
                                                                </div>
                                                            
                                                        ';
                }
               $this->result->installment_table=$metin;

            }else{
                echo "hatalı kart";
            }

            $this->result->status=true;
        }
        $this->response();
    }
    public function orderinfo(){
        $this->load->library('Iyzico');
        $data=new stdClass();
        $data->personal=$this->session->userdata('users');
        $data->category=$this->model->categorylist();
        $data->user=$this->session->userdata('users');
        $data->totals=$this->cart->total();
        $data->cities=$this->model->citieslist();
        $post=new stdClass();
        $post_pay=new stdClass();
        $posts=new stdClass();
        if (!empty($data->indirim=$this->session->userdata('indirim'))){
            if ($data->indirim->indirim_turu==1){
                //prex(($data->indirim->indirim_miktari));
                $data->tutar=doubleval($data->indirim->indirim_miktari);
                $data->duscek=$data->totals - doubleval($data->indirim->indirim_miktari);
            }elseif ($data->indirim->indirim_turu==2){
                $data->yuzde=doubleval($data->totals*($data->indirim->indirim_miktari/100));
                $data->duscek=$data->totals-($data->totals*($data->indirim->indirim_miktari/100));
            }
        }

        if ($this->session->userdata('users')) {
            $data->user = $this->session->userdata('users');
            $data->adlis = $this->model->addliste($data->user);
        }
        if (!empty($_POST)) {
            $this->form_validation->set_rules('kargo','xss_clean');
            $this->form_validation->set_rules('fatura','xss_clean');
            $this->form_validation->set_rules('user', 'user', 'xss_clean|required');
            $this->form_validation->set_rules('tel', 'tel', 'xss_clean|required|integer');
            $this->form_validation->set_rules('mail', 'mail', 'xss_clean|required|valid_email');
            $this->form_validation->set_rules('city', 'city', 'xss_clean|required');
            $this->form_validation->set_rules('counties', 'counties', 'xss_clean|required');
            $this->form_validation->set_rules('notes', 'notes', 'xss_clean');
            $this->form_validation->set_rules('f_name', 'adress', 'xss_clean|required');
            $this->form_validation->set_rules('f_tel', 'adress', 'xss_clean|required|integer');
            $this->form_validation->set_rules('f_mail', 'adress', 'xss_clean|required|valid_email');
            $this->form_validation->set_rules('f_city', 'adress', 'xss_clean|required');
            $this->form_validation->set_rules('f_counties', 'adress', 'xss_clean|required');
            $this->form_validation->set_rules('f_address', 'adress', 'xss_clean|required');
            $kargo=$this->input->post('kargo');
            $fatura=$this->input->post('fatura');
            $this->form_validation->set_rules('cartsec', 'cartsec', 'xss_clean|integer');
            $this->form_validation->set_rules('username', 'username', 'xss_clean|required');
            $this->form_validation->set_rules('number','number','required|xss_clean');
            $this->form_validation->set_rules('expiry','expiry','required|xss_clean');
            $this->form_validation->set_rules('cvc','cvc','required|xss_clean');
            $this->form_validation->set_rules('installment','installment','xss_clean');
            $cartsec=$this->input->post('cartsec',true);
            $this->form_validation->set_rules('faturas','fatura_kargo','xss_clean');
            $this->form_validation->set_rules('installment_price','installment_price','xss_clean');
            $faturas=$this->input->post('faturas');

            if (!empty($this->session->userdata('users'))) {
                if (!empty($kargo && $faturas)) {
                    //prex("kargo && faturas");
                    $user = $this->session->userdata('users');
                    $adlis = $this->model->addliste($user);

                    $post->shiping_name = $adlis->user;
                    $post->shipping_phone = $adlis->tel;
                    $post->shipping_mail = $adlis->mail;
                    $post->shipping_cities_id = $adlis->city;
                    $post->shipping_counties_id = $adlis->counties;
                    $post->shiping_address = $adlis->adress;
                    $post->notes = $this->input->post('notes', true);
                    if(!empty($data->duscek)){
                        $post->total =$data->duscek;
                    }else{
                        $post->total=$this->cart->total();
                    }
                    $users = $this->session->userdata('users');
                    $adlist = $this->model->addliste($users);
                    $post->bling_name = $adlis->user;
                    $post->bling_phone = $adlis->tel;
                    $post->bling_mail = $adlis->mail;
                    $post->bling_city_id = $adlis->city;
                    $post->bling_counties_id = $adlis->counties;
                    $post->bling_adress = $adlis->adress;
                }
                elseif (!empty($kargo && $fatura)) {
                    //prex("kargo && fatura");
                    $user = $this->session->userdata('users');
                    $adlis = $this->model->addliste($user);
                    //prex($adlis);
                    $post->shiping_name = $adlis->user;
                    $post->shipping_phone = $adlis->tel;
                    $post->shipping_mail = $adlis->mail;
                    $post->shipping_cities_id = $adlis->city;
                    $post->shipping_counties_id = $adlis->counties;
                    $post->shiping_address = $adlis->adress;
                    $post->notes = $this->input->post('notes', true);
                    if(!empty($data->duscek)){
                        $post->total =$data->duscek;
                    }else{
                        $post->total=$this->cart->total();
                    }
                    $users = $this->session->userdata('users');
                    $adlist = $this->model->adlstfatra($users);
                   // prex($adlist);
                    $post->bling_name = $adlist->user;
                    $post->bling_phone = $adlist->tel;
                    $post->bling_mail = $adlist->mail;
                    $post->bling_city_id = $adlist->city;
                    $post->bling_counties_id = $adlist->counties;
                    $post->bling_adress = $adlist->adress;

                } elseif (!empty($kargo)) {
                    $userw = $this->session->userdata('users');
                    $adliss = $this->model->addliste($userw);
                    //prex("kargo");

                    $post->shiping_name = $adliss->user;
                    $post->shipping_phone = $adliss->tel;
                    $post->shipping_mail = $adliss->mail;
                    $post->shipping_cities_id = $adliss->city;
                    $post->shipping_counties_id = $adliss->counties;
                    $post->shiping_address = $adliss->adress;
                    $post->notes = $this->input->post('notes', true);
                    if(!empty($data->duscek)){
                        $post->total =$data->duscek;
                    }else{
                        $post->total=$this->cart->total();
                    }
                    $post->bling_name = $this->input->post('f_name', true);
                    $post->bling_phone = $this->input->post('f_tel', true);
                    $post->bling_mail = $this->input->post('f_mail', true);
                    $post->bling_city_id = $this->input->post('f_city', true);
                    $post->bling_counties_id = $this->input->post('f_counties', true);
                    $post->bling_adress = $this->input->post('f_address', true);


                } elseif (!empty($fatura)) {
                    $userws = $this->session->userdata('users');
                    $adlisst = $this->model->adlstfatra($userws);
                    //prex("fatura");
                    $post->bling_name = $adlisst->user;
                    $post->bling_phone = $adlisst->tel;
                    $post->bling_mail = $adlisst->mail;
                    $post->bling_city_id = $adlisst->city;
                    $post->bling_counties_id = $adlisst->counties;
                    $post->bling_adress = $adlisst->adress;
                    $post->shiping_name = $this->input->post('user', true);
                    $post->shipping_phone = $this->input->post('tel', true);
                    $post->shipping_mail = $this->input->post('mail', true);
                    $post->shipping_cities_id = $this->input->post('city', true);
                    $post->shipping_counties_id = $this->input->post('counties', true);
                    $post->notes = $this->input->post('notes', true);
                    $post->shiping_address = $this->input->post('adress', true);
                    if(!empty($data->duscek)){
                        $post->total =$data->duscek;
                    }else{
                        $post->total=$this->cart->total();
                    }
                }
                else{
                    $post->shiping_name = $this->input->post('user', true);
                    $post->shipping_phone = $this->input->post('tel', true);
                    $post->shipping_mail = $this->input->post('mail', true);
                    $post->shipping_cities_id = $this->input->post('city', true);
                    $post->shipping_counties_id = $this->input->post('counties', true);
                    $post->notes = $this->input->post('notes', true);
                    $post->shiping_address = $this->input->post('adress', true);
                    $post->bling_name = $this->input->post('f_name', true);
                    $post->bling_phone = $this->input->post('f_tel', true);
                    $post->bling_mail = $this->input->post('f_mail', true);
                    $post->bling_city_id = $this->input->post('f_city', true);
                    $post->bling_counties_id = $this->input->post('f_counties', true);
                    $post->bling_adress = $this->input->post('f_address', true);
                    if(!empty($data->duscek)){
                        $post->total =$data->duscek;
                    }else{
                        $post->total=$this->cart->total();
                    }
                }
            }elseif(!empty($faturas)) {

                $post->shiping_name = $this->input->post('user', true);
                $post->shipping_phone = $this->input->post('tel', true);
                $post->shipping_mail = $this->input->post('mail', true);
                $post->shipping_cities_id = $this->input->post('city', true);
                $post->shipping_counties_id = $this->input->post('counties', true);
                $post->notes = $this->input->post('notes', true);
                $post->shiping_address = $this->input->post('adress', true);
                $post->bling_name =  $this->input->post('user', true);
                $post->bling_phone = $this->input->post('tel', true);
                $post->bling_mail =$this->input->post('mail', true);
                $post->bling_city_id =  $this->input->post('city', true);
                $post->bling_counties_id = $this->input->post('counties', true);
                $post->bling_adress =$this->input->post('adress', true);
                if(!empty($data->duscek)){
                    $post->total =$data->duscek;
                }else{
                    $post->total=$this->cart->total();
                }
            }

              else{
                $post->shiping_name = $this->input->post('user', true);
                $post->shipping_phone = $this->input->post('tel', true);
                $post->shipping_mail = $this->input->post('mail', true);
                $post->shipping_cities_id = $this->input->post('city', true);
                $post->shipping_counties_id = $this->input->post('counties', true);
                $post->notes = $this->input->post('notes', true);
                $post->shiping_address = $this->input->post('adress', true);
                $post->bling_name = $this->input->post('f_name', true);
                $post->bling_phone = $this->input->post('f_tel', true);
                $post->bling_mail = $this->input->post('f_mail', true);
                $post->bling_city_id = $this->input->post('f_city', true);
                $post->bling_counties_id = $this->input->post('f_counties', true);
                $post->bling_adress = $this->input->post('f_address', true);
                  if(!empty($data->duscek)){
                      $post->total =$data->duscek;
                  }else{
                      $post->total=$this->cart->total();
                  }
            }


        if ($data->order = $this->model->orderinsert($post)) {
            $cart = new stdClass();
            $cart->orderid = $data->order;
            $array3 = [];
            foreach ($this->cart->contents() as $rows) {
                $array4 = [
                    'product_id' => $rows['id'],
                    'product_name' => $rows['name'],
                    'qty' => $rows['qty'],
                    'order_id' => $cart->orderid
                ];
                array_push($array3, $array4);
            }
            $this->model->buyinsert($array3);
//prex($this->cart->contents());

            $array5 = [];
            foreach ($this->cart->contents() as $rows) {
                $product=$this->model->probilgi($rows['id']);
                $array6 = [
                    'id' => $rows['id'],
                    'stok' => $product - $rows['qty'],
                ];;
                array_push($array5, $array6);

            }
          $this->model->stokguncelleme($array5);

            if(!empty($data->duscek)){
                $post->total =$data->duscek;
            }else{
                $post->total=$this->cart->total();
            }
              $post_pay->sipno=md5($data->order);
            if ($cartsec==1){
                $post_pay->username=$this->input->post('username',true);
                $post_pay->number=$this->input->post('number',true);
                $post_pay->expiry=$this->input->post('expiry',true);
                $post_pay->cvc=$this->input->post('cvc',true);
                $post_pay->instalment=$this->input->post('installment',true);
                $post_pay->instalment_price=$this->input->post('installment_price',true);
                $post_pay->status=$cartsec;
                $post_pay->order_id=$data->order;

                list($posts->mount,$posts->year)=explode("/",$post_pay->expiry);
                $posts->number2=str_replace(' ','',$post_pay->number);
                $donen3d=$this->iyzico->ThreeD($post_pay,$post,$posts);
                //prex($post_pay);

                //$this->result->status = true;
                if ($donen3d->status=='success'){
                    $this->result->form = base64_decode($donen3d->threeDSHtmlContent);
                    $this->result->status=true;
                    $this->response();
                }else{
                    $this->result->hatae=$donen3d->errorMessage;
                    $this->response();
                }
            }
            elseif ($cartsec==2){
                $post_pay->status=$cartsec;
                $post_pay->order_id=$data->order;
                $this->result->status = true;

            }
            elseif ($cartsec==3){
                $post_pay->status=$cartsec;
                $post_pay->order_id=$data->order;
                $this->result->status = true;

            }
            $siparis=$this->model->payinsert($post_pay);
            $this->result->status = true;
            $this->result->url='basarili';
             $this->response();
        }else{
            $this->result->error = validation_errors();
            $this->response();
        }}

        $data->iletisim=$this->model->iletisimlist();
        $this->load->view('header',$data);
        $this->load->view('order-info');
        $this->load->view('footer');
    }
    public function iyzi(){
        if ($_POST['status']=='success'){
            redirect(site_url('basarili'));
        }else{
           redirect(site_url('succesnt'));
        }

    }
    public function succesnt(){

        $this->load->view('header');
        $this->load->view('success');
        $this->load->view('footer');

    }

    public function success(){
        $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
        $data->category=$this->model->categorylist();
        $data->sipno=$this->model->siparisno();
        $this->session->unset_userdata('indirim');
        $this->cart->destroy();

        //$data->sipno=$this->model->orderno();
        $this->session->userdata('users');
        //prex($data->sipno);

        $this->model->succ($data->sipno->id);
        //prex($data->sipno);
        $this->load->view('header',$data);
        $this->load->view('success');
        $this->load->view('footer');
    }
    public function productdetails($slug=''){
        $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
        $data->user=$this->session->userdata('users');
        $data->category = $this->model->categorylist();
        $data->yorum=$this->model->yorumlist($slug);
        $metin='<li>
                                                        <span class="icon">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M8.27563 11.9209L11.4281 13.9179C11.8311 14.1729 12.3311 13.7934 12.2116 13.3229L11.3011 9.74042C11.2754 9.64063 11.2784 9.53561 11.3097 9.43743C11.341 9.33925 11.3994 9.2519 11.4781 9.18542L14.3051 6.83292C14.6761 6.52392 14.4851 5.90742 14.0076 5.87642L10.3161 5.63642C10.2167 5.62937 10.1214 5.59424 10.0412 5.53511C9.961 5.47598 9.89925 5.39528 9.86313 5.30242L8.48613 1.83542C8.44864 1.73689 8.38208 1.65209 8.29528 1.59225C8.20849 1.53241 8.10555 1.50037 8.00013 1.50037C7.89471 1.50037 7.79177 1.53241 7.70498 1.59225C7.61818 1.65209 7.55163 1.73689 7.51413 1.83542L6.13713 5.30242C6.10109 5.39538 6.03937 5.47618 5.95916 5.5354C5.87896 5.59462 5.78358 5.62983 5.68413 5.63692L1.99263 5.87692C1.51563 5.90742 1.32363 6.52392 1.69513 6.83292L4.52213 9.18592C4.60079 9.25236 4.65911 9.33962 4.69042 9.4377C4.72173 9.53578 4.72475 9.64071 4.69913 9.74042L3.85513 13.0629C3.71163 13.6274 4.31213 14.0829 4.79513 13.7764L7.72513 11.9209C7.80748 11.8686 7.90305 11.8408 8.00063 11.8408C8.09822 11.8408 8.19378 11.8686 8.27613 11.9209H8.27563Z"
                                                                    fill="#FF8A00" />
                                                            </svg>
                                                        </span>
                                                    </li>';
        $data->metin=$metin;
        //prex($data->yorum);
        if (!$data->detailsproduct = $this->model->prodetails($slug)){
             redirect(site_url());
        }
          //prex($data->detailsproduct->cat_id);
        $data->kategors=$this->model->kategoriproduct($data->detailsproduct->cat_id);
        //prex($data->kategors);
        $data->bnz=$this->model->probenzer($data->detailsproduct->cat_id);
        if (!empty($_POST)) {
            $id = $_POST['id'];
            $qty=$_POST['qty'];
            $basket = $this->model->basketlist($id);
            $this->result->status = true;
            $array = array(
                'id' => $basket->id,
                'qty' => $qty,
                'price' => $basket->price,
                'name' => $basket->name,
                'image' => $basket->img_pet
            );
            $this->cart->insert($array);
            $data->cart = $this->cart->contents();
            $this->response();
        }
        $this->load->view('header',$data);
        $this->load->view('product-detail');
        $this->load->view('footer');
    }
    public function blogin(){
        $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
        $data->category=$this->model->categorylistt();
        $data->blog=$this->model->blosg();
        $this->load->view('header',$data);
        $this->load->view('blog');
        $this->load->view('footer');
    }
    public function contact(){
        $data=new stdClass();

        $data->category = $this->model->categorylist();
        $data->iletisim=$this->model->iletisimlist();
        //prex($data->iletisim);
        if (!empty($_POST)) {
            $this->form_validation->set_rules('adsoyad', 'adsoyad', 'required|xss_clean');
            $this->form_validation->set_rules('tel', 'tel', 'required|xss_clean');
            $this->form_validation->set_rules('subject', 'subject', 'required|xss_clean');
            $this->form_validation->set_rules('mail', 'mail', 'required|xss_clean|valid_email');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->adsoyad = $this->input->post('adsoyad', true);
                $post->tel = $this->input->post('tel', true);
                $post->subject = $this->input->post('subject', true);
                $post->mail = $this->input->post('mail', true);
                $this->model->insertcontact($post);
                $this->result->status = true;
                $this->result->url = 'Anasayfa';
                $this->response();
            } else {
                $this->result->error = validation_errors();
                $this->response();
            }
        }
        $this->load->view('header',$data);
        $this->load->view('contact');
        $this->load->view('footer');
    }
    public function forgetpass(){
        $data=new stdClass();
        $data->category = $this->model->categorylist();
        $data->iletisim=$this->model->iletisimlist();
        $this->form_validation->set_rules('mail','mail','xss_clean|valid_email');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->mail=$this->input->post('mail',true);
            $post->token=md5(date('Y-m-d H:i:s'));
            try {
                $mail = new PHPMailer();
                $mail->IsSMTP();  // telling the class to use SMTP
                $mail->SMTPDebug = 0;
                $mail->Mailer = "smtp";
                $mail->Host = "mail.veripay.com.tr";
                $mail->Port = 587;
                $mail->SMTPAuth = true; // turn on SMTP authentication
                $mail->Username = "mailadresiniz@veripay.com.tr"; // SMTP username
                $mail->Password = '^M80t9jl'; // SMTP password
                $mail->Priority = 1;
                $mail->AddAddress($post->mail, "İletişim Talebi");
                $mail->SetFrom('mailadresiniz@veripay.com.tr', 'İletişim Talebi');
                $mail->AddReplyTo($post->token);
                $mail->CharSet = 'UTF-8';
                $mail->IsHTML(true);
                $mail->Subject = 'Web sitenizden yeni bir iletişim talebi alındı';
                $mail->Body = '
                        
                        <p><a href="http://fatih.vdestek.com/bella/refresh/'.$post->token.'">SİFRE SIFIRLAMA</a></p>';

                if (!$mail->Send()) {
                    $this->result->error = "Mesajınız Gönderilemedi, Daha Sonra Tekrar Deneyin";
                    $this->response();
                } else {
                    if ($this->model->forgpass($post)) {
                        $this->result->status = true;
                        $this->result->url='login';
                    } else {
                        $this->result->error = "Ekleme Esnasında Bir Hata Oluştu Lütfen Tekrar Deneyin";
                    }
                }
            } catch (Exception $e) {
                $this->result->error = "Mesajınız Gönderilemedi, Daha Sonra Tekrar Deneyin";
            }


            $this->response();

        }
        $this->load->view('header',$data);
        $this->load->view('password-forget');
        $this->load->view('footer');
    }
    public function refresh($token=''){
        $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
        $data->token=$token;
        if ($_POST) {
            $this->form_validation->set_rules('password', 'sifre', 'required|xss_clean');
            $this->form_validation->set_rules('passwords', 'şifre tekrar', 'required|xss_clean|matches[password]');
            $this->form_validation->set_rules('token','token','required|xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $password = $this->input->post('password', true);
                $post->token=$this->input->post('token',true);
                $post->password = password_hash($password, PASSWORD_DEFAULT);
                $this->model->updatepas($post);
                $this->model->tokensil($post);
                $this->result->status = true;


            } else {
                $this->result->error=validation_errors();
            }
            $this->result->url='login';
            $this->response();
        }
        $this->load->view('header',$data);
        $this->load->view('password-reset');
        $this->load->view('footer');
    }
    public function about(){
         $data=new stdClass();
        $data->iletisim=$this->model->iletisimlist();
        $data->category=$this->model->categorylist();
        $this->load->view('header',$data);
        $this->load->view('about');
        $this->load->view('footer');
    }

 public function quality(){
     $data=new stdClass();
     $data->category=$this->model->categorylist();
     $data->iletisim=$this->model->iletisimlist();
     $data->foto=$this->model->fotolist();
     $this->load->view('header',$data);
     $this->load->view('quality');
     $this->load->view('footer');

 }
 public function myadressupdate(){
     $data=new stdClass();
     $data->cities=$this->model->citieslist();
     $data->iletisim=$this->model->iletisimlist();
     $data->user=$this->session->userdata('users');
     $this->form_validation->set_rules('id','id','xss_clean');
     $this->form_validation->set_rules('user','user','required|xss_clean');
     $this->form_validation->set_rules('nick','nick','required|xss_clean');
     $this->form_validation->set_rules('mail','mail','required|xss_clean|valid_email');
     $this->form_validation->set_rules('tel','tel','required|xss_clean');
     $this->form_validation->set_rules('mail','mail','required|xss_clean');
     $this->form_validation->set_rules('city','city','required|xss_clean');
     $this->form_validation->set_rules('counties','counties','required|xss_clean');
     $this->form_validation->set_rules('adress','adress','required|xss_clean');
     $this->form_validation->set_rules('kargo','kargo','xss_clean');
     $this->form_validation->set_rules('fatura','fatura','xss_clean');

     if ($this->form_validation->run() !=FALSE){
         $post=new stdClass();
         $post->user=$this->input->post('user',true);
         $post->id=$this->input->post('id',true);
         $post->nick=$this->input->post('nick',true);
         $post->mail=$this->input->post('mail',true);
         $post->tel=$this->input->post('tel',true);
         $post->city=$this->input->post('city',true);
         $data->user=$this->session->userdata('users');
         $post->user_id=$data->user->id ;
         $post->counties=$this->input->post('counties',true);
         $post->adress=$this->input->post('adress',true);
         $post->kargo = $this->input->post('kargo', true);
         $post->fatura = $this->input->post('fatura', true);
         $this->result->status=true;
         if (!empty($post->kargo)) {
             $this->model->adresupdate($post);
         }
         if (!empty($post->fatura)){
             $this->model->adresupdatefatura($post);
         }
         $this->model->updateaddr($post);

     }else {
         $this->result->error = validation_errors();
     }
     $this->response();

  
 }
 public function adresval(){
            $data=new stdClass();
     $data->iletisim=$this->model->iletisimlist();
        if ($_POST) {
            $id = $_POST['id'];
            $adresvals = $this->model->addlist($id);
            $this->result->user = $adresvals->user;
            $this->result->id=$adresvals->id;
            $this->result->nick = $adresvals->nick;
            $this->result->mail = $adresvals->mail;
            $this->result->tel = $adresvals->tel;
            $this->result->countss=$this->model->countiess($adresvals->city);
            //prex($this->result->count);
            $this->result->cities = $adresvals->city;
            $this->result->counties = $adresvals->counties;
            $this->result->adress = $adresvals->adress;
            $this->result->fatura=$adresvals->fatura;
            $this->result->kargo=$adresvals->kargo;
            $this->result->status=true;
            $this->response();

        }
 }
public function like(){
    if ($_POST['id']){
        $post=new stdClass();
        $post->pro_id=$_POST['id'];
        $user=$this->session->userdata('users');
        $post->user_id=$user->id;
        $begen=$this->model->likeinsert($post);
        if (empty($begen)){
            $this->result->error="zaten begendın";
            $this->result->status=false;
        }
        if (!empty($begen)) {
            $this->result->status = true;
        }
    }$this->response();

}
public function mylikes(){
        if (!empty($this->session->userdata('users'))) {


            $data = new stdClass();
            $data->category = $this->model->categorylist();
            $data->user = $this->session->userdata('users');
            $user = $this->session->userdata('users');
            $data->dene = $this->model->likeslist($user->id);
            $data->iletisim=$this->model->iletisimlist();
            $this->load->view('header', $data);
            $this->load->view('my-favorites');
            $this->load->view('footer');
        }else{

            redirect(site_url('login'));
        }
}
public function commentinsert(){

            $this->form_validation->set_rules('user_name', 'user_name', 'required|xss_clean');
            $this->form_validation->set_rules('mail', 'mail', 'required|xss_clean');
            $this->form_validation->set_rules('comment', 'comment', 'required|xss_clean');
            $this->form_validation->set_rules('pro_id', 'pro_id', 'required|xss_clean');
            $this->form_validation->set_rules('puan', 'puan', 'required|xss_clean');
            if ($this->form_validation->run() != FALSE) {
                $post = new stdClass();
                $post->pro_id = $this->input->post('pro_id', true);
                $post->user_name = $this->input->post('user_name', true);
                $post->comment = $this->input->post('comment', true);
                $post->mail = $this->input->post('mail', true);
                $post->puan=$this->input->post('puan',true);
                $this->model->insertcomment($post);
                $this->result->status = true;

        }else{
                $this->result->error = validation_errors();
                $this->response();
            }
    $this->response();
}

public function taksit(){
    $this->load->library('Iyzico');
    $this->iyzico->taksit();

}
public function myorder(){
     $data=new stdClass();
     $data->user=$this->session->userdata('users');
     $data->donen1=$this->model->myorders($data->user->user);
     $data->donen=$this->model->myorderss($data->user->user);
     //prex($data->donen);
    $data->category=$this->model->categorylist();
    $data->iletisim=$this->model->iletisimlist();
     //prex($data->donen);
    $this->load->view('header',$data);
    $this->load->view('my-orders');
    $this->load->view('footer');
}
public function myorderdetail($id=''){
        $data=new stdClass();
    $data->category=$this->model->categorylist();
        $data->user=$this->session->userdata('users');
        $data->detail=$this->model->myortdetail($id);
        $data->paydetail=$this->model->mypaydet($id);
        //prex($data->paydetail);
        $data->product=$this->model->myprodet($id);


    $data->iletisim=$this->model->iletisimlist();
    $this->load->view('header',$data);
    $this->load->view('my-orders-detail');
    $this->load->view('footer');
}
public function talepolustur(){
    if ($_POST){
        $post=new stdClass();
        $post->order_id=$_POST['id'];
        $post->talep=1;
        $this->model->taleps($post);
        $this->result->status=true;
        $this->response();
    }
 
}
public function blogkategori($slug=''){
    $data=new stdClass();
    $data->blog=$this->model->blogliste2($slug);
    $data->category=$this->model->categorylist();
    //prex($data->category);
    $data->iletisim=$this->model->iletisimlist();
    //prex($data->blog);
    $this->load->view('header',$data);
    $this->load->view('blog-kategori');
    $this->load->view('footer');
}
public function deletelike(){
        $post=new stdClass();
        $post->user=$this->session->userdata('users');
        $post->pro_id=$_POST['id'];
        $this->model->deletelike($post);
        $this->result->status=true;
        $this->response();
 
}
public function arama(){
    $keyword=$this->input->get('aranan',true);
    $sonuc=$this->model->allcategor($keyword);
    //$this->allpro($sonuc);

}
public function kuponkullan(){
    if (!empty($_POST)) {
        $this->form_validation->set_rules('kupon','kupon','required|xss_clean');
        if ($this->form_validation->run() !=FALSE){
            $post=new stdClass();
            $post->indirim_kodu=$this->input->post('kupon',true);
            if ($indirim=$this->model->kuponkontrol($post)) {
                $ind_urunid=$this->model->kuponurunkontrol($indirim->id);
                $tarih=date('Y-m-d H:i:s');
                $array=[];
                $total=$this->cart->contents();
                $array3=[];
                foreach ($ind_urunid as $values){
                       array_push($array3,$values->product_id);
                }
                //pre($array3);
                //prex($this->cart->contents());
                $urun2=[];
                foreach (!empty($this->cart->contents()) ? $this->cart->contents() : [] as $val){
                    if (in_array($val['id'],$array3)){
                            array_push($urun2,$val['subtotal']);
                    }
                }
                $toplam=array_sum($urun2);
                if (intval($indirim->sepet_tutar) <= $this->cart->total()/100 && $tarih < $indirim->indirim_tarih&&intval($indirim->sepet_tutar)<=$toplam/100){
                    $this->session->set_userdata('indirim', $indirim);
                    $sessionindirim=$this->session->userdata('indirim');
                    $this->result->status = true;
                    $this->response();
                }
            }else{
                $this->result->error="KUPON KODU GEÇERSİZ";
                $this->response();
            }
        } else {
            $this->result->error = validation_errors();
            $this->response();
        }
    }
}



}
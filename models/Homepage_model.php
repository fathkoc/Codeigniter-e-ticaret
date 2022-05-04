<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homepage_model extends CI_Model {

    public function sliderlist(){
        $this->db->from('slider')->where(['deleted'=>0]);
        $this->db->order_by('position', 'ASC');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function categorylist(){
        $this->db->from('categori')->where(['deleted'=>0]);
        $this->db->order_by('position', 'ASC');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function probenzer($benzer){
        $this->db->from('product')->where(['deleted'=>0,'cat_id'=>$benzer]);
        $this->db->order_by('position', 'ASC');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function categorylistt(){
        $this->db->select('c.*,b.cat_id,
            (Select Count(*) FROM blog Where b.cat_id=c.id) as count
        ');
        $this->db->from('categori c')->where(['c.deleted'=>0]);
        $this->db->join('blog b','c.id=b.cat_id');
        $this->db->order_by('c.position','ASC');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function productlist(){
        $this->db->from('product')->where(['deleted'=>0]);
        $this->db->order_by('position', 'ASC');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function bloglist(){
        $this->db->select('b.*,
            (SELECT name FROM categori WHERE id = b.cat_id) as c_name
        ');
        $this->db->from('blog b')->where(['deleted'=>0]);
        $this->db->order_by('position', 'ASC');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }
    }
    public function blogdetail($slug){
        $this->db->select('b.*,
            (SELECT name FROM categori WHERE id = b.cat_id) as c_name
        ');
        $this->db->from('blog b')->where(['b.slug'=>$slug]);
        $this->db->join('categori c','b.cat_id=c.id');

        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->row();
        }
        else{
            return false;
        }
     
    }
    public function basketlist($id){
        $this->db->from('product')->where(['id'=>$id]);
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->row();
        }
        else{
            return false;
        }

    }
    public function kategoriproduct($post){
        $this->db->from('categori')->where(['id'=>$post]);
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->row();
        }
        else{
            return false;
        }
     
    }
    public function allcategor($config=[],$filtre='',$filtre2='',$keyword=''){
        $this->db->from('product')->where(['deleted'=>0]);
        $this->db->like('name', $keyword);
        if (!empty($filtre)) {
            $this->db->where(['price >' => $filtre['minimum'], 'price <' => $filtre['maximum']]);
        }
        if (!empty($filtre2)) {
            if ($filtre2['filtre'] == "1") {
                $this->db->order_by('price', 'ASC');
            } elseif ($filtre2['filtre'] == "2") {
                $this->db->order_by('price', 'DESC');
            } elseif ($filtre2['filtre'] == "3") {
                $this->db->order_by('name', 'ASC');
            } elseif ($filtre2['filtre'] == "4") {
                $this->db->order_by('name', 'DESC');
            }
        }

        $total_rows     =$this->db->count_all_results();
        $limit_start    = $config['current_page'] > 1 ? ($config['current_page'] - 1) * $config['page'] : 0;

        $this->db->from('product')->where(['deleted'=>0]);
        $this->db->like('name', $keyword);

        if (!empty($filtre)) {
            $this->db->where(['price >' => $filtre['minimum'], 'price <' => $filtre['maximum']]);
        }
        if (!empty($filtre2)) {
            if ($filtre2['filtre'] == "1") {
                $this->db->order_by('price', 'ASC');
            } elseif ($filtre2['filtre'] == "2") {
                $this->db->order_by('price', 'DESC');
            } elseif ($filtre2['filtre'] == "3") {
                $this->db->order_by('name', 'ASC');
            } elseif ($filtre2['filtre'] == "4") {
                $this->db->order_by('name', 'DESC');
            }
        }

        $return_query      = $this->db->limit($config['page'], $limit_start)->get();
        $data              = new stdClass;
        $data->current_val = ($config['current_page'] - 1) * $config['page'] + 1;
        $data->current_last = $data->current_val + $config['current_page'];
        $data->total_rows  = $total_rows;
        $data->data         = $return_query->result();
        return $data;


    }
    public function catdetail($config=[],$slug='',$filtre='',$filtre2=''){
        $this->db->select('p.*');
        $this->db->from('categori c')->where(['c.slug'=>$slug,'c.deleted'=>0,'c.status'=>1,'p.deleted'=>0]);
        $this->db->join('product p','c.id=p.cat_id');
        if (!empty($filtre)) {
            $this->db->where(['p.price >' => $filtre['minimum'], 'p.price <' => $filtre['maximum']]);
        }
        if (!empty($filtre2)) {
            if ($filtre2['filtre'] == "1") {
                $this->db->order_by('p.price', 'ASC');
            } elseif ($filtre2['filtre'] == "2") {
                $this->db->order_by('p.price', 'DESC');
            } elseif ($filtre2['filtre'] == "3") {
                $this->db->order_by('p.name', 'ASC');
            } elseif ($filtre2['filtre'] == "4") {
                $this->db->order_by('p.name', 'DESC');
            }
        }

        $total_rows     =$this->db->count_all_results();
        $limit_start    = $config['current_page'] > 1 ? ($config['current_page'] - 1) * $config['page'] : 0;

        $this->db->select('p.*');
        $this->db->from('categori c')->where(['c.slug'=>$slug,'c.deleted'=>0,'c.status'=>1,'p.deleted'=>0]);
        $this->db->join('product p','c.id=p.cat_id');
        if (!empty($filtre)) {
            $this->db->where(['p.price >' => $filtre['minimum'], 'p.price <' => $filtre['maximum']]);
        }
        if (!empty($filtre2)) {
            if ($filtre2['filtre'] == "1") {
                $this->db->order_by('p.price', 'ASC');
            } elseif ($filtre2['filtre'] == "2") {
                $this->db->order_by('p.price', 'DESC');
            } elseif ($filtre2['filtre'] == "3") {
                $this->db->order_by('p.name', 'ASC');
            } elseif ($filtre2['filtre'] == "4") {
                $this->db->order_by('p.name', 'DESC');
            }
        }
        $return_query      = $this->db->limit($config['page'], $limit_start)->get();
        $data              = new stdClass;
        $data->current_val = ($config['current_page'] - 1) * $config['page'] + 1;
        $data->current_last = $data->current_val + $config['current_page'];
        $data->total_rows  = $total_rows;
        $data->data         = $return_query->result();
        return $data;
    }
    public function register($post){
        $this->db->set($post)->insert('register');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
     
    }
    public function login($post){
        $this->db->from('register');
        $this->db->WHERE(['mail'=>$post->mail]);
        $return_query = $this->db->get();
        if($return_query->num_rows() > 0) {
            return $return_query->row();
        } else {
            return false;
        }
     
    }
    public function loginupdate($post){
        $this->db->set($post)->WHERE(['id'=>$post->id])->update('register');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }
    }
public function citieslist(){
    $this->db->from('cities');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function counties($citiesid){
    $this->db->from('counties')->where(['city_id'=>$citiesid]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }
 
}
public function adres($post){
    $this->db->set($post)->insert('adress');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function adresupdate($post){
    $this->db->set(['kargo'=>0])->where(['user_id'=>$post->user_id])->update('adress');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function adresupdatefatura($post){
    $this->db->set(['fatura'=>0])->where(['user_id'=>$post->user_id])->update('adress');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }
 
}
public function adreslist($user){
    $this->db->from('adress')->where(['user_id'=>$user->id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function orderinsert($post){
    $this->db->set($post)->insert('order_address');
    if($this->db->affected_rows() > 0){
        return $this->db->insert_id();
    }
    else{
        return false;
    }

}
public function order($post){
    $this->db->set($post)->insert('order_address');
    if($this->db->affected_rows() > 0){
        return $this->db->insert_id();
    }
    else{
        return false;
    }
 
}
public function buyinsert($array){
    $this->db->insert_batch('buylist',$array);
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }
}
public function orderno(){
    $this->db->from('order_address');
    $this->db->order_by('id', 'DESC');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function adlstfatra($user){
    $this->db->from('adress')->where(['user_id'=>$user->id,'fatura'=>1]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function addliste($user){
    $this->db->from('adress')->where(['user_id'=>$user->id,'kargo'=>1]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function ordinsert($post){
    $this->db->set($post)->insert('order_address');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function prodetails($slug){
    $this->db->from('product')->where(['slug'=>$slug,'status'=>1,'deleted'=>0]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function benzerproduct($slug){
    $this->db->from('product')->where(['slug'=>$slug]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function blosg(){
    $this->db->from('blog');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function insertcontact($post){
    $this->db->set($post)->insert('contact');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function forgpass($post){
    $this->db->set($post)->WHERE(['mail'=>$post->mail])->update('register');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function listpass($token){
    $this->db->from('register')->where(['token'=>$token]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

 
}
public function updatepas($post){
    $this->db->set($post)->WHERE(['token'=>$post->token])->update('register');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function tokensil($post){
    $this->db->set(['token'=>1])->WHERE(['token'=>$post->token])->update('register');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}

public function taglist(){
    $this->db->from('tag');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function updateaddr($post){
    $this->db->set($post)->WHERE(['id'=>$post->id])->update('adress');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function addlist($id){
    $this->db->from('adress')->where(['id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function countiess($cityid){
    $this->db->from('counties')->where(['city_id'=>$cityid]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function likeinsert($post){
    $this->db->from('likes');
    $this->db->where((array)$post);
    $return_query = $this->db->get();
    if ($return_query->num_rows() > 0) {
        return false;
    } else {
        $this->db->set($post)->insert('likes');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
public function likeslist($user){
    $this->db->select('p.*,l.user_id,l.pro_id');
    $this->db->from('product p')->where(['p.deleted'=>0,'l.user_id'=>$user]);
    $this->db->join('likes l','p.id=l.pro_id');
    $this->db->order_by('p.position', 'ASC');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function insertcomment($post){
    $this->db->set($post)->insert('comment');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function yorumlist($slug){
        $this->db->select('p.slug,p.id,c.*');
    $this->db->from('comment c')->where(['p.slug'=>$slug,'c.status'=>1]);
    $this->db->join('product p','c.pro_id=p.id');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function payinsert($post){
    $this->db->set($post)->insert('pay_info');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function succ($post){
    $this->db->set(['odeme'=>1])->WHERE(['id'=>$post])->update('pay_info');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function likekontrol(){
    
 
}
public function myorders($user){
    $this->db->select('p.*,b.order_id,b.durum,b.talep');
    $this->db->from('pay_info p')->where(['p.username'=>$user]);
    $this->db->join('buylist b','p.order_id=b.order_id');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }
 
}
public function myorderss($user){
        $this->db->select('b.talep,b.durum,o.total,o.shiping_name,o.id,p.sipno');
    $this->db->from('order_address o');
    $this->db->join('buylist b','o.id=b.order_id');
    $this->db->join('pay_info p','p.order_id=o.id');
    $this->db->where(['o.shiping_name'=>$user]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function myortdetail($id){
    $this->db->from('order_address')->where(['id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }
 
}
public function mypaydet($id){
    $this->db->from('pay_info')->where(['order_id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function myprodet($id){
        $this->db->select('p.prices,p.price,p.img_pet,b.product_id,b.product_name,b.qty');
    $this->db->from('buylist b')->where(['order_id'=>$id]);
    $this->db->join('product p','b.product_id=p.id');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function taleps($post){
    $this->db->set($post)->WHERE(['order_id'=>$post->order_id])->update('buylist');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function fotolist(){
    $this->db->from('foto');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function blogliste2($slug){
    $this->db->select('c.id,c.slug,b.name,b.cat_id,b.img_pet,b.content,b.slug as b_slug');
    $this->db->from('blog b');
    $this->db->join('categori c','b.cat_id=c.id');
    $this->db->where(['c.slug'=>$slug,'c.deleted'=>0,'b.deleted'=>0,'c.status'=>1,'b.status'=>1]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function deletelike($post){
    $this->db->where(['user_id'=>$post->user->id,'pro_id'=>$post->pro_id]);
    $this->db->delete('likes');

}
public function iletisimlist(){
    $this->db->from('contactinfo');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function regilist($data){
    $this->db->from('register')->where(['id'=>$data]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function stokguncelleme($array3){
    $this->db->update_batch('product',$array3,'id');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function probilgi($rows){
        $this->db->select('stok');
    $this->db->from('product')->where(['id'=>$rows]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row()->stok;
    }
    else{
        return false;
    }

}
public function productsliste($id){
    $this->db->from('product')->where(['id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function arama($keyword){
    //$this->db->select('*');
    $this->db->from('product');
    $this->db->like('name', $keyword);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }
 
}
public function inserttoken($post){
    $this->db->set($post)->insert('cerez');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function siparisno(){
    $this->db->from('pay_info');
    $this->db->order_by('id', 'DESC');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function kuponkontrol($post){
    $this->db->from('indirim')->where(['indirim_kodu'=>$post->indirim_kodu,'status'=>1,'deleted'=>0]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function kuponurunkontrol($id){
    $this->db->from('indirim_urunler')->where(['indirim_id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }
}
public function get_indirim($post){
    $this->db->from('indirim_urunler')->where(['indirim_id'=>$post]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}




}
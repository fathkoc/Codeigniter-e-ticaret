<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
public function siptakip(){
    $this->db->select('o.total,o.shipping_phone,o.shipping_mail,o.shiping_address,o.id,b.durum,p.status');
    $this->db->from('order_address o');
    $this->db->join('buylist b','b.order_id=o.id');
    $this->db->join('pay_info p','b.order_id=p.order_id');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function orddurumrow($id){
    $this->db->from('buylist')->where(['order_id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function orddetail($id){

    $this->db->from('buylist')->where(['order_id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }
}
public function faturadeail($id){
    $this->db->from('order_address o')->where(['id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }
 
}
public function sipdurum($post){
    $this->db->set($post)->WHERE(['order_id'=>$post->order_id])->update('buylist');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function ords($id){
    $this->db->from('buylist')->where(['order_id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function talepinsert($post){
    $this->db->set($post)->WHERE(['order_id'=>$post->order_id])->update('buylist');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function sipatakip($siptakip){
    $this->db->select('b.*,p.*');
    $this->db->from('buylist b')->where(['b.order_id'=>$siptakip[0]->id]);
    $this->db->join('pay_info p','b.order_id=p.order_id');
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}

}

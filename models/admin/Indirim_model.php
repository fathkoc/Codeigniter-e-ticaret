<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Indirim_model extends CI_Model
{

    public function product(){
        $this->db->from('product')->where(['deleted'=>0]);
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function indirimekle($post){
        $this->db->set($post)->insert('indirim');
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }
        else{
            return false;
        }

    }
    public function indirimurunekle($post2){
        $this->db->insert_batch('indirim_urunler',$post2);
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }

    }
    public function indirimliste(){
        $this->db->from('indirim')->where(['deleted'=>0]);
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function indirimstatus($post){
        $this->db->set($post)->WHERE(['id'=>$post->id])->update('indirim');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }

    }
    public function indirimdelete($post){
        $this->db->set(['deleted'=>1])->WHERE(['id'=>$post->id])->update('indirim');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }

    }
    public function indirimlist($id){
        $this->db->from('indirim')->where(['deleted'=>0,'id'=>$id]);
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->row();
        }
        else{
            return false;
        }

    }
    public function indirimguncelle($post){
        $this->db->set($post)->WHERE(['id'=>$post->id])->update('indirim');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }

    }
    public function indirimtoplu($array){
        $this->db->update_batch('indirimli_urunler',$array,'indirim_id');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }

    }
    public function secili($id){
        $this->db->select('p.name,p.id,i.product_id,i.indirim_id');
        $this->db->from('product p')->where(['p.deleted'=>0])->where(['i.indirim_id'=>$id]);
        $this->db->join('indirim_urunler i','i.product_id=p.id');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
}
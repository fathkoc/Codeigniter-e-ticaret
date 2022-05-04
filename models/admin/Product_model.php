<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
public function catlist(){
    $this->db->from('categori')->where(['deleted'=>0]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }
}

    public function productinsert($post){
        $this->db->set($post)->insert('product');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }

    }
    public function productlist(){
        $this->db->from('product')->where(['deleted'=>0]);
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function productliste($id){
        $this->db->from('product')->where(['id'=>$id]);
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->row();
        }
        else{
            return false;
        }

    }
    public function productupdate($post){
        $this->db->set(['status'=>$post->status])->WHERE(['id'=>$post->id])->update('product');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }

    }
    public function deletetupdate($post){
    $this->db->set(['deleted'=>1])->WHERE(['id'=>$post->id])->update('product');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function productpostiiton($post){
    $this->db->set($post)->WHERE(['id'=>$post->id])->update('product');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function productupdates($post){
    $this->db->set($post)->WHERE(['id'=>$post->id])->update('product');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}




}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model
{
public function categoryinsert($post){
    $this->db->set($post)->insert('categori');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function categorylist(){
    $this->db->from('categori')->where(['deleted'=>0]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function categoryupdate($post){
    $this->db->set($post)->WHERE(['id'=>$post->id])->update('categori');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }
}
public function categoryliste($id){
    $this->db->from('categori')->where(['id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function deletetupdate($post){
    $this->db->set(['deleted'=>1])->WHERE(['id'=>$post->id])->update('categori');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}

}
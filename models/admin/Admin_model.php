<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

public function sliderinsert($post){
    $this->db->set($post)->insert('slider');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }
}
public function sliderlist(){
    $this->db->from('slider')->where(['deleted'=>0]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function sliderupdate($post){
    $this->db->set($post)->WHERE(['id'=>$post->id])->update('slider');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }
 
}
public function sliderliste($id){
    $this->db->from('slider')->where(['id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function deletetupdate($post){
    $this->db->set(['deleted'=>1])->WHERE(['id'=>$post->id])->update('slider');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}


    

}
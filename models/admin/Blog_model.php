<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_model extends CI_Model
{
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
public function insertblog($post){
    $this->db->set($post)->insert('blog');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

}
public function listblog(){
    $this->db->from('blog')->where(['deleted'=>0]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->result();
    }
    else{
        return false;
    }

}
public function blogposition($post){
    $this->db->set($post)->WHERE(['id'=>$post->id])->update('blog');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function blogupdate($post){
    $this->db->set(['deleted'=>1])->WHERE(['id'=>$post->id])->update('blog');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}
public function bloglistele($id){
    $this->db->from('blog')->where(['id'=>$id]);
    $return_query=$this->db->get();
    if ($return_query->num_rows() > 0){
        return $return_query->row();
    }
    else{
        return false;
    }

}
public function blogupdates($post){
    $this->db->set($post)->WHERE(['id'=>$post->id])->update('blog');
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    else {
        return false;
    }

}


}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tag_model extends CI_Model
{
    public function taginsert($post){
        $this->db->set($post)->insert('tag');
        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }

    }
    public function taglist(){
        $this->db->from('tag');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function updatetag($post){
        $this->db->set(['deleted'=>1])->WHERE(['id'=>$post->id])->update('tag');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else {
            return false;
        }

    }
    public function listcontact(){
        $this->db->from('contact');
        $return_query=$this->db->get();
        if ($return_query->num_rows() > 0){
            return $return_query->result();
        }
        else{
            return false;
        }

    }
    public function contactekle($post){
    $this->db->set($post)->insert('contactinfo');
    if($this->db->affected_rows() > 0){
        return true;
    }
    else{
        return false;
    }

    }
}

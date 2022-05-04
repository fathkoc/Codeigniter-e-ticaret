<?php
defined('BASEPATH') or exit('No direct script access allowed');

class  Add_images extends CI_Controller {

    public function add_image()
    {
        $uploaded_images = [];
        $config['upload_path'] = 'assets/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = TRUE;
        $this->load->library('Upload', $config);
        if ($this->upload->do_upload('file')) {
            $image_session = $this->session->userdata('images');
            if ($image_session == false) {
                $uploaded_images = [];
            } else {
                $uploaded_images = $image_session;
            }
            $uploaded_images[] = 'assets/uploads/' . $this->upload->data('file_name');
            $this->session->set_userdata('images', $uploaded_images);
            pre($this->session->userdata('images'));
        } else {
            $this->output->set_status_header('404');
            print strip_tags($this->upload->display_errors());
            exit;
        }
    }

    public function cover_image()
    {
        $uploaded_images = [];
        $config['upload_path'] = 'assets/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = TRUE;
        $this->load->library('Upload', $config);
        if ($this->upload->do_upload('file')) {
            $image_session = $this->session->userdata('cover_images');
            if ($image_session == false) {
                $uploaded_images = [];
            } else {
                $uploaded_images = $image_session;
            }

            $uploaded_images[] = 'assets/uploads/'.$this->upload->data('file_name');
            $source = 'assets/uploads/'.$this->upload->data('file_name');
            $this->session->set_userdata('cover_images', $uploaded_images);
            pre($this->session->userdata('cover_images'));
        }
        else {
            $this->output->set_status_header('404');
            print strip_tags($this->upload->display_errors());
            exit;
        }
    }

    public function add_video()
    {
        $uploaded_video = [];
        $config['upload_path'] = 'assets/uploads/';
        $config['allowed_types'] = 'mp4';
        $config['encrypt_name'] = TRUE;
        $this->load->library('Upload', $config);
        if ($this->upload->do_upload('file')) {
            $video_session = $this->session->userdata('video');
            if ($video_session == false) {
                $uploaded_video = [];
            } else {
                $uploaded_video = $video_session;
            }

            $uploaded_video[] = 'assets/uploads/'.$this->upload->data('file_name');
            $this->session->set_userdata('video', $uploaded_video);
            pre($this->session->userdata('video'));
        }
        else {
            $this->output->set_status_header('404');
            print strip_tags($this->upload->display_errors());
            exit;
        }
    }

    public function add_image2()
    {
        $uploaded_images = [];
        $config['upload_path'] = 'assets/uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = TRUE;
        $this->load->library('Upload', $config);
        if ($this->upload->do_upload('file')) {
            $image_session = $this->session->userdata('images2');
            if ($image_session == false) {
                $uploaded_images = [];
            } else {
                $uploaded_images = $image_session;
            }

            $uploaded_images[] = 'assets/uploads/'.$this->upload->data('file_name');
            $source = 'assets/uploads/'.$this->upload->data('file_name');
            $this->session->set_userdata('images2', $uploaded_images);
            pre($this->session->userdata('images2'));
        }
        else {
            $this->output->set_status_header('404');
            print strip_tags($this->upload->display_errors());
            exit;
        }
    }

    public function add_icon()
    {
        $uploaded_images = [];
        $config['upload_path'] = 'assets/uploads/';
        $config['allowed_types'] = 'svg';
        $config['encrypt_name'] = TRUE;
        $this->load->library('Upload', $config);
        if ($this->upload->do_upload('file')) {
            $image_session = $this->session->userdata('icon');
            if ($image_session == false) {
                $uploaded_images = [];
            } else {
                $uploaded_images = $image_session;
            }

            $uploaded_images[] = 'assets/uploads/'.$this->upload->data('file_name');
            $this->session->set_userdata('icon', $uploaded_images);
            pre($this->session->userdata('icon'));
        }
        else {
            $this->output->set_status_header('404');
            print strip_tags($this->upload->display_errors());
            exit;
        }
    }
}
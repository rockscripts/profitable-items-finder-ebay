<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(true);
class DigitalItems extends CI_Controller {
	public function __construct()
        {
           parent::__construct();
           $this->load->model('Digitalitems_model');
           $this->load->helper(array('form', 'url'));
        } 
	public function index()
	{
	  $data = array();
	  $data["digital_items"] = $orders = $this->Digitalitems_model->get_items();
	  $this->template->load('administrator/display_digital_items',$data);
	}	
	public function add()
	{
	  if($this->input->post("digital_item_title")):
	  $data = array("title"=>$this->input->post("digital_item_title"));
	    $id = $this->Digitalitems_model->add($data);
		$data = array();
		if($id)
		{		  
		  $data["message"] = "Digital item has beed added successfully";		  
		  $data["digital_items"] = $orders = $this->Digitalitems_model->get_items();
	      $this->template->load('administrator/display_digital_items',$data);
		}
	  endif;
	}
	public function remove()
	{
		$data = array();
		if($this->input->post("id_digital_item")):
         $data = array("id_digital_item"=>$this->input->post("id_digital_item"));
		 $this->Digitalitems_model->remove($data);
		endif;
		$data = array();
		echo json_encode($data);
	}
	public function get_digital_item_to_add_html()
	{
		$data = array();
		 $data["digital_item_to_add_html"] = $this->template->ajax_load_view('add_digital_item',$data, true);
         echo json_encode($data);
	}
	
	public function cdkeys()
	{
		if($this->input->get("di")):	
          $data["item_digital_title"] = $this->Digitalitems_model->get_item_title($this->input->get("di"));
		  $data["cd_keys"] = $this->Digitalitems_model->get_cd_keys($this->input->get("di"));
		  $data["di"] = $this->input->get("di"); 
		  if($this->input->get("m")=="error"):
		  $data["message"] = "File not selected or invalid type.";
		  $data["message_type"] = "error";
		  else:
		   $data["message"] = "CD-Keys added successfully.";		  
		  endif;
		  $this->template->load('administrator/display_cd_keys',$data);
		endif;
	}
	public function remove_cd_key_single()
	{
		$data = array();
		if($this->input->post("id_cd_key")):
         $data = array("id_cd_key"=>$this->input->post("id_cd_key"));
		 $this->Digitalitems_model->remove_cd_key_single($data);
		endif;
		$data = array();
		echo json_encode($data);
	}
	public function get_cd_key_to_add_html()
	{
		 $data = array();
		 if($this->input->post("di")):
		 $data["id_digital_item"] = $this->input->post("di");
		 $data["cd_key_to_add_html"] = $this->template->ajax_load_view('add_cd_key',$data, true);
		 endif;
         echo json_encode($data);
	}
	public function add_key()
	{
	            $config['upload_path']          = './uploads/';
                $config['allowed_types']        = 'text|txt';
				$config['overwrite'] = TRUE;
                $keys = array();
                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('txt_file'))
                {
					   if($this->input->post("is_upload_pack")):
							 $data = array("key"=>$this->input->post("pack_keys"), "id_digital_item"=>$this->input->post("id_digital_item"));
		                         $this->Digitalitems_model->add_cd_key($data);	                        
							endif;
                      // redirect(base_url('index.php/administrator/DigitalItems/cdkeys?di='.$this->input->post("id_digital_item")."&m=error"));
                }
                else
                {
                        $data = array('upload_data' => $this->upload->data());
                        $handle = fopen($data["upload_data"]["full_path"], "r");
						if ($handle) {
							while (($line = fgets($handle)) !== false) {
								// process the line read.
								
								if($this->input->post("is_upload_pack")):
								 array_push($keys, trim($line));
								 else:
								 $data = array("key"=>trim($line), "id_digital_item"=>$this->input->post("id_digital_item"));
		                         $this->Digitalitems_model->add_cd_key($data);		
								endif;
														
							}
							if($this->input->post("is_upload_pack")):
							 $data = array("key"=>$this->input->post("pack_keys"), "id_digital_item"=>$this->input->post("id_digital_item"));
		                         $this->Digitalitems_model->add_cd_key($data);	                     
							endif;
							fclose($handle);
						} else {
							// error opening the file.
						} 
                       redirect(base_url('index.php/administrator/DigitalItems/cdkeys?di='.$this->input->post("id_digital_item")."&m=success"));
                }	
	}
	
}
 
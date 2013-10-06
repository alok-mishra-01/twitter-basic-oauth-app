<?php

class Pages extends CI_Controller {

        public function index()
	{
		$this->load->view('welcome_message');
	}
	
	public function view($page = 'authenticate')
	{
               if ( ! file_exists('application/views/pages/'.$page.'.php'))
            	{
            		// Whoops, we don't have a page for that!
            		show_404();
            	}
            
            	$data['title'] = ucfirst($page); // Capitalize the first letter
        
            	$this->load->view('templates/header', $data);
            	$this->load->view('pages/'.$page, $data);
            	$this->load->view('templates/footer', $data);
	}
}
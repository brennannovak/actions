<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:			Social Igniter : Actions : API Controller
* Author: 		Brennan Novak
* 		  		hi@brennannovak.com
* 
* Project:		http://social-igniter.com
* 
* Description: This file is for the Actions API Controller class
*/
class Api extends Oauth_Controller
{
    function __construct()
    {
        parent::__construct();
	}

    /* Install App */
	function install_authd_get()
	{
		// Load
		$this->load->library('installer');
		$this->load->config('install');
		$this->load->dbforge();

		// Create Data Table
		$this->dbforge->add_key('action_id', TRUE);
		$this->dbforge->add_field(config_item('database_actions_actions_table'));
		$this->dbforge->create_table('actions');

		// Settings & Create Folders
		$settings = $this->installer->install_settings('actions', config_item('actions_settings'));
	
		if ($settings == TRUE)
		{
            $message = array('status' => 'success', 'message' => 'Yay, the Actions App was installed');
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Dang Actions App could not be installed');
        }		
		
		$this->response($message, 200);
	} 
	
	function view_get()
    {
    	$this->load->model('data_model');

		$data	= $this->data_model->get_data($this->get('id'));    
   		 	
        if($data)
        {
            $message = array('status' => 'success', 'message' => 'Activity has been found', 'data' => $data);
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Could not find any Data');
        }

        $this->response($message, 200);
    }

    function create_authd_post()
    {    
    	$this->load->model('actions_model');

		$action_data = array(
			'user_id'			=> $this->oauth_user_id,
			'user_state'		=> $this->input->post('user_state'),
			'trigger'			=> $this->input->post('trigger'),
			'trigger_type'		=> $this->input->post('trigger_type'),
			'trigger_param'		=> $this->input->post('trigger_param'),
			'trigger_value'		=> $this->input->post('trigger_value'),
			'action'			=> $this->input->post('action'),
			'action_target'		=> $this->input->post('action_target'),
			'action_data'		=> $this->input->post('action_data')
		);

		// Add Data
		if ($add_action = $this->actions_model->add_action($action_data))
		{
        	$message = array('status' => 'success', 'message' => 'Action added successfully created', 'action' => $add_action);
        }
        else
        {
	        $message = array('status' => 'error', 'message' => 'Oops unable to add Action');
        }
	
        $this->response($message, 200);
    }
    
    function update_authd_get()
    {
    	$this->load->model('data_model');
    
    	$udpate_data = array(
    		'text'	=> $this->input->post('text')
    	);
    
		$update = $this->social_tools->update_data($this->get('id'), $update_data);			
    	
        if($update)
        {
            $message = array('status' => 'success', 'message' => 'Data was update');
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Could not update data');
        } 

        $this->response($message, 200);           
    }  

    function destroy_authd_get()
    { 
       	$this->load->model('data_model'); 
         
    	if ($this->data_model->delete_data($this->get('id')))
    	{   	
    		$message = array('status' => 'success', 'message' => 'Data was deleted');
    	}
    	else
    	{
    		$message = array('status' => 'error', 'message' => 'Oops Data was not deleted');        	
    	}
        
        $this->response($message, 200);
    }

    function cron_job_get()
    {
    	// Load Libraries for compatible Apps (foursquare, geoloqi, messages)
    	$this->load->library('foursquare/foursquare_library');
    	$this->load->model('actions/actions_model');
 
 
	    // Query Actions Table for "actions"
    	$actions = $this->actions_model->get_actions_view();
    	
    	print_r($actions);
	    
	    // Loop through "actions"
	    
	    	// Check "trigger_type" 
		    if ($action->trigger_type == 'foursquare')
		    {
		    	$checkins = $this->foursquare_library->get_checkins();
		    	
		    	// Process "trigger_detail"
		    	
		    	if ($this->places_igniter->is_geo_within_fence($checkin->lat, $checkin->lon, $action->fence))
		    	{
			    		
			    	// Now do "action" (send SMS or Email)	
			    	
		    	}
		    	
		    }
	    
    }
    
}
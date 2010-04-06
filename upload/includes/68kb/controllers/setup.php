<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * iClassEngine
 *
 * THIS IS COPYRIGHTED SOFTWARE
 * PLEASE READ THE LICENSE AGREEMENT
 * http://iclassengine.com/user_guide/license.html
 *
 * @package		iClassEngine
 * @author		ICE Dev Team
 * @copyright	Copyright (c) 2010, 68 Designs, LLC
 * @license		http://iclassengine.com/user_guide/license.html
 * @link		http://iclassengine.com
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Setup Controller
 *
 *
 * @subpackage	Controllers
 * @link		http://iclassengine.com/user_guide/
 *
 */
class Setup extends Controller
{
	/**
	 * Script Version
	 */
	var $script_version = '##VERSION##';
	
	/**
	 * Build Version
	 */
	var $build_version = '##BUILD##';
	
	/**
	 * Array of template data
	 */
	var $data = array();
	
	/**
	 * Hold any errors
	 */
	var $errors = array();
	
	/**
	 * Is the db installed? We assume it isn't
	 */
	var $is_installed = FALSE;
	
	/**
	 * Min PHP Version
	 */
	var $min_php = '5.1';
	
	/**
	 * Min MySQL
	 */
	var $min_mysql = '';
	
	
	/**
	 * Setup the setup. :-) 
	 */
    function __construct()
	{
		parent::__construct();
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		// See if the db is already installed.
		if ($this->db->table_exists('settings'))
		{
			$this->is_installed = TRUE;
		}
		
		// Check we are running the min version of php.
		if (is_php($this->min_php) == FALSE)
		{
 			$this->errors[] = 'PHP version '. $this->min_php .' or higher required.';
		}
		
		// Set the version
		$this->data['version'] = $this->script_version;
	}
    
	// ------------------------------------------------------------------------
	
	/**
	 * This is the start of the setup. 
	 */
	function index()
	{
		// Before we can enter the setup routine we must have a valid license
		if ( ! $license_key = $this->config->item('license_key'))
		{
			$this->errors[] = 'Please enter you license key in includes/config.php';
		}
		
		// These should never be blank because they are hidden fields. 
		$this->form_validation->set_rules('license_key', 'License Key', 'required|trim|callback_key_check');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->data['body'] = $this->load->view('setup/index', $this->data, TRUE);
			$this->load_template();
		}
		else
		{
			if ($this->is_installed)
			{
				redirect('setup/upgrade');
			}
			else
			{
				redirect('setup/install');
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Validate the key entered is correct.
	 *
	 * @param	string - Key
	 * @return 	bool
	 */
	function key_check($key)
	{
		if ($key != $this->config->item('license_key'))
		{
			$this->form_validation->set_message('key_check', 'The license key you entered is not correct.');
			return FALSE;
		}
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Install
	 * 
	 * Perform the install.
	 */
	function install()
	{
		$this->data['cache'] 				= $this->_writable(APPPATH.'cache');
		$this->data['uploads'] 				= $this->_writable(ROOTPATH.'uploads');
		$this->data['cat_image_path'] 		= $this->_writable(ROOTPATH.$this->config->item('cat_image_path'));
		$this->data['listing_image_path'] 	= $this->_writable(ROOTPATH.$this->config->item('listing_image_path'));
		
		// Setup form validation
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		
		// Handle Validation
		if ($this->form_validation->run() == FALSE)
		{
			$this->data['body'] = $this->load->view('setup/install', $this->data, TRUE);
			$this->load_template();
		}
		else
		{
			// Assign out the posted data
			$this->data['username'] = $this->input->post('username', TRUE);
			$this->data['password'] = $this->input->post('password', TRUE);
			$this->data['email'] = $this->input->post('email', TRUE);
			$this->data['sample'] = $this->input->post('sample', TRUE);
			
			// Now install the database.
			$this->_install_db();
			
			// Do they want sample data?
			if ($sample = $this->input->post('sample', TRUE))
			{	
				$this->_process_sql($sample);
			}
			
			$data = array( 
				'option_value' => $this->input->post('site_name')
			);
			$this->db->where('option_name', 'site_name');
			$this->db->update('settings', $data);
			
			$data = array( 
				'option_value' => $this->data['email']
			);
			$this->db->where('option_name', 'site_email');
			$this->db->update('settings', $data);
			
			$data = array( 
				'option_value' => $this->build_version
			);
			$this->db->where('option_name', 'script_build');
			$this->db->update('settings', $data);
			
			// Load the last setup template.
			$this->data['body'] = $this->load->view('setup/complete', $this->data, TRUE);
			$this->load_template();
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Run the install
	*
	*/
	private function _install_db()
	{
		$this->load->library('migrate');
		$this->migrate->setverbose(TRUE);
		
		$this->data['log'] = $this->migrate->install();
		
		$this->load->library('cache');
		$this->load->library('addons/events');
		$this->load->model('users/users_model');
		
		// Update version
		//$this->settings->edit_setting('script_version', $this->script_version);
		
		$insert_data = array(
			    'user_email' => $this->data['email'],
			    'user_username' => $this->data['username'],
			    'user_password' => $this->data['password'],
			    'user_join_date' => time(),
				'user_group'	=> 1
			);
		$this->users_model->add_user($insert_data);
		
		// Now add version data
		$version_data['option_value'] = $this->script_version;
		$this->db->where('option_name', 'script_version');
		$this->db->update('settings', $version_data);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Do the upgrade
	*
	* @access	public
	*/
	function upgrade()
	{
		$this->load->library('migrate');
		$this->migrate->setverbose(TRUE);
		$this->data['log'] = $this->migrate->version();
		
		// now maintenance
		$version_data['option_value'] = $this->script_version;
		$this->db->where('option_name', 'script_version');
		$this->db->update('settings', $version_data);
		
		$data = array( 
			'option_value' => $this->build_version
		);
		$this->db->where('option_name', 'script_build');
		$this->db->update('settings', $data);
		
		//optimize db
		$this->load->dbutil();
		$this->dbutil->optimize_database();

		//delete cache
		$this->load->helper('file');
		delete_files($this->config->item('cache_path'));
		$this->load->library('settings/settings');
		$this->load->library('cache');
		$this->cache->delete_all();
		
		// Load the last setup template.
		$this->data['body'] = $this->load->view('setup/complete', $this->data, TRUE);
		$this->load_template();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Check if a file is writable.
	 *
	 * @param	string The file name.
	 * @access	private
	 */
	private function _writable($filename) 
	{
		if ( ! is_really_writable($filename)) 
		{
			$this->data['error'] = TRUE;
			return '<div class="fail">Not Writable</div>';
		}
		return '<div class="pass">Passed</div>';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Checks to see if any errors exists.
	 *
	 * @return 	boolean True or False
	 */
	function is_error()
	{
		return (sizeof($this->errors) > 0) ? TRUE : FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get the error list
	 *
	 * @return 	array
	 */
	function get_error_list()
	{
		return $this->errors;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Load the layout view file.
	 */
	function load_template()
	{
		if ($this->is_error())
		{
			$this->data['errors'] = $this->get_error_list();
		}
		$this->load->view('setup/layout.php', $this->data);
	}
	
	// ------------------------------------------------------------------------
	
	private function _process_sql($sql_file)
	{
		$file = ROOTPATH .'setup/sample_data/'.$sql_file.'.sql';
		
		if ( ! file_exists($file))
		{
			echo 'file not found';
			return FALSE;
		}
		
		$schema = file_get_contents($file);
		
		$schema = str_replace('{prefix}', $this->db->dbprefix, $schema);
		
		$queries = explode('-- query --', $schema);
		
		foreach($queries as $query)
		{
			$query = rtrim( trim($query), "\n;");
			$this->db->query($query);
		}
		
		return TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	private function _drop_tables()
	{
		$this->load->dbforge();
		
		$tables = $this->db->list_tables();
		
		foreach ($tables as $table)
		{
			$table = str_replace($this->db->dbprefix, '', $table);
			$this->dbforge->drop_table($table);
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Migrate to a particular version
	 *
	 * This is mainly for dev purposes. 
	 *
	 */
	function version($id = NULL)
	{
		$this->load->library('migrate');
		$this->migrate->setverbose(TRUE);
		
		if (is_null($id)) 
		{
			show_error("Must pass in an id.");
		}
		
		$migrate = $this->migrate->version($id);
		
		if ( ! $migrate)
		{
			show_error($this->migrate->error);
		}
		else
		{
			echo $migrate ." <br />Migration Successful<br />";
		}
	}
}

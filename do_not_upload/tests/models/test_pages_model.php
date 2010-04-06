<?php
class test_pages_model extends CodeIgniterUnitTestCase
{
	protected $_ci = '';
	
	function __construct()
	{
		parent::__construct();
		
		$this->UnitTestCase('Pages Model');
		
		$this->_ci->load->model('pages/pages_model');
		$this->_ci->db->truncate('pages'); 
	}

	function setUp()
	{
		$this->_ci->db->flush_cache();
    }

    function tearDown()
	{
		
    }
	
	public function test_included()
	{
		$this->assertTrue(class_exists('pages_model'));
	}
	
	public function test_crud()
	{
		$data = array(
			'page_title' 		=> 'Testing',
			'page_content' 		=> 'Just some content',
			'page_date_added' 	=> time(),
			'page_date_edited' 	=> time(),
			'page_status' 		=> 'active',
			'page_nav' 			=> 'yes',
			'page_parent'		=> 0
		);
		$var = $this->_ci->pages_model->add_page($data);
		$this->assertTrue($var, 'Added Page');
		
		$this->_ci->db->flush_cache();
		$data = array(
			'page_title' 		=> 'Testing Editing',
			'page_uri' 			=> 'testing_editing',
			'page_content' 		=> 'Just some content',
			'page_date_edited' 	=> time(),
			'page_status' 		=> 'active',
			'page_nav' 			=> 'yes',
			'page_parent'		=> 0
		);
		$var = $this->_ci->pages_model->edit_page(1, $data);
		$this->assertTrue($var, 'Edited Page');
		
		$this->_ci->db->flush_cache();
		$var = $this->_ci->pages_model->delete_page(1);
		$this->assertTrue($var, 'Deleted Page');
	}
	
	public function test_uri()
	{
		$this->_ci->db->truncate('pages');
		
		$this->_ci->db->flush_cache();
		
		$data = array(
			'page_title' 		=> 'Testing',
			'page_content' 		=> 'Just some content',
			'page_date_added' 	=> time(),
			'page_date_edited' 	=> time(),
			'page_status' 		=> 'active',
			'page_nav' 			=> 'yes',
			'page_parent'		=> 0
		);
		$var = $this->_ci->pages_model->add_page($data);
		$this->assertTrue($var, 'Added Page');
		
		$var = $this->_ci->pages_model->get_page(1);
		$this->assertEqual($var['page_uri'], 'testing', 'Testing');
		
		$this->_ci->db->flush_cache();
		$data = array(
			'page_title' 		=> 'áéíóúàèìòùñäëïöü',
			'page_date_edited' 	=> time(),
			'page_parent'		=> 0
		);
		$var = $this->_ci->pages_model->edit_page(1, $data);
		$this->assertTrue($var, 'Edited Page');
		
		$var = $this->_ci->pages_model->get_page(1);
		$this->assertEqual($var['page_uri'], 'aeiouaeiounaeiou', 'Accents');
	}
}
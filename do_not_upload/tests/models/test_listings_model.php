<?php
class test_listings_model extends CodeIgniterUnitTestCase
{
	protected $_ci = '';
	
	var $rand = '';
	
	var $listing_id = '';
	
	function __construct()
	{
		parent::__construct();
		
		$this->UnitTestCase('Listings Model');
		
		$this->_ci->load->model('listings/listings_model');
		
		$this->rand = rand(500,15000);
	}

	function setUp()
	{
		$this->_ci->db->flush_cache();
		
		$this->_ci->db->truncate('listings'); 
		$this->_ci->db->truncate('listing_fields'); 
		
		$insert_data = array(
			    'listing_title' 		=> 'Test Listing',
				'listing_description' 	=> 'description',
				'listing_owner_id'		=> 1,
				'listing_cart_id'		=> 1,
				'listing_product_id' 	=> 1,
				'listing_category' 		=> 1,
				'listing_added' 		=> time(),
				'listing_modified' 		=> time(),
				'listing_expiration' 	=> time()+3000,
				'listing_cookie_id'		=> '',
				'listing_status'		=> 1,
			);
		$listing_id = $this->_ci->listings_model->add_listing($insert_data);
		// Insert any fields
		$fields_array = array();
		$fields = array('listing_field_id' => $listing_id);
		$fields_data = array_merge($fields, $fields_array);
		$this->_ci->listings_model->add_fields($fields_data);
    }

    function tearDown()
	{
       
    }

	public function test_included()
	{
		$this->assertTrue(class_exists('listings_model'));
	}
	
	function test_add_listing()
	{
		$this->_ci->db->truncate('listings'); 
		$this->_ci->db->truncate('listing_fields'); 
		$insert_data = array(
			    'listing_title' 		=> 'Test Listing',
				'listing_description' 	=> 'description',
				'listing_owner_id'		=> 1,
				'listing_cart_id'		=> 1,
				'listing_product_id' 	=> 1,
				'listing_category' 		=> 1,
				'listing_added' 		=> time(),
				'listing_modified' 		=> time(),
				'listing_expiration' 	=> time()+3000,
				'listing_cookie_id'		=> '',
				'listing_status'		=> 1,
			);
		$listing_id = $this->_ci->listings_model->add_listing($insert_data);
		// Insert any fields
		$fields_array = array();
		$fields = array('listing_field_id' => $listing_id);
		$fields_data = array_merge($fields, $fields_array);
		$this->_ci->listings_model->add_fields($fields_data);
		
		//$this->dump($user_id);
		$this->assertEqual($listing_id, 1, 'listing id = 1');
	}
	
	function test_add_hit()
	{
		$this->_ci->db->flush_cache();
		$this->_ci->listings_model->add_hit(1);
		$this->_ci->listings_model->add_hit(1, 'listing_search');
		
		$listing = $this->_ci->listings_model->get_listing(1);
		// $this->dump($listing);
		$this->assertEqual($listing['listing_hits'], 1, 'Listing Hit');
		$this->assertEqual($listing['listing_search_hits'], 1, 'Search Hit');
	}
	
	public function test_get_listing()
	{
		$this->assertTrue($this->_ci->listings_model->get_listing(1));
	}
}
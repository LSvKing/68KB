<?php
class test_helper_listing extends CodeIgniterUnitTestCase
{
	protected $_ci = '';

	function __construct()
	{
		parent::__construct();

		$this->UnitTestCase('Listing Helper');
		$this->_ci->load->helper('listings/listing');
		$this->_ci->load->model('listings/listings_model');
	}

	function setUp()
	{
		$this->_ci->db->truncate('listings'); 
    }

    function tearDown()
	{
		
    }

	public function test_included()
	{
		$this->assertTrue(function_exists('listing_title'));
	}
	
	public function test_listing_title()
	{
		$data = array(
			'listing_title' 		=> 'Test Listing',
			'listing_description' 	=> 'description',
			'listing_owner_id'		=> 1,
			'listing_cart_id'		=> 1,
			'listing_product_id' 	=> 1,
			'listing_category' 		=> 1,
			'listing_added' 		=> time(),
			'listing_modified' 		=> time(),
			'listing_cookie_id'		=> '',
			'listing_status'		=> 5,
		);
		$var = $this->_ci->listings_model->add_listing($data);
		$this->assertTrue($var, 'Added Listing');
		$this->assertEqual(listing_title(1), 'Test Listing');
	}
	
	public function test_total_listings()
	{
		$data = array(
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
		$var = $this->_ci->listings_model->add_listing($data);
		$this->assertTrue($var, 'Added Listing');
		$this->assertEqual(total_listings(TRUE), 1);
	}
}
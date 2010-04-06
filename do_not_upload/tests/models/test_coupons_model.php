<?php
class test_coupons_model extends CodeIgniterUnitTestCase
{
	protected $_ci = '';
	
	function __construct()
	{
		parent::__construct();
		
		$this->UnitTestCase('Coupons Model');
		
		$this->_ci->load->model('coupons/coupons_model');
	}

	function setUp()
	{
		//$this->_ci->db = new fake_DB();
		//$this->_ci->coupons_model->db = $this->_ci->db;
    }

    function tearDown()
	{
		
    }

	public function test_included()
	{
		$this->assertTrue(class_exists('coupons_model'));
	}
	
	public function test_add_coupon()
	{
		$this->_ci->db->truncate('coupons'); 
		$insert_data = array(
			'coupon_name' 		=> 'test',
			'coupon_code' 		=> 'test',
			'coupon_start_date' => time(),
			'coupon_end_date' 	=> time()+10000,
			'coupon_discount' 	=> '10',
			'coupon_type' 		=> 'percentage',
			'coupon_available' 	=> '5',
			'coupon_group' 		=> 0,
			'coupon_user_id' 	=> 0
		);
		$id = $this->_ci->coupons_model->add_coupon($insert_data);
		$this->assertEqual($id, 1, 'coupon id = 1');
	}
	
}
/* End of file test_coupons_model.php */
/* Location: ./do_not_upload/tests/models/test_coupons_model.php */ 
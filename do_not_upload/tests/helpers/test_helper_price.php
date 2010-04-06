<?php
class test_helper_price extends CodeIgniterUnitTestCase
{
	protected $_ci = '';

	function __construct()
	{
		parent::__construct();

		$this->UnitTestCase('Price Helper');
	}

	function setUp()
	{

    }

    function tearDown()
	{
		
    }

	function test_str_to_decimal_usd_correct()
	{	
		$price = '2199.99';
		$currency_decimal = '.';
		$currency_thousands = ',';
		$var = str_to_decimal($price, $currency_decimal, $currency_thousands);
		$this->assertEqual($var, '2199.99');
	}
	
	function test_str_to_decimal_comma()
	{
		$price = '2,199.99';
		$currency_decimal = '.';
		$currency_thousands = ',';
		$var = str_to_decimal($price, $currency_decimal, $currency_thousands);
		$this->assertEqual($var, '2199.99');
	}
	
	function test_str_to_decimal_comma_cents()
	{
		$price = '219999';
		$currency_decimal = ',';
		$currency_thousands = '.';
		$var = str_to_decimal($price, $currency_decimal, $currency_thousands);
		$this->assertEqual($var, '219999.00');
	}
	
	function test_str_to_decimal_euro_formated()
	{
		$price = '2199.99';
		$this->_ci->config->set_item('currency_symbol', '&euro;');
		$this->_ci->config->set_item('currency_decimal_places', '2');
		$this->_ci->config->set_item('currency_decimal', ',');
		$this->_ci->config->set_item('currency_thousands', '.');
		$this->_ci->config->set_item('currency_after', TRUE);
		$var = format_money($price);
		$this->assertEqual($var, '2.199,99&euro;');
	}
	
	function test_str_to_decimal_euro()
	{
		$price = '2199,99';
		$currency_decimal = ',';
		$currency_thousands = '.';
		$var = str_to_decimal($price, $currency_decimal, $currency_thousands);
		$this->assertEqual($var, '2199.99');
	}
}
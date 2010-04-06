<?php
class test_admin_login_view extends CodeIgniterWebTestCase
{
	protected $_ci = '';
	
	var $user = '';
	
	function __construct()
	{
		parent::__construct();
		
		$this->_ci =& get_instance();
		// $this->UnitTestCase('Auth Library');
		
	}

	function setUp()
	{
		
    }

    function tearDown()
	{
        
    }
 	
	function test_login() 
	{
		$this->ageCookies(7400);
		$this->restart();
		$this->get(site_url('admin/login'));
        $session = $this->getCookie('icelogin');
        $this->setField('username', 'demo');
        $this->setField('password', 'demo');
        $this->setField('remember', 'y');
        $this->click('Login');
        $this->assertText('New Orders');
        $this->assertCookie('icelogin', $session);
    }

	function testLoseAuthenticationAfterBrowserClose() 
	{
		$this->get(site_url('admin/login'));
		$this->setField('username', 'demo');
        $this->setField('password', 'demo');
		$this->click('Login');
		$this->assertText('New Orders');
		
		$this->ageCookies(7400);
		$this->restart();
		$this->get(site_url('admin'));
		$this->assertText('Please Login');
	}

	
}
<?php
namespace Orange\QuickMakingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class BaseControllerTest extends WebTestCase
{	
	/**
	 * @array
	 */
	protected $connexion;
	
	/**
	 * @var Session
	 */
	protected $session;
	
	/**
	 * @var Container
	 */
	protected $container;
	
	/**
	 * @var Request
	 */
	protected $request;
	
	
	public function setUp() {
		parent::setUp();
		self::$kernel = static::createKernel();
		self::$kernel->boot();
		$this->container = self::$kernel->getContainer();
		$this->request = new \Symfony\Component\HttpFoundation\Request();
		$this->session = new Session(new MockArraySessionStorage());
		$this->container->set('session',  $this->session);
	}
	
	/**
	 * @dataProvider urlProvider
	 */
	public function testActionWithGet($url) {
		$client = self::createClient();
		$client->getContainer()->get('session')->set('export_data', array());
		$connexion = array(
				'username' => isset($this->connexion['username']) ? $this->connexion['username'] : null, 
				'password' => isset($this->connexion['password']) ? $this->connexion['password'] : null
			);
		$this->doLogin($connexion['username'], $connexion['password'], $client);
		$client->request('GET', $url);
		$this->assertContains($client->getResponse()->getStatusCode(), array(200, 302), $client->getResponse()->getContent());
	}

	/**
	 * @return array 
	 */
	public function urlProvider() {
		return array();
	}
	
	/**
	 * @param string $username
	 * @param string $password
	 * @param Client  $client
	 */
	public function doLogin($username, $password, $client) {
		$crawler = $client->request('GET', '/login');
		$form = $crawler->selectButton('_submit')->form();
		$client->submit($form,array('_username'  => $username,'_password'  => $password));
	}
	
}

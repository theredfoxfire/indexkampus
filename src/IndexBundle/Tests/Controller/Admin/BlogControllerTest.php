<?php

namespace IndexBundle\Test\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use IndexBundle\Entity\Post;

class BlogControllerTest extends WebTestCase
{
	public function testRegularUsersCannotAccesToTheBackend()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'john_user',
			'PHP_AUTH_PW' => 'kitten',
		));

		$client->request('GET', 'en/admin/post/');

		$this->assertEquals(Response::HTTP_FORBIDEN, $client->getResponse()->getStatusCode());
	}

	public function testAdministratorUsersCanAccessToTheBackend()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'anna_admin',
			'PHP_AUTH_PW' => 'kitten',
		));

		$client->request('GET', '/en/admin/post');

		$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
	}

	public function testIndex()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'anna_admin',
			'PHP_AUTH_PW' => 'kitten',
		));

		$crawler = $client->request('GET', '/en/admin/post');

		$this->assertCount(
			Post:NUM_ITEMS,
			$crawler->filter('body#admin_post_index #main body tr'),
			'The backend homepage displays the right number of posts.'
		);
	}
}
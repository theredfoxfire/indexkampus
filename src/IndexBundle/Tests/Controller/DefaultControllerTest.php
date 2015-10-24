<?php

namespace IndexBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $url);

        $this->assertTrue(
        	$crawler->getResponse()->isSuccessful(),
        	sprintf('The %s public URL loads correctly.', $url)
        );

        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }

    public function testSecureUrls($url)
    {
    	$client = self::createClient();
    	$client->request('GET', $url);

    	$this->assertTrue($client->getResponse()->isRedirect());

    	$this->asserEquals(
    		'http://localhost/indexkampus/web/app_dev.php/en/login',
    		$client->getResponse()->getTargetUrl(),
    		sprintf('The %s secure URL redirects to the login form.', $url)
    	);
    }

    public function getPublicUrls()
    {
    	return array(
    		array('/'),
    		array('/en/blog'),
    		array('/en/blog/posts/morbi-tempus-commodo-mattis'),
    		array('/en/login'),
    	);
    }

    public function getSecureUrls()
    {
    	return array(
    		array('/en/admin/post/'),
    		array('/en/admin/post/new'),
    		array('/en/admin/post/1'),
    		array('/en/admin/post/1/edit'),
    	);
    }
}

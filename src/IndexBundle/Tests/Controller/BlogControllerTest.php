<?php

namespace IndexBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use IndexBundle\Entity\Post;

class BlogControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/index');

        $this->assertCount(
        	Post::NUM_ITEMS,
        	$crawler->filter('article.post'),
        	'The homepage display the right number of post.'
        );
    }

}

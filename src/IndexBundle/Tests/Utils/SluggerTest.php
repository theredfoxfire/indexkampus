<?php

namespace IndexBundle\Tests\Utils;

use IndexBundle\Utils\Slugger;

class SluggerTest extends \PhpUnit_Framework_TestCase
{
	public function testSlugify($string, $slug)
	{
		$slugger = new Slugger();
		$result = $sluger->slugify($string);

		$this->asserEquals($slug, $result);
	}

	public function getSlugs()
	{
		return array(
			array('Lorem Ipsum' , 'lorem-ipsum'),
			array(' Lorem Ipsum' , 'lorem-ipsum'),
			array('lorEm iPsUm', 'lorem-ipsum'),
			array('!Lorem Ipsum!', 'lorem-ipsum'),
			array('lorem-ipsum', 'lorem-ipsum'),
		);
	}
}
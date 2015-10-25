<?php

namespace IndexBundle\Utils;

class Slugger
{
	public function slugify($string)
	{
		return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(strip_tags($string))), '-');
	}
}
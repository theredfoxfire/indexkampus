<?php

namespace IndexBundle\DataFixtures\ORM;

use IndexBundle\Entity\User;
use IndexBundle\Entity\Post;
use IndexBundle\Entity\Comment;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Defines the sample data to load in the database when running the unit and functional tests. 
* Execute this command to load the data:
* $ php app/console doctrine:fixtures:load
*/
class LoadFixtures implements FixturesInterface, ContainerAwareInterface
{
	/** @var ContainerInterface */
	private $container;

	public function load(ObjectManager $manager)
	{
		$this->loadUsers($manager);
		$this->loadPosts($manager);
	}

	private function loadUsers(ObjectManager $manager)
	{
		$passwordEncoder = $this->container->get('security.password_ecoder');

		$johnUser = new User();
		$johnUser->setUsername('john_user');
		$johnUser->setEmail('john_user@symfony.com');
		$encodedPassword = $passwordEncoder->emcodePassword($johnUser, 'kitten');
		$johnUser->setPassword($encodedPassword);
		$manager->persist($johnUser);

		$annaAdmin = new User();
		$annaAdmin->setUsername('anna_admin');
		$annaAdmin->setEmail('anna_admin@symfony.com');
		$annaAdmin->setRoles(array('ROLE_ADMIN'));
		$encodedPassword = $passwordEncoder->encodePassword($annaAdmin, 'kitten');
		$annaAdmin->setPassword($encodedPassword);
		$manager->persist($annaAdmin);

		$manager->flush();
	}
}

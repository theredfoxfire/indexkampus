<?php

namespace IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
* Controller used to manage the application security.
*/
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login_form")
     * @Template()
     */
    public function loginAction()
    {
    	$helper = $this->get('security.authentication_utils');

        return $this->render('security/login.html.twig', array(
        		//last username entered by the user (if any)
        		'last_username' => $helper->getLastUsername(),
        		//last authentication error (if any)
        		'error' => $helper->getLastAuthenticationError(),
        	));
	}

	/**
	* This is the route the login form submits to.
	* @Route("/login_check", name="security_login_check")
	*/
	public function loginCheckAction()
	{
		throw new \Exception('This should never be reached!');
	}

	/**
	* This is the route the user can use to logout.
	* @Route("/logout", name="security_logout")
	*/
	public function logoutAction()
	{
		throw new \Exception('This should never be reached!');
	}
}

<?php

namespace IndexBundle\Controller;

use IndexBundle\Entity\Comment;
use IndexBundle\Entity\Post;
use IndexBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;


/**
* Controller used to manage blog contents in the public part of the site
*
* @Route("/blog")
*/
class BlogController extends Controller
{
	/**
	* @var object DoctrineEntityManager
	*/
	private $em;

    /**
     * @Route("/", name="blog_index")
     */
    public function indexAction()
    {
    	$this->em = $this->getDoctrine()->getManager();
    	$posts = $this->em->getRepository('IndexBundle:Post')->findLatest();

        return $this->render('blog/index.html.twig', array('post' => $posts));
    }

    /**
    * @Route("/posts/{slug}". name="blog_post")
    */
    public function postShowAction(Post $post)
    {
    	return $this->render('blog/post_show.html.twig', array('post' => $post));
    }

    /**
    * @Route("/comment/{postSlug}/new", name="comment_new")
    * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
    * 
    * @Method("POST")
    * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
    */
    public function commentNewAction(Request $request, Post $post)
    {
    	$form = $this->createCommentForm();

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		/** @var Comment $comment */
    		$comment = $form->getData();
    		$comment->setAuthorEmail($this->getUser()->getEmail());
    		$comment->setPost($post);
    		$comment->setPublishedAt(new \DateTime());

    		$this->em->persist($comment);
    		$this->em->flush();

    		return $this->redirectToRoute('blog_post', array('slug' => $post->getSlug()));
    	}

    	return $this->render('blog/comment_form_error.html.twig', array(
    			'post' => $post,
    			'form' => $form->createView(),
    		));
    }

    /**
    * @param Post $post
    * @return Response
    */
    public function commentFormAction(Post $post)
    {
    	$form = $this->createCommentForm();

    	return $this->render('blog/comment_form.html.twig', array(
    			'post' => $post,
    			'form' => $form->createView(),
    		));
    }

    /**
    * Utility method to create comment forms
    */
    private function createCommentForm()
    {
    	$form = $this->createForm(new CommentType());
    	$form->add('submit', 'submit', array('label' => 'Create'));

    	return $form;
    }
}

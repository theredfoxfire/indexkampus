<?php

namespace IndexBundle\Controller\Admin;

use IndexBundle\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FramewrokExtraBundle\Configuration\Security;
use IndexBundle\Entity\Post;


class BlogController extends Controller
{
	/**
	 * @var object DoctrineEntityManager
	 */
	private $em;
	
	/**
	 * List all post entities
	 * 
	 * @Route("/", name="admin_index")
	 * @Route("/", name="admin_post_index")
	 * @Method("GET")
	 */
	public function indexAction()
	{
		$this->em = $this->getDoctrine()->getManager();
		$posts = $this->em->getRepository('IndexBundle:Post')->findAll();
		
		return $this->render('admin/blog/index.html.twig', array('posts' => $posts));
	}
	
	/**
	 * Create new Post entity
	 * 
	 * @Route("/new", name="admin_post_new")
	 * @Method({"GET", "POST"})
	 */
	public function newAction(Request $request)
	{
		$post = new Post();
		$post->setAuthorEmail($this->getUser()->getEmail());
		$form = $this->createForm(new PostType(), $post);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$post->setSlug($this->get('slugger')->slugify($post->getTitle()));
			
			$this->em->persist($post);
			$this->em->flush();
			
			return $this->redirectToRoute('admin_post_index');
		}
		
		return $this->render('admin/blog/new.html.twig', array(
			'post' => $post,
			'form' => $form->createView(),
		));
	}
	
	/**
	 * Find and display Post entity
	 * 
	 * @Route("/{id}", requirements={"id" = "\d+"}, name="admin_post_show")
	 * @Method("GET")
	 */
	public function showAction(Post $post)
	{
		if (null === $this->getUser() || !$post->isAuthor($this->getUser())) {
			throw $this->createAccessDeniedException('Posts can only be shown to their authors. ');
		}

		$deleteForm = $this->createDeleteForm($post);

		return $this->render('admin/blog/show.html.twig', array(
				'post' => $post,
				'delete_form' => $deleteForm->createView(),
			));
	}

	/**
	* Display a form to edit an existing Post entity.
	*
	* @Route("/{id}/edit", requirements={"id" = "\d+"}, name="admin_post_edit")
	* @Mthod({"GET", "POST"})
	*/
	public function editAction(Post $post, Request $request)
	{
		if (null === $this->getUser() || !$post->isAuthor($this->getUser())) {
			throw $this->createAccessDeniedException('Posts can only be edited by their authors. ');
		}

		$editForm = $this->createForm(new PostType(), $post);
		$deleteForm = $this->createDeleteForm($post);

		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && editForm->isValid()) {
			$post->setSlug($this->get('slugger')->slugify($post->getTitle()));
			$this->em->flush();

			return $this->redirectToRoute('admin_post_edit', array('id' => $post->getId()));
		}

		return $this->render('admin/blog/edit.html.twig', array(
				'post' => $post,
				'edit_form' => $editForm->createView(),
				'delete_form' => $deleteForm->createView(),
			));
	}

	/**
	* Deletes a Post entity
	* @Route("/{id}", name="admin_post_delete")
	* @Method("DELETE")
	* @Security("post.isAuthor(user)")
	*
	*isAuthor() method is defined in IndexBundle\Entity\Post entity.
	*/
	public function deleteAction(Request $request, Post $post)
	{
		$form = $this->createDeleteForm($post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->em->remove($post);
			$this->em->flush();
		}

		return $this->redirectToRoute('admin_post_index');
	}

	/**
	* Creates a form to delete a Post entity by id.
	* @param Post $post The post object
	*
	* @return \Symfony\Component\Form\Form the form
	*/
	private function createDeleteForm(Post $post)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('admin_post_delete', array('id' => $post->getId())))
			->setMethod('DELETE')
			->getForm()
			;
	}
}

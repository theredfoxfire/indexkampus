<?php

namespace IndexBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
	/**
	* @param FormBuilderInterface $builder
	* @param array $options
	*/
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title', null, array('label' => 'label.title'))
			->add('summary', 'textarea', array('label' => 'label.summary'))
			->add('content', 'textarea', array(
				'label' => 'label.content',
				'attr' => array('rows' => 20),
			))
			->add('authorEmail', 'email', array('label' => 'label.author_email'))
			->add('publishedAt', 'datetime', array(
				'widget' => 'single_text',
				'label' => 'label.published_at',
			))
			;
	}

	/**
	* @param OptionsResolver $resolver
	*/
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'IndexBundle\Entity\Post',
		));
	}

	/**
	* @return string
	*/
	public function getName()
	{
		return 'app_post';
	}
}
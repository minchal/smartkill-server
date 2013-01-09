<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\MinLength;

class ContactType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text', array('label'=>'Nick:', 'constraints'=>new NotBlank()))
			->add('email', 'email', array('label'=>'E-mail:', 'constraints'=>new Email()))
			->add('subject', 'text', array(
						'label'=>'Temat:',
						'constraints'=>array(
		            		new NotBlank(),
		            		new MaxLength(150)
					)))
			->add('msg', 'textarea', array(
						'label'=>'Treść:',
						'constraints'=>array(
		            		new NotBlank(),
		            		new MinLength(20)
					)))
		;
	}

	public function getName() {
		return 'contact';
	}
}
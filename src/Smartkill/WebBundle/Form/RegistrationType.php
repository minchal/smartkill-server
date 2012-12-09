<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('username', 'text', array('label'=>'Login:'))
			->add('email', 'email', array('label'=>'E-mail:'))
			->add('password', 'repeated', array(
				'invalid_message' => 'Podane hasła muszą być takie same.', // TODO: przenieść do tłumaczeń
				'first_name' => 'password',
				'second_name' => 'confirm',
				'type' => 'password',
				'first_options' => array('label' => 'Hasło:'),
				'second_options' => array('label' => 'Powtórz hasło:'),
				'constraints'=> new NotBlank(),
			))
		;
	}

	public function getName() {
		return 'registration';
	}
}

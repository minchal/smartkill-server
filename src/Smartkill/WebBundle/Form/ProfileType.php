<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('email', 'email', array('label'=>'E-mail:'))
			->add('oldPassword', 'password', array('required'=>false, 'label'=>'Obecne hasło:'))
			->add('password', 'repeated', array(
				'required'=>false,
				'invalid_message' => 'Podane hasła muszą być takie same.', // TODO: przenieść do tłumaczeń
				'first_name' => 'password',
				'second_name' => 'confirm',
				'type' => 'password',
				'first_options' => array('label' => 'Hasło:'),
				'second_options' => array('label' => 'Powtórz hasło:')
			))
		;
	}

	public function getName() {
		return 'profile';
	}
}

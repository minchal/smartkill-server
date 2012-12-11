<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('username', 'text', array('label'=>'Login:'))
			->add('email', 'email', array('label'=>'E-mail:'))
			->add('admin', 'checkbox', array('label'=>'Uprawnienia administratora','required'=>false))
			->add('password', 'repeated', array(
				'required'=> false,
				'invalid_message' => 'Podane hasła muszą być takie same.',
				'first_name' => 'password',
				'second_name' => 'confirm',
				'type' => 'password',
				'first_options' => array('label' => 'Hasło:'),
				'second_options' => array('label' => 'Powtórz hasło:'),
			))
		;
	}

	public function getName() {
		return 'user';
	}
}

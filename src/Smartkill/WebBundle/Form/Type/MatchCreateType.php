<?php

namespace Smartkill\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MatchCreateType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', 'text', array('label'=>'Nazwa:'));
		$builder->add('dueDate', 'datetime', array('label'=>'Termin gry:', 'widget' => 'single_text'));
		$builder->add('length', 'integer', array('label'=>'Czas trwania:'));
		$builder->add('size', 'integer', array('label'=>'Promień terytorium:'));
		$builder->add('maxPlayers', 'integer', array('label'=>'Maks. liczba graczy:'));
		$builder->add('password', 'password', array('label'=>'Hasło:'));
	}

	public function getName() {
		return 'matchCreate';
	}
}

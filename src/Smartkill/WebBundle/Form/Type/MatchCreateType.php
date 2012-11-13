<?php

namespace Smartkill\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MatchCreateType extends AbstractType {
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', 'text', array('label'=>'Nazwa:'));
		$builder->add('password', 'password', array('label'=>'Hasło:'));
		$builder->add('lat', 'number', array('label'=>'Szerokość geo.:'));
		$builder->add('lng', 'number', array('label'=>'Długość geo.:'));
		$builder->add('size', 'number', array('label'=>'Rozmiar:'));
		$builder->add('length', 'integer', array('label'=>'Czas trwania:'));
		$builder->add('dueDate', 'datetime', array('label'=>'Termin gry:'));
		$builder->add('maxPlayers', 'integer', array('label'=>'Maks. liczba graczy:'));
	}

	public function getName() {
		return 'matchCreate';
	}
}

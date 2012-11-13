<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label'=>'Nazwa:'))
            ->add('password', 'password', array('label'=>'Hasło:'))
            ->add('lat', 'number', array('label'=>'Szerokość geo.:'))
            ->add('lng', 'number', array('label'=>'Długość geo.:'))
            ->add('size', 'integer', array('label'=>'Promień terytorium:'))
            ->add('length', 'integer', array('label'=>'Czas trwania:'))
            ->add('dueDate', 'datetime', array('label'=>'Termin gry:'))
            ->add('maxPlayers', 'integer', array('label'=>'Maks. liczba graczy:'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Smartkill\WebBundle\Entity\Match'
        ));
    }

    public function getName()
    {
        return 'smartkill_webbundle_matchtype';
    }
}

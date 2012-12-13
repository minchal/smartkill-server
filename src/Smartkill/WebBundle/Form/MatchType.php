<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\DateTime;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
            	'label'=>'Nazwa:'
            ))
            ->add('descr', 'textarea', array(
            	'label'=>'Dodatkowy opis:',
            	'required'=>false
            ))
            ->add('dueDate', 'datetime', array(
            	'label'=>'Termin gry:', 
            	'widget'=>'single_text', 
            	'format'=>'yyyy-MM-dd\'T\'HH:mm', 
            	'constraints' => new DateTimeRangeConstraint(array('min'=>new \DateTime()))
            ))
            ->add('length', 'choice', array(
            	'label'=>'Czas trwania:', 
            	'choices' => array('30'=>'30 minut','60'=>'60 minut','90'=>'90 minut','120'=>'2 godz.','180'=>'3 godz.','300'=>'5 godz.','720'=>'12 godz.','1440'=>'Cały dzień')
            ))
            ->add('size', 'choice', array(
            	'label'=>'Promień terytorium:', 
            	'choices' => array(500=>'0,5km',1000=>'1km',2000=>'2km',5000=>'5km')
            ))
            ->add('maxPlayers', 'integer', array(
            	'label'=>'Maks. liczba graczy:'
            ))
            ->add('password', 'password', array(
            	'label'=>'Hasło:',
            	'required'=>false
            ))
            
            ->add('density', 'choice', array(
            	'label'=>'Ilość paczek:', 
            	'choices' => array(0=>'Bez paczek',5=>'Mało (5/km²)',10=>'Średnio (10/km²)',20=>'Dużo (20/km²)',50=>'OMG! One są wszędzie! (50/km²)')
            ))
            ->add('pkgTime',   'checkbox', array('required'=>false, 'label'=>'Czasówki'))
            ->add('pkgShield', 'checkbox', array('required'=>false, 'label'=>'Tarcze'))
            ->add('pkgSnipe',  'checkbox', array('required'=>false, 'label'=>'Lunety'))
            ->add('pkgSwitch', 'checkbox', array('required'=>false, 'label'=>'Zmiana ról'))
            
            ->add('lat', 'hidden')
            ->add('lng', 'hidden')
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
        return 'match';
    }
}

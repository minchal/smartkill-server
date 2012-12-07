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
            ->add('dueDate', 'datetime', array('label'=>'Termin gry:', 'widget'=>'single_text'))
            ->add('length', 'choice', array('label'=>'Czas trwania:', 'choices' => array('30'=>'30 min.','60'=>'1 godz.','120'=>'2 godz.','180'=>'3 godz.')))
            ->add('size', 'choice', array('label'=>'Promień terytorium:', 'choices' => array('0.5'=>'0,5km','1'=>'1km','2'=>'2km','5'=>'5km')))
            ->add('maxPlayers', 'integer', array('label'=>'Maks. liczba graczy:'))
            ->add('password', 'password', array('label'=>'Hasło:', 'required'=>false))
            ->add('lat', 'hidden', array('data'=>51.11))
            ->add('lng', 'hidden', array('data'=>17.06))
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

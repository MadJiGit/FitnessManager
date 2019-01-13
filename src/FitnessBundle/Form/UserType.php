<?php

namespace FitnessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('username', TextType::class)
	        ->add('password', PasswordType::class)
	        ->add('firstName', TextType::class)
	        ->add('lastName', TextType::class)
	        ->add('email', EmailType::class)
	        ->add('phone', TextType::class)
	        ->add('gender', TextType::class)
	        ->add('role', TextType::class)
	        ->add('sport', TextType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FitnessBundle\Entity\User'
        ));
    }

}

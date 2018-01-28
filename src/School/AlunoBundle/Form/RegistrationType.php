<?php
namespace School\AlunoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('telefone')
            ->add('dataNascimento', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'placeholder' => 'Select a value',
                'attr' =>[ 'data-provide'=> "datepicker","data-date-format"=>"yyyy-mm-dd" ]
                ])
            ->add('cpf')
            ->add('rg');
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

}
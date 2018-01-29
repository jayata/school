<?php

namespace School\MatriculaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MatriculaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('curso')
            ->add('ano', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'placeholder' => 'Select a value',
                'attr' => ['data-provide' => "datepicker", "data-date-format" => "yyyy-mm-dd"]
            ])
            ->add('ativa');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'School\MatriculaBundle\Entity\Matricula',
//            'admin' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'school_matriculabundle_matricula';
    }


}

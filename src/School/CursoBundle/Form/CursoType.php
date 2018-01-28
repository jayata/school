<?php

namespace School\CursoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CursoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nome')
            ->add('mensualidade')
            ->add('descripcao', TextareaType::class, array(
                'attr' => array('class' => 'tinymce'),
            ))
            ->add('valorMatricula')
            ->add('periodo', ChoiceType::class, array(
            'choices'  => array(
                'matutino' => 'matutino',
                'vespertino' => 'vespertino',
                'noturno' => 'noturno',
            ),))
            ->add('mesesDuracao');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'School\CursoBundle\Entity\Curso'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'school_cursobundle_curso';
    }


}

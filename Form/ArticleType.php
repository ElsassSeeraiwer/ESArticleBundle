<?php

namespace ElsassSeeraiwer\ESArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('key', null, array(
                'label'                  => 'form.new.key',
                'translation_domain'    => 'ESArticle',
            ))*/
            //->add('createDate')
            //->add('modifyDate')
            ->add('title', 'text', array(
                'attr'                  => array('class' => 'span12'),
                'label'                 => 'form.new.title',
                'translation_domain'    => 'ESArticle',
                'required'              => false
            ))
            ->add('save', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ElsassSeeraiwer\ESArticleBundle\Entity\Article'
        ));
    }

    public function getName()
    {
        return 'articleform';
    }
}

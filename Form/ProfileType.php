<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Form;

use Pd\UserBundle\Model\ProfileInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Languages;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

/**
 * User Profile Type.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add Email
        $builder->add('email', EmailType::class, [
                'label' => 'security.email',
            ]
        );

        // Add Profile Section
        $builder->add(
            $builder
                ->create('profile', FormType::class, [
                    'label' => false,
                    'attr' => ['class' => 'col-12'],
                    'data_class' => ProfileInterface::class,
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'firstname',
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'lastname',
                ])
                ->add('phone', TextType::class, [
                    'label' => 'phone',
                    'required' => false,
                    'constraints' => [
                        new Type(['type' => 'numeric']),
                    ],
                ])
                ->add('website', TextType::class, [
                    'label' => 'website',
                    'required' => false,
                ])
                ->add('company', TextType::class, [
                    'label' => 'company',
                    'required' => false,
                ])
                ->add('language', ChoiceType::class, [
                    'label' => 'language',
                    'choices' => $this->getLanguageList($options['parameter_bag']),
                    'choice_translation_domain' => false,
                ])
        );

        // Add Submit
        $builder->add('submit', SubmitType::class, [
            'label' => 'save',
        ]);
    }

    /**
     * Set Default Options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('parameter_bag');
    }

    /**
     * Return Active Language List.
     *
     * @return array|bool
     */
    public function getLanguageList(ParameterBagInterface $bag)
    {
        return array_flip(array_intersect_key(Languages::getNames(), array_flip($bag->get('active_language'))));
    }
}

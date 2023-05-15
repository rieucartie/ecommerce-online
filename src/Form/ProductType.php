<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tva;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('description')
            ->add('content')

            ->add('promo',CheckboxType::class,['label_attr' => array(
                'class' => 'checkbox-inline'
               ),
                'required'=>false
                  ])

            ->add('imageFile',VichImageType::class, [
               'required' => false,
               'download_uri' => false,
               'image_uri' => false,
               'allow_delete' => false,
               'download_link'     => false,
               'asset_helper' => false,
                'download_label' => static function (Product $product) {
                    return $product->getFileName();
              },

            ])
            ->add("stock", IntegerType::class, [
                'constraints' => array(
                    new NotBlank(),
                    new Regex(array(
                            'pattern' => '/^[0-9]\d*$/',
                            'message' => 'Seul les nombres positifs sont acceptés.'
                        )
                    ),
                )
            ])
           /* ->add("slug")*/

            ->add('categories', EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
            ->add('tva', EntityType::class,[
                'required'=>true,
                'label'=>"Tva",
                'class'=> Tva::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

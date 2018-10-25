<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type;

use Symfony\Component\Form\AbstractType;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\RichTextDataTransformer;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\RichText as ValidRichTextInput;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use eZ\Publish\Core\FieldType\RichText\Converter;
use eZ\Publish\Core\FieldType\RichText\DOMDocumentFactory;
use eZ\Publish\Core\FieldType\RichText\InputHandler;

class RichTextType extends AbstractType
{
    /** @var \eZ\Publish\Core\FieldType\RichText\DOMDocumentFactory */
    private $domDocumentFactory;

    /** @var \eZ\Publish\Core\FieldType\RichText\InputHandler */
    private $inputHandler;

    /** @var \eZ\Publish\Core\FieldType\RichText\Converter */
    private $docbook2xhtml5editConverter;

    /**
     * @param \eZ\Publish\Core\FieldType\RichText\DOMDocumentFactory $domDocumentFactory
     * @param \eZ\Publish\Core\FieldType\RichText\InputHandler $inputHandler
     * @param \eZ\Publish\Core\FieldType\RichText\Converter $docbook2xhtml5editConverter
     */
    public function __construct(DOMDocumentFactory $domDocumentFactory, InputHandler $inputHandler, Converter $docbook2xhtml5editConverter)
    {
        $this->domDocumentFactory = $domDocumentFactory;
        $this->inputHandler = $inputHandler;
        $this->docbook2xhtml5editConverter = $docbook2xhtml5editConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new RichTextDataTransformer(
            $this->domDocumentFactory,
            $this->inputHandler,
            $this->docbook2xhtml5editConverter
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars += [
            'language' => $options['language'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Allow to create a embeded content in all languages
            'language' => null,
            'constraints' => [
                new ValidRichTextInput(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'richtext';
    }
}

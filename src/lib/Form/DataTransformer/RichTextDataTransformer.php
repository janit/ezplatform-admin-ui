<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\FieldType\RichText\Converter;
use eZ\Publish\Core\FieldType\RichText\DOMDocumentFactory;
use eZ\Publish\Core\FieldType\RichText\InputHandlerInterface;
use eZ\Publish\Core\FieldType\RichText\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RichTextDataTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\Core\FieldType\RichText\DOMDocumentFactory */
    private $domDocumentFactory;

    /** @var \eZ\Publish\Core\FieldType\RichText\InputHandlerInterface */
    private $inputHandler;

    /** @var \eZ\Publish\Core\FieldType\RichText\Converter */
    private $docbook2xhtml5editConverter;

    /**
     * @param \eZ\Publish\Core\FieldType\RichText\DOMDocumentFactory $domDocumentFactory
     * @param \eZ\Publish\Core\FieldType\RichText\InputHandlerInterface $inputHandler
     * @param \eZ\Publish\Core\FieldType\RichText\Converter $docbook2xhtml5editConverter
     */
    public function __construct(DOMDocumentFactory $domDocumentFactory, InputHandlerInterface $inputHandler, Converter $docbook2xhtml5editConverter)
    {
        $this->domDocumentFactory = $domDocumentFactory;
        $this->inputHandler = $inputHandler;
        $this->docbook2xhtml5editConverter = $docbook2xhtml5editConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value): string
    {
        if (!$value) {
            $value = Value::EMPTY_VALUE;
        }

        try {
            return $this->docbook2xhtml5editConverter->convert(
                $this->domDocumentFactory->loadXMLString((string) $value)
            )->saveXML();
        } catch (NotFoundException | InvalidArgumentException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): string
    {
        try {
            return $this->inputHandler->fromString($value)->saveXML();
        } catch (NotFoundException | InvalidArgumentException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

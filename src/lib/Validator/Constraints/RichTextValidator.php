<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use DOMDocument;
use eZ\Publish\Core\FieldType\RichText\Exception\InvalidXmlException;
use eZ\Publish\Core\FieldType\RichText\InputHandlerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RichTextValidator extends ConstraintValidator
{
    /** @var \eZ\Publish\Core\FieldType\RichText\InputHandlerInterface */
    private $richTextInputHandler;

    /**
     * @param \eZ\Publish\Core\FieldType\RichText\InputHandlerInterface $richTextInputHandler
     */
    public function __construct(InputHandlerInterface $richTextInputHandler)
    {
        $this->richTextInputHandler = $richTextInputHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (is_string($value)) {
            try {
                $value = $this->richTextInputHandler->fromString($value);
            } catch (InvalidXmlException $e) {
                foreach ($e->getErrors() as $error) {
                    $this->context->addViolation($error->message);
                }
            }
        }

        if (!($value instanceof DOMDocument)) {
            return;
        }

        foreach ($this->richTextInputHandler->validate($value) as $error) {
            $this->context->addViolation($error);
        }
    }
}

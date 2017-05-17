<?php

namespace Spiral\Pages\Requests\Checkers;

use Spiral\ORM\Entities\RecordSource;
use Spiral\ORM\RecordEntity;
use Spiral\Validation\Prototypes\AbstractChecker;

class EntityChecker extends AbstractChecker
{
    /**
     * Field name to pass an entity inside.
     */
    const ENTITY_FIELD = 'entity';

    /**
     * Default error messages associated with checker method by name.
     * {@inheritdoc}
     */
    const MESSAGES = [
        'isUnique' => '[[Must be unique value.]]',
    ];

    /**
     * @param string $value
     * @param string $sourceClass
     * @param string $field
     * @return bool
     */
    public function isUnique(string $value, string $sourceClass, string $field)
    {
        /** @var RecordEntity $entity */
        $entity = $this->getValidator()->getValue(static::ENTITY_FIELD);
        if (!empty($entity) && $entity[$field] === $value) {
            //Entity is passed and its value hasn't changed.
            return true;
        }

        /** @var RecordSource $source */
        $source = $this->container->get($sourceClass);
        $another = $source->findOne([$field => $value]);

        //Another entity in database with same field value will cause error.
        return empty($another);
    }
}
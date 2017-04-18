<?php

namespace Spiral\Tests\Pages;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Requests\Checkers\EntityChecker;
use Spiral\Pages\Requests\PageRequest;
use Spiral\Tests\BaseTest;
use Spiral\Validation\ValidatorInterface;

class RequestTest extends BaseTest
{
    public function testFilter()
    {
        $this->assertEquals('slug', PageRequest::trimSlug('Slug'));
        $this->assertEquals('slug', PageRequest::trimSlug('/slug/'));
    }

    public function testEntityChecker()
    {
        /** @var Page $page */
        $page = $this->orm->source(Page::class)->create();
        $page->slug = 'slug';

        $rules = [
            'slug' => [
                [
                    EntityChecker::class . '::isUnique',
                    PageSource::class,
                    'slug'
                ]
            ]
        ];

        //first instance, not in db yet
        $this->assertValid(['slug' => $page->slug], $rules);

        $page->save();

        //first instance, in db already, if entity is passed - should be ok
        $this->assertValid(['slug' => $page->slug, EntityChecker::ENTITY_FIELD => $page], $rules);
        $this->assertFail('slug', ['slug' => $page->slug], $rules);

        /** @var Page $page1 */
        $page1 = $this->orm->source(Page::class)->create();
        $page1->slug = 'slug';

        $this->assertFail('slug', ['slug' => $page1->slug], $rules);

        $page1->slug = 'slug1';
        //first instance, not in db yet
        $this->assertValid(['slug' => $page1->slug], $rules);
    }

    protected function assertValid(array $data, array $rules)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->container->make(ValidatorInterface::class, ['rules' => $rules]);
        $validator->setData($data);

        $this->assertTrue($validator->isValid(), 'Validation FAILED');
    }

    protected function assertFail(string $error, array $data, array $rules)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->container->make(ValidatorInterface::class, ['rules' => $rules]);
        $validator->setData($data);

        $this->assertFalse($validator->isValid(), 'Validation PASSED');
        $this->assertArrayHasKey($error, $validator->getErrors());
    }
}
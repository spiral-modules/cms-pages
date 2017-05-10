<?php

namespace Spiral\Tests\Pages\Requests;

use Spiral\Pages\Database\Page;
use Spiral\Pages\Database\Sources\PageSource;
use Spiral\Pages\Database\Types\PageStatus;
use Spiral\Pages\Requests\Api\MetaRequest;
use Spiral\Pages\Requests\Api\SourceRequest;
use Spiral\Pages\Requests\Checkers\EntityChecker;
use Spiral\Pages\Requests\PageRequest;
use Spiral\Tests\BaseTest;
use Spiral\Validation\ValidatorInterface;

class RequestsTest extends BaseTest
{
    public function testFilter()
    {
        $this->assertEquals('slug', PageRequest::slugSetter('Slug'));
        $this->assertEquals('slug', PageRequest::slugSetter('/slug/'));
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

    public function testPageRequest()
    {
        $request = new PageRequest();
        $this->assertFalse($request->isValid());

        $request->setField('title', 'some title');
        $request->setField('slug', 'some-slug');
        $request->setField('status', 'some status');
        $request->setField('source', 'some source');
        $this->assertFalse($request->isValid());

        $request->setField('status', PageStatus::ACTIVE);
        $this->assertTrue($request->isValid());
    }

    public function testMetaRequest()
    {
        $request = new MetaRequest();
        $this->assertFalse($request->isValid());

        $request->setField('title', 'some title');
        $this->assertTrue($request->isValid());
    }

    public function testSourceRequest()
    {
        $request = new SourceRequest();
        $this->assertFalse($request->isValid());

        $request->setField('source', 'some source');
        $this->assertTrue($request->isValid());
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
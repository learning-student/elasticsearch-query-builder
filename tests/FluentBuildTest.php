<?php


use Sheriff\Elastic\FluentBuilder;

class FluentBuildTest extends \PHPUnit\Framework\TestCase
{
    private $builder;

    protected function setUp(): void
    {
        $this->builder = new \Sheriff\Elastic\FluentBuilder();
    }

    public function testQuery()
    {
        $this->assertInstanceOf(\Sheriff\Elastic\FluentBuilder::class, $this->builder->setQuery([]));
        $this->assertIsArray($this->builder->getQuery());
    }

    public function testQueryBuild()
    {
        $builder = $this->builder->query()
            ->bool();

        $this->assertEquals([
            'query' => [
                'bool' => []
            ]
        ], $builder->build());
    }

    public function testMustBuild()
    {
        $builder = $this->builder->query()
            ->bool()
            ->must([

                    FluentBuilder::terms([
                        'type' => 'test'
                    ]),

                    FluentBuilder::match([
                        'source' => 'test'
                    ])

            ]);


        $this->assertEquals([
            'query' => [
                'bool' => [
                   'must' => [
                       ['terms' => ['type' => 'test']],
                       ['match' => ['source' => 'test']]
                   ]

                ]
            ]
        ], $builder->build());
    }
}
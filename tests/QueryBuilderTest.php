<?php


class QueryBuilderTest extends \PHPUnit\Framework\TestCase
{

    private $builder;

    protected function setUp(): void
    {
        $this->builder = new \Sheriff\Elastic\QueryBuilder();
    }

    public function testBuilderMatch()
    {
       $builder =  $this->builder->match(
            $this->builder->message([
                'query' => 'test'
            ])
        );

        $output = $builder->build();

        $this->assertIsArray($output);
        $this->assertEquals([
            'match' => [ 'message' => ['query' => 'test']]
        ] , $output);
    }

    public function testBuilderMatchAll()
    {
        $builder =  $this->builder->matchAll(
            $this->builder->message([
                'query' => 'test'
            ])
        );

        $output = $builder->build();

        $this->assertIsArray($output);
        $this->assertEquals([
            'match_all' => [ 'message' => ['query' => 'test']]
        ] , $output);
    }



    public function testQuery()
    {
        $this->assertInstanceOf(\Sheriff\Elastic\QueryBuilder::class, $this->builder->setQuery([]));
        $this->assertIsArray($this->builder->getQuery());
    }

    public function testColumn(){
        $builder =  $this->builder->query(
            $this->builder->column('test', 'deneme')
        );

        $this->assertEquals([
            'query' => [
                'test' => 'deneme'
            ]
        ], $builder->build());
    }

    public function testToCamelCase()
    {
        $this->assertEquals("FirstName", $this->builder->toCamelCase('first_name', true));
    }

    public function testMixed()
    {
        $builder = $this->builder;
        $output = $builder->bool(
            $builder->must([
                    $builder->match($builder->companyId(1)),
                    $builder->bool(
                        $builder->should(
                            [
                                $builder->matchPhrasePrefix($builder->name("test")),
                                $builder->matchPhrasePrefix($builder->taxNumber("11"))
                            ]
                        )
                    )
                ]
            )


        )->build();


        $this->assertIsArray($output);
        $this->assertEquals([
            'bool' => [
                'must' => [
                    ['match' => ['company_id' => 1]],
                    [
                        'bool' => [
                            'should' => [
                                ['match_phrase_prefix' => ['name' => "test"]],
                                ['match_phrase_prefix' => ['tax_number' => "11"]],

                            ]
                        ]
                    ]
                ]
            ]
        ], $output);
    }
    
    public function testBuilderBool()
    {
        $builder =  $this->builder->bool(
            $this->builder->must([
               $this->builder->match(
                   $this->builder->companyId($this->builder->query('test'))
               )
            ])
        );

        $output = $builder->build();


        $this->assertIsArray($output);
        $this->assertEquals( [
            'bool' => [
                'must' => [
                    [
                        'match' => [
                            'company_id' => [
                                'query' => 'test'
                            ]
                        ]
                    ]
                ]
            ]
        ] , $output);
    }
}
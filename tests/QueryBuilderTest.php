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
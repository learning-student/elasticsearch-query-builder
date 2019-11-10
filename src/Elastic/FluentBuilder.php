<?php


namespace Sheriff\Elastic;


class FluentBuilder
{

    /**
     * @var array<Query>
     */
    private $query = [];

    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * FluentBuilder constructor.
     */
    public function __construct()
    {
        $this->builder = new QueryBuilder();
    }


    /**
     * @param array $content
     * @return array
     */
    private function mapArrayContent(array $content)
    {
        $mapped = [];
        $builder = $this->builder;

        foreach ($content as $key => $value) {

            if ($value instanceof FluentBuilder) {
                $mapped[] = $value->build(false);
            } else {
                $method = $builder->toCamelCase($key);
                $mapped[] = $builder->{$method}($value);
            }
        }


        return $mapped;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    private function buildMultiple($arguments)
    {
        $arguments = $this->mapArrayContent($arguments);


        return [(new Multiple())->setQueries($arguments)];
    }


    private function mapMultipleArguments(array $arguments)
    {
        $mapped = [];

        foreach ($arguments as $argument) {
            if ($argument instanceof FluentBuilder) {
                $queries = $argument->getQuery();

                if (count($queries)) {
                    $mapped[] = $argument->build(false);

                }

            }
        }

        return $mapped;
    }

    private function buildOne(int $index)
    {
        $query = $this->query[$index];
        $builder = clone $this->builder;


        $hasNext = isset($this->query[$index + 1]);

        $arguments = $query->getArguments();


        if (count($arguments) === 1 && is_array($arguments[0])) {

            $isAssoc = array_keys($arguments[0]) !== range(0, count($arguments[0]) - 1);

            $content = $isAssoc ? $this->mapArrayContent($arguments[0]) :
                [$this->mapArrayContent($arguments[0])];
        } else {
            $content = $this->mapMultipleArguments($arguments);
        }


        $buildNext = $hasNext ? [$this->buildOne($index + 1)] : [];


        $sent = count($content) ? array_merge($content, $buildNext) : $buildNext;

        return $builder->{$query->getCommand()}(
            ...$sent
        );
    }

    /**
     * @param bool $toArray
     * @return array|QueryBuilder
     */
    public function build(bool $toArray = true)
    {
        $queries = $this->query;

        if (count($queries)) {
            $builded = $this->buildOne(0);

            if ($builded instanceof QueryBuilder) {
                return $toArray ? $builded->build() : $builded;

            }

        }


    }

    /**
     * @return array<Query>
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     * @return FluentBuilder
     */
    public function setQuery(array $query): FluentBuilder
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function getBuilder(): QueryBuilder
    {
        return $this->builder;
    }

    /**
     * @param QueryBuilder $builder
     * @return FluentBuilder
     */
    public function setBuilder(QueryBuilder $builder): FluentBuilder
    {
        $this->builder = $builder;
        return $this;
    }


    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments)
    {


        $query = (new Query())
            ->setArguments($arguments)
            ->setCommand($name);

        $this->query[] = $query;

        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static())->$name(...$arguments);
    }


}


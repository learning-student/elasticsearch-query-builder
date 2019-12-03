<?php


namespace Sheriff\Elastic;

/**
 * Sheriff\Elastic\QueryBuilder
 *
 *
 * @method QueryBuilder bool(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder match(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder must(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder should(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder mustNot(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder matchAll(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder range(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder term(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder terms(array|QueryBuilder $value, $_ = null)
 * @method QueryBuilder gt(mixed $value)
 * @method QueryBuilder gte(mixed $value)
 * @method QueryBuilder lt(mixed $value)
 * @method QueryBuilder lte(mixed $value)
 * @method QueryBuilder boots(int|float $value)
 */
class QueryBuilder
{

    /**
     * @var array
     */
    protected $query = [];


    /**
     * Translates a camel case string into a string with
     * underscores (e.g. firstName -> first_name)
     *
     * @param string $str String in camel case format
     * @return string $str Translated into underscore format
     */
    public function fromCamelCase($str)
    {
        $str[0] = strtolower($str[0]);
        return preg_replace_callback('/([A-Z])/', function ($c) {
            return "_" . strtolower($c[1]);
        }, $str);
    }

    /**
     * @param string $name
     * @param int|float|string $value
     * @return QueryBuilder
     */
    public function column(string $name, $value)
    {
        return $this->{$this->toCamelCase($name)}([
            $name
        ]);
    }

    /**
     * @param string $name
     * @param $content
     * @return QueryBuilder
     */
    public function addToQuery(string $name, $content): QueryBuilder
    {
        $this->query[$name] = $content;

        return $this;
    }


    private function createArrayArguments(array $arguments): QueryBuilder
    {
        $instance = new static();

        foreach ($arguments as $key => $argument) {
            // if argument's null, skip it
            if (!$argument) {
                continue;
            }


            $instance->addToQuery($key, $argument);
        }

        return $instance;
    }


    private function createArrayArgumentsAndMerge(array $arguments): QueryBuilder
    {
        $instance = new static();


        foreach ($arguments as $key => $argument) {


            // if argument's null, skip it
            if (!$argument) {
                continue;
            }


            if ($argument instanceof QueryBuilder) {

                $instance->setQuery(
                    array_merge(
                        $instance->getQuery(),
                        $argument->getQuery()
                    )
                );

            } else {
                $instance->addToQuery($key, $argument);
            }
        }

        return $instance;
    }


    public function __call($name, $arguments)
    {
        $snakeCase = $this->fromCamelCase($name);

        /**
         *  if we have only one argument
         */
        if (count($arguments) === 1) {
            $argument = $arguments[0];
            if (is_array($argument)) {
                return (new static)->addToQuery($snakeCase, $this->createArrayArguments($argument));
            }

            return (new static)->addToQuery($snakeCase, $arguments[0]);
        }


        // if multiple arguments exists

        return $this->addToQuery($snakeCase, $this->createArrayArgumentsAndMerge($arguments));

    }


    /**
     * @return array
     */
    public function build(): array
    {
        $data = [];


        foreach ($this->query as $item => $value) {
            if ($value instanceof QueryBuilder) {
                $value = $value->build();
            }

            $data[$item] = $value;
        }

        return $data;
    }

    /**
     * Translates a string with underscores
     * into camel case (e.g. first_name -> firstName)
     *
     * @param string $str String in underscore format
     * @param bool $capitalise_first_char If true, capitalise the first char in $str
     * @return string $str translated into camel caps
     */
    function toCamelCase($str, $capitalise_first_char = false)
    {
        if ($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        return preg_replace_callback('/_([a-z])/', function ($c) {
            return strtoupper($c[1]);
        }, $str);
    }


    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     */
    public function setQuery(array $query)
    {
        $this->query = $query;
    }

}

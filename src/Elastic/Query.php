<?php


namespace Sheriff\Elastic;


class Query
{

    /**
     * @var  string
     */
    private $command;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     * @return Query
     */
    public function setCommand(string $command): Query
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     * @return Query
     */
    public function setArguments(array $arguments): Query
    {
        $this->arguments = $arguments;
        return $this;
    }



}
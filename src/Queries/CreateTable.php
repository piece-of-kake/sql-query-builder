<?php

namespace PoK\SQLQueryBuilder\Queries;

use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\FieldBuilder;
use PoK\ValueObject\Collection;

class CreateTable implements CanCompile
{
    private $tableName;
    private $fieldBuilders;
    private $engine;
    private $charset;
    private $collation;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->fieldBuilders = new Collection([]);
    }

    public function field(string $fieldName)
    {
        $fieldBuilder = new FieldBuilder($fieldName);
        $this->fieldBuilders[] = $fieldBuilder;
        return $fieldBuilder;
    }

    public function engine(string $engine = 'InnoDB')
    {
        $this->engine = $engine;
        return $this;
    }

    public function charset(string $charset)
    {
        $this->charset = $charset;
        return $this;
    }

    public function collation(string $collation)
    {
        $this->collation = $collation;
        return $this;
    }

    public function fields($callback)
    {
        $callback($this);
        return $this;
    }

    public function compile()
    {
        $fields = $this->fieldBuilders
            ->map(function ($builder) {
                return $builder->compile();
            })
            ->implode(',');

        $primaryKeys = $this->fieldBuilders
            ->filter(function ($builder) {
                return $builder->isPrimary();
            })
            ->map(function ($builder) {
                return '`' . $builder->getName() . '`';
            })
            ->implode(',');

        $uniqueKeys = $this->fieldBuilders
            ->filter(function ($builder) {
                return $builder->isUnique();
            })
            ->map(function ($builder) {
                return sprintf(
                    ', UNIQUE KEY `%s_%s_unique` (`%s`)',
                    $this->tableName,
                    $builder->getName(),
                    $builder->getName()
                );
            })
            ->implode('');

        return sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (
                %s
                %s
                %s
            ) %s %s %s;',
            $this->tableName,
            $fields,
            $primaryKeys ? ", PRIMARY KEY ($primaryKeys)" : '',
            $uniqueKeys ? $uniqueKeys : '',
            $this->engine ? "ENGINE=$this->engine" : '',
            $this->charset ? "DEFAULT CHARSET=$this->charset" : '',
            $this->collation ? "COLLATE=$this->collation" : ''
        );
    }
}
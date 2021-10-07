<?php

namespace PoK\SQLQueryBuilder\Table\Alter;

use PoK\SQLQueryBuilder\Exceptions\Builder\RemoveOrUpdateDefaultException;
use PoK\SQLQueryBuilder\Exceptions\Builder\UpdateDefaultOrChangeDefinitionException;
use PoK\SQLQueryBuilder\Exceptions\Builder\UpdateDefaultOrChangePositionException;
use PoK\SQLQueryBuilder\Interfaces\CanCompile;
use PoK\SQLQueryBuilder\Table\ColumnBuilder;

class UpdateColumn implements CanCompile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ColumnBuilder
     */
    private $definition;

    /**
     * @var bool
     */
    private $first = false;

    /**
     * @var string
     */
    private $after;

    /**
     * @var string
     */
    private $newName;

    /**
     * @var mixed
     */
    private $default;

    /**
     * @var bool
     */
    private $changedDefault = false; // Used to indicate if the default value has changed, since default value can be null.

    /**
     * @var bool
     */
    private $removeDefault = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function definition(): ColumnBuilder
    {
        $this->definition = new ColumnBuilder($this->newName ? $this->newName : $this->name);
        $this->checkCollisions();
        return $this->definition;
    }

    public function first()
    {
        $this->first = true;
        $this->checkCollisions();
        return $this;
    }

    public function after(string $columnName)
    {
        $this->after = $columnName;
        $this->checkCollisions();
        return $this;
    }

    /**
     * It is necessary to provide the full column definition after this beacause MySQL logic behind requires it.
     * RENAME COLUMN option is available after 8.0 so it is not built in here.
     *
     * @param string $name
     * @return $this
     */
    public function rename(string $name)
    {
        $this->newName = $name;
        return $this;
    }

    /**
     * Use this if you only need to change the default value. Otherwise use some of the qualified field functionalities
     * (like $this->string(), $this->integer() etc..) and define the default value that way.
     *
     * @param $default
     */
    public function default($default)
    {
        $this->default = $default;
        $this->changedDefault = true;
        $this->checkCollisions();
    }

    /**
     * Use this if you only need to remove the default value. Otherwise use some of the qualified field functionalities
     * (like $this->string(), $this->integer() etc..) without defining the default value and it will be removed.
     */
    public function removeDefault()
    {
        $this->removeDefault = true;
        $this->checkCollisions();
    }

    public function compile()
    {
        $this->checkCollisions();

        $positionSuffix = '';
        if ($this->first && !$this->after) $positionSuffix = ' FIRST';
        if (!$this->first && $this->after) $positionSuffix = sprintf(' AFTER `%s`', $this->after);

        if ($this->removeDefault) return sprintf('ALTER COLUMN `%s` DROP DEFAULT', $this->name);
        else if ($this->changedDefault) return sprintf('ALTER COLUMN `%s` SET DEFAULT %s', $this->name, $this->default);
        else if ($this->newName) return sprintf('CHANGE COLUMN `%s` %s%s', $this->name, $this->definition->compile(), $positionSuffix);
        else return sprintf('MODIFY COLUMN %s%s', $this->definition->compile(), $positionSuffix);
    }

    private function checkCollisions()
    {
        // Cannot remove and update the default value in the same query.
        if ($this->removeDefault && $this->changedDefault) throw new RemoveOrUpdateDefaultException();
        // Cannot change the default value and update definition in the same query. Set default through definition instead.
        if (($this->removeDefault || $this->changedDefault) && ($this->definition instanceof ColumnBuilder)) throw new UpdateDefaultOrChangeDefinitionException();
        // Cannot change default and column position in the same query.
        if (($this->removeDefault || $this->changedDefault) && ($this->first || $this->after)) throw new UpdateDefaultOrChangePositionException();
    }
}

//ALTER TABLE `admins` ADD COLUMN `test_decimal` decimal(5,2) NULL DEFAULT '3.5' AFTER `id`;
//ALTER TABLE `admins` CHANGE COLUMN `test_decimal` `test_new` VARCHAR(10) NOT NULL DEFAULT 'bla' FIRST;
//ALTER TABLE `admins` CHANGE COLUMN `test_new` `test_new` VARCHAR(12) NULL DEFAULT 'something';
//ALTER TABLE `admins` MODIFY COLUMN `test_new` VARCHAR(14) NOT NULL;

//ALTER TABLE `admins`
//CHANGE COLUMN `test_new` `test_another` VARCHAR(10) NOT NULL DEFAULT 'bla' FIRST,
//ALTER COLUMN `test_new` SET DEFAULT 'something',
//MODIFY COLUMN `test_new` VARCHAR(14) NOT NULL;
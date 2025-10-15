<?php



/**
 * Class Query
 */
class Query
{
    /**
     * @var array|null
     */
    private $from = null;

    /**
     * @var array|null
     */
    private $select = null;

    /**
     * @var array
     */
    private $where = [];
    private $order_by = '';

    /**
     * @param string $table
     *
     * @return $this
     */
    public function from(string $table): self
    {
        $this->from = $table;

        return $this;
    }

    /**
     * Example:
     * ```php
     * Input: ['field1', 'field2']; Output: SELECT field1, field2
     * Input: ['f1' => 'field1', 'f2' => 'field2']; Output: SELECT field1 AS f1, field2 AS f2
     * ```
     *
     * @param array $fields
     *
     * @return $this
     */
    public function select(array $fields): self
    {
        $this->select = $fields;

        return $this;
    }

    /**
     * @param array $condition
     *
     * @return $this
     */
    public function where(array $condition): self
    {
        $this->where[] = $condition;

        return $this;
    }
    /**
     * @param string $condition
     *
     * @return $this
     */
    public function order_by(string $condition): self
    {
        $this->order_by = ' Order By '. $condition;

        return $this;
    }
    /**
     * @return array|null
     */
    public function one()
    {
        $result = $this->getResult();
        if (empty($result)) {
            return null;
        }

        return reset($result);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->getResult();
    }

    /**
     * @return mixed
     */
    public function scalar()
    {
        global $database;
        $sql = $this->buildQuery();
        $database->setQuery($sql);
        /** @var mixed $result */
        $result = $database->loadResult();

        return $result;
    }

    /**
     * @return array
     */
    private function getResult(): array
    {
        global $database;
        $sql = $this->buildQuery();
        $database->setQuery($sql);

        return $database->loadAssocList() ?? [];
    }

    /**
     * @return string
     */
    private function buildQuery(): string
    {
        $select = $this->buildSelectSql();
        $where = '';
        if ($this->where) {
            $where = $this->buildWhere();
        }
        return "$select FROM $this->from $where $this->order_by;";
    }

    /**
     * @return string
     */
    private function buildSelectSql(): string
    {
        $sql = 'SELECT ';

        if (empty($this->select)) {
            $sql .= '*';

            return $sql;
        }
        $result = [];
        foreach ($this->select as $alias => $field) {
            if (is_string($alias)) {
                $result[] = "$field AS $alias";
            } else {
                $result[] = $field;
            }
        }

        return $sql . implode(', ', $result);
    }

    /**
     * @return string
     */
    private function buildWhere(): string
    {
        $sql = 'WHERE ';
        $where = [];
        foreach ($this->where as $condition) {
            if (isset($condition[0])) { // operator
                list($operator, $field, $value) = $condition;
                $value = $this->getPrepareValue($value);
                $where[] = "$field $operator $value";
            } else {
                foreach ($condition as $field => $value) {
                    $value = $this->getPrepareValue($value);
                    $where[] = "$field = $value";
                }
            }
        }

        return $sql . implode(' AND ', $where);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function getPrepareValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        global $database;

        return "'" . $database->getEscaped($value) . "'";
    }
}

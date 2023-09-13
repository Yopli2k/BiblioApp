<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace BiblioApp\Core\DataBase;

/**
 * Structure that defines a WHERE condition to filter the model data
 *
 * @author Carlos García Gómez  <carlos@facturascripts.com>
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
final class DataBaseWhere
{

    /**
     * Link with the active database.
     *
     * @var DataBase
     */
    private DataBase $dataBase;

    /**
     * Field list to apply the filters to, separated by '|'.
     *
     * @var string
     */
    private string $fields;

    /**
     * Logic operator that will be applied to the condition.
     *
     * @var string
     */
    private string $operation;

    /**
     * Arithmetic operator that is being used.
     *
     * @var string
     */
    private string $operator;

    /**
     * Filter value.
     *
     * @var mixed
     */
    private mixed $value;

    /**
     * DataBaseWhere constructor.
     *
     * @param string $fields
     * @param mixed $value
     * @param string $operator
     * @param string $operation
     */
    public function __construct(string $fields, mixed $value, string $operator = '=', string $operation = 'AND')
    {
        $this->dataBase = new DataBase();
        $this->fields = $fields;
        $this->operation = $operation;
        $this->operator = $operator;
        $this->value = $value;

        if (null === $value) {
            $this->operator = ($operator === '!=') ? 'IS NOT' : 'IS';
        }
    }

    /**
     * Returns an array with the field (key) and the operation (value).
     * Given a list of fields with operators:
     *    - '|' for OR operations
     *    - ',' for AND operations
     *
     * @param string $fields
     * @return array
     */
    public static function applyOperation(string $fields): array
    {
        if (empty($fields)) {
            return [];
        }

        $result = [];
        foreach (explode(',', $fields) as $field) {
            if ($field !== '' &&  str_contains($field, '|')) {
                $result[$field] = 'AND';
            }
        }
        foreach (explode('|', $fields) as $field) {
            if ($field !== '' && str_contains($field, ',') === false) {
                $result[$field] = 'OR';
            }
        }

        return $result;
    }

    /**
     * Given a DataBaseWhere array, returns the field list with their values
     * that will be applied as a filter.
     * Only returns filters with the '=' operator.
     *
     * @param array $whereItems
     * @return array
     */
    public static function getFieldsFilter(array $whereItems): array
    {
        $result = [];
        foreach ($whereItems as $item) {
            if ($item->operator !== '=') {
                continue;
            }

            $fields = explode('|', $item->fields);
            foreach ($fields as $field) {
                $result[$field] = $item->value;
            }
        }

        return $result;
    }

    /**
     * Returns a string to apply to the WHERE clause.
     *
     * @param bool $applyOperation
     * @param string $prefix
     * @return string
     */
    public function getSQLWhereItem(bool $applyOperation = false, string $prefix = ''): string
    {
        $fields = explode('|', $this->fields);
        $result = $this->applyValueToFields($this->value, $fields);
        if ($result === '') {
            return '';
        }

        if (count($fields) > 1) {
            $result = '(' . $result . ')';
        }

        $result = $prefix . $result;
        if ($applyOperation) {
            $result = ' ' . $this->operation . ' ' . $result;
        }

        return $result;
    }

    /**
     * Given a DataBaseWhere array, it returns the full WHERE clause.
     *
     * @param DataBaseWhere[] $whereItems
     * @return string
     */
    public static function getSQLWhere(array $whereItems): string
    {
        $result = '';
        $join = false;
        $group = false;

        $keys = array_keys($whereItems);
        foreach ($keys as $num => $key) {
            $next = $keys[$num + 1] ?? null;

            // Calculate the logical grouping
            $prefix = is_null($next) ? '' : self::getGroupPrefix($whereItems[$next], $group);

            // Calculate the sql clause for the condition
            $result .= $whereItems[$key]->getSQLWhereItem($join, $prefix);
            $join = true;

            // Closes the logical condition of grouping if it exists
            if (null !== $next && $group && $whereItems[$next]->operation != 'OR') {
                $result .= ')';
                $group = false;
            }
        }

        if ($result === '') {
            return '';
        }

        // Closes the logical condition of grouping
        if ($group) {
            $result .= ')';
        }

        return ' WHERE ' . $result;
    }

    /**
     * Apply one value to a field list.
     *
     * @param mixed $value
     * @param array $fields
     * @return string
     */
    private function applyValueToFields(mixed $value, array $fields): string
    {
        $result = '';
        foreach ($fields as $field) {
            $union = empty($result) ? '' : ' OR ';
            switch ($this->operator) {
                case 'LIKE':
                    $result .= $union . 'LOWER(' . $this->escapeColumn($field) . ') '
                        . $this->dataBase->getOperator($this->operator) . ' ' . $this->getValueFromOperatorLike($value);
                    break;

                case 'XLIKE':
                    $result .= $union . '(';
                    $union2 = '';
                    foreach (explode(' ', $value) as $query) {
                        $result .= $union2 . 'LOWER(' . $this->escapeColumn($field) . ') '
                            . $this->dataBase->getOperator('LIKE') . ' ' . $this->getValueFromOperatorLike($query);
                        $union2 = ' AND ';
                    }
                    $result .= ')';
                    break;

                default:
                    $result .= $union . $this->escapeColumn($field) . ' '
                        . $this->dataBase->getOperator($this->operator) . ' ' . $this->getValue($value);
                    break;
            }
        }

        return $result;
    }

    /**
     *
     * @param string $column
     * @return string
     */
    private function escapeColumn(string $column): string
    {
        foreach (['.', 'CAST('] as $char) {
            if (str_contains($column, $char)) {
                return $column;
            }
        }

        return $this->dataBase->escapeColumn($column);
    }

    /**
     * Calculate if you need grouping of conditions.
     * It is necessary for logical conditions of type 'OR'
     *
     * @param DataBaseWhere $item
     * @param bool $group
     * @return string
     */
    private static function getGroupPrefix(DataBaseWhere $item, bool &$group): string
    {
        if ($item->operation == 'OR' && $group === false) {
            $group = true;
            return '(';
        }

        return '';
    }

    /**
     * Return list values for IN operator.
     *
     * @param string $values
     * @return string
     */
    private function getValueFromOperatorIn(string $values): string
    {
        if (0 === stripos($values, 'select ')) {
            return $values;
        }

        $result = '';
        $comma = '';
        foreach (explode(',', $values) as $value) {
            $result .= $comma . $this->dataBase->var2str($value);
            $comma = ',';
        }
        return $result;
    }

    /**
     * Return value for LIKE operator.
     *
     * @param mixed $value
     * @return string
     */
    private function getValueFromOperatorLike(mixed $value): string
    {
        if (is_null($value) || is_bool($value)) {
            return $this->dataBase->var2str($value);
        }

        if (str_contains($value, '%') === false) {
            return "LOWER('%" . $this->dataBase->escapeString($value) . "%')";
        }

        return "LOWER('" . $this->dataBase->escapeString($value) . "')";
    }

    /**
     * Returns the value for the operator.
     *
     * @param string $value
     * @return string
     */
    private function getValueFromOperator(string $value): string
    {
        return match ($this->operator) {
            'IN', 'NOT IN' => '(' . $this->getValueFromOperatorIn($value) . ')',
            'LIKE', 'XLIKE' => $this->getValueFromOperatorLike($value),
            default => $this->dataBase->var2str($value),
        };
    }

    /**
     * Returns the filter value formatted according to the type.
     *
     * @param mixed $value
     * @return string
     */
    private function getValue(mixed $value): string
    {
        if (in_array($this->operator, ['IN', 'LIKE', 'NOT IN', 'XLIKE'])) {
            return $this->getValueFromOperator($value);
        }

        if ($value !== null && str_starts_with($value, 'field:')) {
            return $this->dataBase->escapeColumn(substr($value, 6));
        }

        return $this->dataBase->var2str($value);
    }
}

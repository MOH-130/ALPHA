<?php

namespace App\Core;

class QueryBuilder {
    protected string $modelClass;
    protected string $table;
    protected string $primaryKey = 'id';
    
    protected array $wheres = [];
    protected array $whereParams = [];
    protected array $orderBys = [];
    protected ?int $limitValue = null;
    protected ?int $offsetValue = null;
    protected array $joins = [];
    protected string $selectCols = '*';

    public function __construct(string $modelClass) {
        $this->modelClass = $modelClass;
        $instance = new $modelClass();
        $this->table = $instance->getTable();
        $this->primaryKey = $instance->getKeyName();
    }

    public function select(string $columns): self {
        $this->selectCols = $columns;
        return $this;
    }

    public function where(string $column, $operator = null, $value = null): self {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if ($value === null) {
            if ($operator === '=' || $operator === 'IS' || $operator === null) {
                $this->wheres[] = "`{$column}` IS NULL";
            } else {
                $this->wheres[] = "`{$column}` IS NOT NULL";
            }
            return $this;
        }

        $this->wheres[] = "`{$column}` {$operator} ?";
        $this->whereParams[] = $value;
        return $this;
    }

    public function orWhere(string $column, $operator = null, $value = null): self {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if ($value === null) {
            $clause = ($operator === '=' || $operator === 'IS' || $operator === null) ? "`{$column}` IS NULL" : "`{$column}` IS NOT NULL";
        } else {
            $clause = "`{$column}` {$operator} ?";
            $this->whereParams[] = $value;
        }

        if (empty($this->wheres)) {
            $this->wheres[] = $clause;
        } else {
            $last = array_pop($this->wheres);
            $this->wheres[] = "({$last} OR {$clause})";
        }
        return $this;
    }

    public function whereRaw(string $sql, array $params = []): self {
        $this->wheres[] = $sql;
        $this->whereParams = array_merge($this->whereParams, $params);
        return $this;
    }

    public function whereIn(string $column, array $values): self {
        if (empty($values)) {
            $this->wheres[] = "1=0";
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "`{$column}` IN ({$placeholders})";
        $this->whereParams = array_merge($this->whereParams, array_values($values));
        return $this;
    }

    public function whereNotIn(string $column, array $values): self {
        if (empty($values)) {
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "`{$column}` NOT IN ({$placeholders})";
        $this->whereParams = array_merge($this->whereParams, array_values($values));
        return $this;
    }

    public function whereNull(string $column): self {
        $this->wheres[] = "`{$column}` IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): self {
        $this->wheres[] = "`{$column}` IS NOT NULL";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBys[] = "`{$column}` " . strtoupper($direction);
        return $this;
    }

    public function orderByDesc(string $column): self {
        return $this->orderBy($column, 'DESC');
    }

    public function limit(int $limit): self {
        $this->limitValue = $limit;
        return $this;
    }

    public function offset(int $offset): self {
        $this->offsetValue = $offset;
        return $this;
    }

    public function take(int $limit): self {
        return $this->limit($limit);
    }

    public function skip(int $offset): self {
        return $this->offset($offset);
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self {
        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function buildQuery(): array {
        $sql = "SELECT {$this->selectCols} FROM " . $this->table;
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        if (!empty($this->orderBys)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBys);
        }
        if ($this->limitValue !== null) {
            $sql .= " LIMIT " . $this->limitValue;
        }
        if ($this->offsetValue !== null) {
            $sql .= " OFFSET " . $this->offsetValue;
        }
        return [$sql, $this->whereParams];
    }

    public function get(): array {
        [$sql, $params] = $this->buildQuery();
        $rows = Database::select($sql, $params);
        $models = [];
        $class = $this->modelClass;
        foreach ($rows as $row) {
            $m = new $class();
            $m->fill($row);
            $models[] = $m;
        }
        return $models;
    }

    public function first(): ?Model {
        $this->limit(1);
        $list = $this->get();
        return $list[0] ?? null;
    }

    public function find($id): ?Model {
        return $this->where($this->primaryKey, '=', $id)->first();
    }

    public function count(): int {
        $sql = "SELECT COUNT(*) as aggregate FROM " . $this->table;
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $result = Database::selectOne($sql, $this->whereParams);
        return (int) ($result['aggregate'] ?? 0);
    }

    public function sum(string $column): float {
        $sql = "SELECT SUM(`{$column}`) as aggregate FROM " . $this->table;
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $result = Database::selectOne($sql, $this->whereParams);
        return (float) ($result['aggregate'] ?? 0);
    }

    public function avg(string $column): float {
        $sql = "SELECT AVG(`{$column}`) as aggregate FROM " . $this->table;
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $result = Database::selectOne($sql, $this->whereParams);
        return (float) ($result['aggregate'] ?? 0);
    }

    public function max(string $column) {
        $sql = "SELECT MAX(`{$column}`) as aggregate FROM " . $this->table;
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $result = Database::selectOne($sql, $this->whereParams);
        return $result['aggregate'] ?? null;
    }

    public function min(string $column) {
        $sql = "SELECT MIN(`{$column}`) as aggregate FROM " . $this->table;
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $result = Database::selectOne($sql, $this->whereParams);
        return $result['aggregate'] ?? null;
    }

    public function exists(): bool {
        return $this->count() > 0;
    }

    public function paginate(int $perPage = 15, ?int $page = null): array {
        $page = $page ?: (int) ($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $total = $this->count();
        $offset = ($page - 1) * $perPage;

        $this->limit($perPage)->offset($offset);
        $items = $this->get();
        $lastPage = (int) max(1, ceil($total / $perPage));

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'has_more' => $page < $lastPage
        ];
    }

    public function delete(): bool {
        $sql = "DELETE FROM " . $this->table;
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        return Database::statement($sql, $this->whereParams);
    }

    public function update(array $attributes): bool {
        $sets = [];
        $values = [];
        foreach ($attributes as $k => $v) {
            $sets[] = "`{$k}` = ?";
            $values[] = $v;
        }
        $sql = "UPDATE " . $this->table . " SET " . implode(', ', $sets);
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        $params = array_merge($values, $this->whereParams);
        return Database::statement($sql, $params);
    }
}

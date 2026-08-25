<?php

namespace App\Core;

abstract class Model {
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];
    protected array $original = [];
    protected bool $timestamps = true;

    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }

    public function fill(array $attributes): self {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        $this->original = $this->attributes;
        return $this;
    }

    public function __get(string $key) {
        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
        $accessor = 'get' . $studly . 'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->$accessor();
        }
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, $value) {
        $this->attributes[$key] = $value;
    }

    public function __isset(string $key): bool {
        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
        $accessor = 'get' . $studly . 'Attribute';
        if (method_exists($this, $accessor)) {
            return true;
        }
        return isset($this->attributes[$key]);
    }

    public function toArray(): array {
        return $this->attributes;
    }

    public function getTable(): string {
        if (!empty($this->table)) {
            return $this->table;
        }
        $class = (new \ReflectionClass($this))->getShortName();
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class)) . 's';
    }

    public function getKeyName(): string {
        return $this->primaryKey;
    }

    public function getKey() {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    public static function query(): QueryBuilder {
        return new QueryBuilder(static::class);
    }

    public static function all(): array {
        return static::query()->get();
    }

    public static function find($id): ?static {
        return static::query()->find($id);
    }

    public static function findOrFail($id): static {
        $item = static::find($id);
        if (!$item) {
            throw new \Exception("Model [" . static::class . "] not found for ID: " . $id);
        }
        return $item;
    }

    public static function create(array $attributes): static {
        $model = new static();
        if ($model->timestamps) {
            $now = date('Y-m-d H:i:s');
            $attributes['created_at'] = $attributes['created_at'] ?? $now;
            $attributes['updated_at'] = $attributes['updated_at'] ?? $now;
        }
        
        $fields = [];
        $placeholders = [];
        $values = [];
        foreach ($attributes as $k => $v) {
            if (empty($model->fillable) || in_array($k, $model->fillable) || in_array($k, ['created_at', 'updated_at'])) {
                $fields[] = "`{$k}`";
                $placeholders[] = "?";
                $values[] = $v;
            }
        }
        
        $sql = "INSERT INTO " . $model->getTable() . " (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $id = Database::insert($sql, $values);
        $attributes[$model->primaryKey] = $id;
        $model->fill($attributes);
        return $model;
    }

    public function update(array $attributes): bool {
        if ($this->timestamps) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }
        $sets = [];
        $values = [];
        foreach ($attributes as $k => $v) {
            $sets[] = "`{$k}` = ?";
            $values[] = $v;
            $this->attributes[$k] = $v;
        }
        $values[] = $this->attributes[$this->primaryKey] ?? null;
        $sql = "UPDATE " . $this->getTable() . " SET " . implode(', ', $sets) . " WHERE `{$this->primaryKey}` = ?";
        return Database::statement($sql, $values);
    }

    public function save(): bool {
        if (isset($this->attributes[$this->primaryKey]) && !empty($this->attributes[$this->primaryKey])) {
            return $this->update($this->attributes);
        }
        $created = static::create($this->attributes);
        $this->attributes = $created->toArray();
        return true;
    }

    public function delete(): bool {
        if (!isset($this->attributes[$this->primaryKey])) return false;
        $sql = "DELETE FROM " . $this->getTable() . " WHERE `{$this->primaryKey}` = ?";
        return Database::statement($sql, [$this->attributes[$this->primaryKey]]);
    }

    public function __call(string $method, array $parameters) {
        return static::query()->$method(...$parameters);
    }

    public static function __callStatic(string $method, array $parameters) {
        return static::query()->$method(...$parameters);
    }
}

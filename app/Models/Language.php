<?php

namespace App\Models;

use App\Core\Model;

class Language extends Model {
    protected string $table = 'languages';
    protected array $fillable = ['code', 'name', 'direction', 'flag', 'is_default', 'status'];
}

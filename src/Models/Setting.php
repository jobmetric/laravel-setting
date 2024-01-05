<?php

namespace JobMetric\Setting\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * JobMetric\Setting\Models\Setting
 *
 * @property mixed id
 * @property mixed code
 * @property mixed key
 * @property mixed value
 * @property mixed is_json
 * @property mixed created_at
 * @property mixed updated_at
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'key',
        'value',
        'is_json'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getTable()
    {
        return config('setting.tables.setting', parent::getTable());
    }

    /**
     * Scope a query to only include settings of a given code.
     *
     * @param Builder $query
     * @param string $code
     *
     * @return Builder
     */
    public function scopeOfCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }
}

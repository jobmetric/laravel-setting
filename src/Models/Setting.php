<?php

namespace JobMetric\Setting\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @package JobMetric\Setting
 *
 * @property int $id
 * @property string $form
 * @property string $key
 * @property mixed $value
 * @property bool $is_json
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static ofForm(string $form)
 * @method static create(array $array)
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'form',
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
        'form' => 'string',
        'key' => 'string',
        'value' => 'array|string|null',
        'is_json' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getTable()
    {
        return config('setting.tables.setting', parent::getTable());
    }

    /**
     * Scope a query to only include settings of a given form.
     *
     * @param Builder $query
     * @param string $form
     *
     * @return Builder
     */
    public function scopeOfForm(Builder $query, string $form): Builder
    {
        return $query->where('form', $form);
    }
}

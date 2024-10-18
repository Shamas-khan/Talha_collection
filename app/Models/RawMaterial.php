<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;
    protected $primaryKey = 'raw_material_id';
    protected $table = 'raw_material';
    protected $fillable = ['unit_id', 'name'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function rawStocks()
    {
        return $this->hasMany(RawStock::class);
    }
}

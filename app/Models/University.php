<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function specializations()
    {
        return $this->belongsToMany(Specialization::class);
    }
    public function theses()
    {
        return $this->hasMany(Thesis::class);
    }
}

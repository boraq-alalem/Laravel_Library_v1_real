<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisTitlesSimple extends Model
{
    use HasFactory;
    protected $table = 'thesis_titles_simple';
    protected $fillable = [
        'title', 'person_name', 'university'
    ];
}

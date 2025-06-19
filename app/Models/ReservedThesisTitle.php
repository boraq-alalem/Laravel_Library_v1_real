<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservedThesisTitle extends Model
{
    use HasFactory;
    protected $table = 'reserved_thesis_titles';
    protected $fillable = [
        'title', 'person_name', 'university', 'specialization', 'degree', 'date'
    ];
}

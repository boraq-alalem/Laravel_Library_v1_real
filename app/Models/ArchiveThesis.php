<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveThesis extends Model
{
    use HasFactory;
    protected $table = 'archive_theses';
    protected $fillable = [
        'id', 'title', 'year', 'pdf_path', 'university_id', 'specialization_id', 'degree_id', 'author_id'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }
    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}

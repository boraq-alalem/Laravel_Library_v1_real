<?php
namespace App\Models;

use App\Traits\OptimizedQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thesis extends Model
{
    use HasFactory, OptimizedQueries;
    protected $fillable = [
        'id', 'title', 'year', 'pdf_path', 'university_id', 'specialization_id', 'degree_id', 'author_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\ThesisCreated::class,
        'updated' => \App\Events\ThesisUpdated::class,
        'deleted' => \App\Events\ThesisDeleted::class,
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

<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFileNote extends Model
{
    protected $table = 'project_file_notes';
    public $timestamps = false;

    protected $fillable = [
        'project_file_id',
        'notes',
    ];

    public function projectFile(): BelongsTo
    {
        return $this->belongsTo(ProjectFile::class, 'project_file_id');
    }
}

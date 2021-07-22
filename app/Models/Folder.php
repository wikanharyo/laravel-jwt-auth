<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $incrementing = false;
    public $timestamps = false;

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}

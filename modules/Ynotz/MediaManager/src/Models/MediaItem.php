<?php

namespace Ynotz\MediaManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ulid',
        'filename',
        'filepath',
        'disk',
        'type',
        'size',
        'mime_type',
    ];

    public function getUrlproperty(Type $args): void
    {
        $url = Storage::disk($this->disk)->url($this->filepath);
    }
}

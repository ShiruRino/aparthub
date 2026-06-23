<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ServiceRequestAttachment extends Model
{
    protected $fillable = [
        'service_request_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    protected $appends = [
        'url',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}

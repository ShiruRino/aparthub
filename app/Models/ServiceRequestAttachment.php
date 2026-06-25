<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ServiceRequestAttachment extends Model
{
    public const TYPE_RESIDENT_SUPPORTING = 'resident_supporting';

    public const TYPE_TECHNICIAN_BEFORE = 'technician_before';

    public const TYPE_TECHNICIAN_AFTER = 'technician_after';

    protected $fillable = [
        'service_request_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'file_size',
        'attachment_type',
        'uploaded_by_user_id',
    ];

    protected $appends = [
        'url',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }
}

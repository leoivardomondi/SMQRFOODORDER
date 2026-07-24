<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class NotificationSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = "settings";

    public function getFileAttribute(): string
    {
        return $this->getFirstMedia('notification-file')?->file_name ?? '';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('notification-file')->useDisk('private')->singleFile();
    }
}

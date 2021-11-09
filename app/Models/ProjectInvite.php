<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectInvite extends Model
{
    use HasFactory;

    // The available identity providers.
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_PENDING = 'pending';
    public const STATUS_REJECTED = 'rejected';

    protected $guarded = [];
    protected $table = 'project_invite';

    public static function getStatuses()
    {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_PENDING,
            self::STATUS_REJECTED,
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    use HasFactory;

    protected $table = 'user_documents';

    // Document Type Constants
    const TYPE_AADHAAR        = 1;
    const TYPE_PAN            = 2;
    const TYPE_DRIVING_LICENCE = 3;
    const TYPE_PASSPORT       = 4;
    const TYPE_VOTER_ID       = 5;
    const TYPE_OTHER          = 6;

    protected $fillable = [
        'company_id',
        'user_id',
        'document_type',
        'document_number',
        'document_front_path',
        'document_back_path',
        'is_verified',
    ];

    /**
     * Helper method to get human-readable document name
     */
    public static function getDocumentTypes()
    {
        return [
            self::TYPE_AADHAAR         => 'Aadhaar Card',
            self::TYPE_PAN             => 'PAN Card',
            self::TYPE_DRIVING_LICENCE => 'Driving Licence',
            self::TYPE_PASSPORT        => 'Passport',
            self::TYPE_VOTER_ID        => 'Voter ID',
            self::TYPE_OTHER           => 'Other Document',
        ];
    }

    public function getDocumentTypeNameAttribute()
    {
        $types = self::getDocumentTypes();
        return $types[$this->document_type] ?? 'Unknown';
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

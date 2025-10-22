<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'requested_by', 'user_id', 'asset_type_id', 'justification', 'status',
        'approved_by', 'approved_at', 'approval_notes', 
        'fulfilled_asset_id', 'fulfilled_at'
    ];

    protected $dates = ['approved_at', 'fulfilled_at'];

    // Relationships
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function fulfilledAsset()
    {
        return $this->belongsTo(Asset::class, 'fulfilled_asset_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFulfilled($query)
    {
        return $query->where('status', 'fulfilled');
    }

    // Methods
    public function approve($approverId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'approval_notes' => $notes
        ]);
    }

    protected static function booted()
    {
        // Keep legacy 'user_id' and 'requested_by' in sync for compatibility with tests
        static::creating(function ($model) {
            if (empty($model->user_id) && !empty($model->requested_by)) {
                $model->user_id = $model->requested_by;
            }
            if (empty($model->requested_by) && !empty($model->user_id)) {
                $model->requested_by = $model->user_id;
            }
        });

        static::saving(function ($model) {
            if (!empty($model->requested_by)) {
                $model->user_id = $model->requested_by;
            } elseif (!empty($model->user_id)) {
                $model->requested_by = $model->user_id;
            }
        });
    }

    public function reject($approverId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'approval_notes' => $notes
        ]);
    }

    public function fulfill($assetId)
    {
        $this->update([
            'status' => 'fulfilled',
            'fulfilled_asset_id' => $assetId,
            'fulfilled_at' => now()
        ]);
    }
}
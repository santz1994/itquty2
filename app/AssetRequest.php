<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    protected $fillable = [
        'requested_by', 'asset_type_id', 'justification', 'status',
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
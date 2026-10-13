<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'type', // 'di_tempat' or 'po'
        'cashier_id',
        'customer_name',
        'customer_phone',
        'notes',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_proof',
        'po_token',
        'pickup_status', // 'none', 'menunggu_pengambilan', 'sudah_diambil'
        'picked_up_at',
        'picked_up_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'picked_up_at' => 'datetime',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function pickedUpByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'picked_up_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}

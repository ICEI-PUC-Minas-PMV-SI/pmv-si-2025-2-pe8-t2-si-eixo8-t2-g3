<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceOrder extends Model
{
    use SoftDeletes;

    protected $table = 'service_orders';

    protected $fillable = [
        'customer_id',
        'customer_name_snapshot',
        'customer_phone_snapshot',
        'customer_email_snapshot',
        'opened_at',
        'closed_at',
        'status',
        'problem_description',
        'problem_category',                 // NEW
        'services_done',
        'parts_list',
        'technician_name',
        'service_type',                     // NEW
        'labor_hours',                      // NEW
        'travel_km',                        // NEW
        'travel_cost_per_km_snapshot',      // NEW
        'technician_hour_cost_snapshot',    // NEW
        'parts_cost',                       // NEW
        'resolved_in_first_visit',          // NEW
        'total_amount',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',

        // Valores monetários e quantitativos
        'total_amount' => 'decimal:2',
        'labor_hours' => 'decimal:2',                   // NEW
        'travel_km' => 'decimal:2',                     // NEW
        'travel_cost_per_km_snapshot' => 'decimal:2',   // NEW
        'technician_hour_cost_snapshot' => 'decimal:2', // NEW
        'parts_cost' => 'decimal:2',                    // NEW

        // Flags
        'resolved_in_first_visit' => 'boolean',         // NEW
    ];

    // (Opcional) incluir campos calculados no JSON
    protected $appends = [
        'labor_cost',
        'travel_cost',
        'total_cost',
        'gross_margin',
        'gross_margin_pct',
        'duration_hours',
    ];

    // Relacionamento
    public function customer()
    {
        return $this->belongsTo(Client::class, 'customer_id');
    }

    // Scopes úteis
    public function scopeOpen($q)      { return $q->whereNull('closed_at'); }
    public function scopeClosed($q)    { return $q->whereNotNull('closed_at'); }
    public function scopeStatus($q,$s) { return $q->where('status', $s); }
    public function scopeServiceType($q, $type) { return $q->where('service_type', $type); }
    public function scopeProblemCategory($q, $cat) { return $q->where('problem_category', $cat); }

    // Acessor: duração em horas (aprox.)
    public function getDurationHoursAttribute(): ?int
    {
        if (!$this->opened_at) return null;
        $end = $this->closed_at ?: now();
        return $this->opened_at->diffInHours($end);
    }

    // ====== Acessores calculados (opcionais) ======

    public function getLaborCostAttribute(): float
    {
        $hours = (float) ($this->labor_hours ?? 0);
        $rate  = (float) ($this->technician_hour_cost_snapshot ?? 0);
        return round($hours * $rate, 2);
    }

    public function getTravelCostAttribute(): float
    {
        $km   = (float) ($this->travel_km ?? 0);
        $cKm  = (float) ($this->travel_cost_per_km_snapshot ?? 0);
        return round($km * $cKm, 2);
    }

    public function getTotalCostAttribute(): float
    {
        $parts  = (float) ($this->parts_cost ?? 0);
        return round($parts + $this->labor_cost + $this->travel_cost, 2);
    }

    public function getGrossMarginAttribute(): float
    {
        $amount = (float) ($this->total_amount ?? 0);
        return round($amount - $this->total_cost, 2);
    }

    public function getGrossMarginPctAttribute(): ?float
    {
        $amount = (float) ($this->total_amount ?? 0);
        if ($amount <= 0) return null;
        return round($this->gross_margin / $amount, 4); // 0.1234 = 12.34%
    }
}

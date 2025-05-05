<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transport extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // Add global scope to filter transports for transporters
        static::addGlobalScope('transporter_access', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Transporter')) {
                $builder->where('transporter_id', auth()->user()->transporter_id);
            }
        });
    }

    protected static function booted()
    {
        static::created(function ($transport) {
            if ($transport->transporter && $transport->transporter->user) {
                $transport->transporter->user->notify(new \App\Notifications\NewTransportAssigned($transport));
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'transporter_id',
        'origin',
        'destination',
        'pickup_date',
        'delivery_date',
        'status',
        'is_acknowledged',
        'acknowledged_at',
        'acknowledged_by',
        'transporter_name',
        'transporter_phone',
        'transporter_email',
        'notes',
        'batch_id',
        'gate_pass_path',
        'qr_code_path',
        'batch_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pickup_date' => 'date',
        'delivery_date' => 'date',
        'is_acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Get the vehicle associated with the transport.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the transporter associated with the transport.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    /**
     * Get the batch associated with the transport.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the user who acknowledged the transport.
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Check if a gate pass has been uploaded
     */
    public function hasGatePass(): bool
    {
        return !empty($this->gate_pass_path);
    }

    /**
     * Get the gate pass URL if available
     */
    public function getGatePassUrl(): ?string
    {
        if ($this->hasGatePass()) {
            return asset('storage/' . $this->gate_pass_path);
        }

        return null;
    }

    /**
     * Get the QR code URL if available
     */
    public function getQrCodeUrl(): ?string
    {
        if (!empty($this->qr_code_path)) {
            return asset('storage/' . $this->qr_code_path);
        }

        return null;
    }

    /**
     * Get all transports for a specific batch
     */
    public static function getByBatchId(string $batchId)
    {
        return self::where('batch_id', $batchId)->with('vehicle')->get();
    }

    /**
     * Get monthly transport counts for a specific year
     * 
     * @param int|null $year The year to get data for (defaults to current year)
     * @param int|null $transporterId Filter by transporter ID (optional)
     * @return array An array of counts indexed by month (0-11 for Jan-Dec)
     */
    public static function getMonthlyTransportCounts(?int $year = null, ?int $transporterId = null): array
    {
        $year = $year ?? now()->year;
        
        // Create base query
        $query = self::whereYear('created_at', $year);
        
        // Apply transporter filter if provided
        if ($transporterId) {
            $query->where('transporter_id', $transporterId);
        }
        
        // Get raw data first to debug
        $rawData = (clone $query)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Initialize array with zeros for all months (0-11 for Jan-Dec)
        $monthlyTransports = array_fill(0, 12, 0);
        
        // Fill in the actual counts, ensure we're handling the index correctly
        foreach ($rawData as $data) {
            // Ensure month is treated as integer and properly normalize to 0-based index
            $monthIndex = (int)$data->month - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $monthlyTransports[$monthIndex] = (int)$data->count;
            }
        }
       
        return $monthlyTransports;
    }
    
    /**
     * Check if any transport data exists for the given year
     * 
     * @param int|null $year The year to check (defaults to current year)
     * @return bool True if data exists, false otherwise
     */
    public static function hasDataForYear(?int $year = null): bool
    {
        $year = $year ?? now()->year;
        return self::whereYear('created_at', $year)->exists();
    }
} 
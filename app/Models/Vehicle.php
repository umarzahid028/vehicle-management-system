<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\PlaceholderImageService;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Vehicle status constants
     */
    const STATUS_AVAILABLE = 'Available';

    const STATUS_TRANSPORT_PENDING = 'Transport Pending';
    const STATUS_TRANSPORT_IN_TRANSIT = 'Transport In Transit';
    const STATUS_TRANSPORT_IN_PROGRESS = 'Transport In Progress';
    const STATUS_TRANSPORT_DELIVERED = 'Transport Delivered';
    const STATUS_TRANSPORT_COMPLETED = 'Transport Completed';
    const STATUS_TRANSPORT_CANCELLED = 'Transport Cancelled';

    const STATUS_INSPECTION_PENDING = 'Inspection Started';
    const STATUS_INSPECTION_IN_PROGRESS = 'Inspection In Progress';
    const STATUS_INSPECTION_COMPLETED = 'Inspection Completed';
    const STATUS_INSPECTION_CANCELLED = 'Inspection Cancelled';

    const STATUS_REPAIR_PENDING = 'Vendor Assigned';
    const STATUS_REPAIR_IN_PROGRESS = 'Repair In Progress';
    const STATUS_REPAIR_COMPLETED = 'Repair Completed';
    const STATUS_REPAIR_CANCELLED = 'Repair Cancelled';
    const STATUS_REPAIRS_COMPLETED = 'Repairs Completed';


    const STATUS_READY_FOR_SALE = 'Ready for Sale';
    const STATUS_READY_FOR_SALE_ASSIGNED = 'Ready for Sale Assigned';
    const STATUS_ASSIGNED_TO_SALES = 'Assigned to Sales';
    const STATUS_SOLD = 'Sold';

    const STATUS_GOODWILL_CLAIMS = 'Goodwill Claims';
    const STATUS_GOODWILL_CLAIMS_ASSIGNED = 'Goodwill Claims Assigned';
    const STATUS_GOODWILL_CLAIMS_COMPLETED = 'Goodwill Claims Completed';

    const STATUS_ARCHIVE = 'Archive';

    /**
     * Status Category Constants
     */
    const CATEGORY_AVAILABLE = 'available';
    const CATEGORY_TRANSPORT = 'transport';
    const CATEGORY_INSPECTION = 'inspection';
    const CATEGORY_REPAIR = 'repair';
    const CATEGORY_SALES = 'sales';
    const CATEGORY_GOODWILL = 'goodwill_claims';
    const CATEGORY_ARCHIVE = 'archive';

    protected static function boot()
    {
        parent::boot();

        // Add global scope to filter vehicles for transporters
        static::addGlobalScope('transporter_access', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Transporter')) {
                $builder->whereHas('transports', function ($query) {
                    $query->where('transporter_id', auth()->user()->transporter_id)
                          ->orWhereHas('batch', function ($q) {
                              $q->where('transporter_id', auth()->user()->transporter_id);
                          });
                });
            }
        });
        
        // Set default status to "Available" for new vehicles
        static::creating(function (Vehicle $vehicle) {
            if (empty($vehicle->status)) {
                $vehicle->status = self::STATUS_AVAILABLE;
            }
        });
    }

    protected $fillable = [
        'stock_number',
        'vin',
        'year',
        'make',
        'model',
        'trim',
        'date_in_stock',
        'odometer',
        'exterior_color',
        'interior_color',
        'number_of_leads',
        'status',
        'body_type',
        'drive_train',
        'engine',
        'fuel_type',
        'is_featured',
        'has_video',
        'number_of_pics',
        'image_path',
        'has_placeholder_image',
        'purchased_from',
        'purchase_date',
        'transmission',
        'transmission_type',
        'vehicle_purchase_source',
        'advertising_price',
        'deal_status',
        'sold_date',
        'buyer_name',
        'import_file',
        'processed_at',
        'transport_status',
        'sales_team_id',
        'assigned_for_sale_by',
        'assigned_for_sale_at',
    ];

    protected $casts = [
        'date_in_stock' => 'date',
        'purchase_date' => 'date',
        'sold_date' => 'date',
        'is_featured' => 'boolean',
        'has_video' => 'boolean',
        'has_placeholder_image' => 'boolean',
        'year' => 'integer',
        'odometer' => 'integer',
        'number_of_leads' => 'integer',
        'number_of_pics' => 'integer',
        'advertising_price' => 'decimal:2',
        'processed_at' => 'datetime',
        'assigned_for_sale_at' => 'datetime',
    ];

    /**
     * Get the transports for the vehicle.
     */
    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class);
    }

    /**
     * Get the gate passes for the vehicle.
     */
    public function gatePasses(): HasMany
    {
        return $this->hasMany(GatePass::class);
    }

    /**
     * Get the inspections for the vehicle.
     */
    public function vehicleInspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }

    /**
     * Get the images for the vehicle.
     */
    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    /**
     * Get the featured images for the vehicle.
     */
    public function featuredImages()
    {
        return $this->images()->where('is_featured', true)->orderBy('sort_order');
    }

    /**
     * Scope for vehicles that are ready for sale
     */
    public function scopeReadyForSale($query)
    {
        return $query->where('status', self::STATUS_READY_FOR_SALE);
    }

    /**
     * Check if the vehicle is ready for sale
     */
    public function isReadyForSale(): bool
    {
        return $this->status === self::STATUS_READY_FOR_SALE;
    }

    /**
     * Mark the vehicle as ready for sale
     */
    public function markAsReadyForSale(): bool
    {
        return $this->update(['status' => self::STATUS_READY_FOR_SALE]);
    }

    /**
     * Get the vehicle's image URL
     */
    public function getImageUrlAttribute(): string
    {
        // First try the direct image_path field
        if ($this->image_path) {
            // If it's already a full URL, return it directly
            if (strpos($this->image_path, 'http') === 0) {
                return $this->image_path;
            } 
            // If it's a local path, check if it exists in storage and return URL
            elseif (Storage::disk('public')->exists($this->image_path)) {
                return asset(Storage::url($this->image_path));
            }
        }
        
        // If no main image, check for a featured image in the gallery
        $featuredImage = $this->images()->where('is_featured', true)->first();
        if ($featuredImage) {
            return $featuredImage->image_url;
        }
        
        // If no featured image, get the first image in the gallery
        $firstImage = $this->images()->orderBy('sort_order')->first();
        if ($firstImage) {
            return $firstImage->image_url;
        }
        
        // Return a modern car placeholder with background, styling
        return "data:image/svg+xml;base64," . base64_encode('
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600">
            <!-- Background Rectangle -->
            <rect width="800" height="600" fill="#F3F4F6" rx="8" ry="8"/>
            
            <!-- Company Name or Logo Placeholder -->
            <text x="400" y="100" font-family="Arial, sans-serif" font-size="36" text-anchor="middle" fill="#6B7280">Trevinos Auto</text>
            
            <!-- Car Icon -->
            <g transform="translate(250, 150) scale(0.6)">
                <path d="M135.2 117.4L109.1 192H402.9l-26.1-74.6C372.3 104.6 360.2 96 346.6 96H165.4c-13.6 0-25.7 8.6-30.2 21.4zM39.6 196.8L74.8 96.3C88.3 57.8 124.6 32 165.4 32H346.6c40.8 0 77.1 25.8 90.6 64.3l35.2 100.5c23.2 9.6 39.6 32.5 39.6 59.2V400v48c0 17.7-14.3 32-32 32H448c-17.7 0-32-14.3-32-32V400H96v48c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V400 256c0-26.7 16.4-49.6 39.6-59.2zM128 288a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64z" fill="#9CA3AF"/>
            </g>
            
            <!-- Text Label -->
            <text x="400" y="380" font-family="Arial, sans-serif" font-size="24" text-anchor="middle" fill="#6B7280">' . 
            ($this->stock_number ? 'Stock # ' . $this->stock_number : 'No Image Available') . 
            '</text>
            
            <!-- Additional Info -->
            <text x="400" y="420" font-family="Arial, sans-serif" font-size="20" text-anchor="middle" fill="#9CA3AF">' . 
            ($this->year && $this->make && $this->model ? $this->year . ' ' . $this->make . ' ' . $this->model : '') . 
            '</text>
        </svg>
        ');
    }
    
    /**
     * Check if the vehicle has a main image
     */
    public function getHasMainImageAttribute(): bool
    {
        // Check if image_path is set and valid
        if ($this->image_path) {
            // If it's a URL, assume it's valid
            if (strpos($this->image_path, 'http') === 0) {
                return true;
            }
            // If it's a local path, check if it exists
            elseif (Storage::disk('public')->exists($this->image_path)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get all the image URLs for this vehicle
     */
    public function getImageUrlsAttribute(): array
    {
        $urls = [];
        
        // Add main image if exists
        if ($this->image_url) {
            $urls[] = $this->image_url;
        }
        
        // Add all additional images
        foreach ($this->images()->orderBy('sort_order')->get() as $image) {
            if ($image->image_url) {
                $urls[] = $image->image_url;
            }
        }
        
        return $urls;
    }

    /**
     * Get the sales team member assigned to this vehicle.
     */
    public function salesTeam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_team_id');
    }

    /**
     * Get the user who assigned this vehicle to sales team.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_for_sale_by');
    }

    /**
     * Check if the vehicle has been assigned to sales team.
     */
    public function isAssignedToSales(): bool
    {
        return $this->status === self::STATUS_ASSIGNED_TO_SALES 
            && $this->sales_team_id !== null;
    }

    /**
     * Assign the vehicle to a sales team member.
     */
    public function assignToSalesTeam(int $salesTeamId, int $assignedById): bool
    {
        return $this->update([
            'status' => self::STATUS_ASSIGNED_TO_SALES,
            'sales_team_id' => $salesTeamId,
            'assigned_for_sale_by' => $assignedById,
            'assigned_for_sale_at' => now(),
        ]);
    }

    /**
     * Get all vehicles that are ready to be assigned to sales.
     */
    public function scopeReadyForSalesAssignment($query)
    {
        return $query->where('status', self::STATUS_READY_FOR_SALE);
    }

    /**
     * Get all vehicles assigned to sales.
     */
    public function scopeAssignedToSales($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED_TO_SALES);
    }

    /**
     * Check if the vehicle is in a transport-related status
     */
    public function isInTransportProcess(): bool
    {
        $transportStatuses = [
            self::STATUS_TRANSPORT_PENDING,
            self::STATUS_TRANSPORT_IN_TRANSIT,
            self::STATUS_TRANSPORT_IN_PROGRESS,
            self::STATUS_TRANSPORT_DELIVERED,
        ];

        return in_array($this->status, $transportStatuses);
    }

    /**
     * Check if the vehicle is in an inspection-related status
     */
    public function isInInspectionProcess(): bool
    {
        $inspectionStatuses = [
            self::STATUS_INSPECTION_PENDING,
            self::STATUS_INSPECTION_IN_PROGRESS,
        ];

        return in_array($this->status, $inspectionStatuses);
    }

    /**
     * Check if the vehicle is in a repair-related status
     */
    public function isInRepairProcess(): bool
    {
        $repairStatuses = [
            self::STATUS_REPAIR_PENDING,
            self::STATUS_REPAIR_IN_PROGRESS,
        ];

        return in_array($this->status, $repairStatuses);
    }

    /**
     * Check if the vehicle is in a sales-related status
     */
    public function isInSalesProcess(): bool
    {
        $salesStatuses = [
            self::STATUS_READY_FOR_SALE,
            self::STATUS_READY_FOR_SALE_ASSIGNED,
        ];

        return in_array($this->status, $salesStatuses);
    }

    /**
     * Check if the vehicle is in a goodwill claims-related status
     */
    public function isInGoodwillClaimsProcess(): bool
    {
        $goodwillStatuses = [
            self::STATUS_GOODWILL_CLAIMS,
            self::STATUS_GOODWILL_CLAIMS_ASSIGNED,
        ];

        return in_array($this->status, $goodwillStatuses);
    }

    /**
     * Check if the vehicle is available
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Check if the vehicle is sold
     */
    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD;
    }

    /**
     * Check if the vehicle is archived
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVE;
    }

    /**
     * Update vehicle status
     */
    public function updateStatus(string $newStatus): bool
    {
        return app(\App\Services\VehicleStatusService::class)->updateStatus($this, $newStatus);
    }

    /**
     * Get status category
     */
    public function getStatusCategory(): ?string
    {
        return app(\App\Services\VehicleStatusService::class)->getStatusCategory($this->status);
    }

    /**
     * Get available status transitions
     */
    public function getAvailableStatusTransitions(): array
    {
        return app(\App\Services\VehicleStatusService::class)->getAvailableTransitions($this->status);
    }

    /**
     * Scope for available vehicles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope for transport process vehicles
     */
    public function scopeInTransportProcess($query)
    {
        return $query->whereIn('status', [
            self::STATUS_TRANSPORT_PENDING,
            self::STATUS_TRANSPORT_IN_TRANSIT,
            self::STATUS_TRANSPORT_IN_PROGRESS,
            self::STATUS_TRANSPORT_DELIVERED,
        ]);
    }

    /**
     * Scope for inspection process vehicles
     */
    public function scopeInInspectionProcess($query)
    {
        return $query->whereIn('status', [
            self::STATUS_INSPECTION_PENDING,
            self::STATUS_INSPECTION_IN_PROGRESS,
        ]);
    }

    /**
     * Scope for repair process vehicles
     */
    public function scopeInRepairProcess($query)
    {
        return $query->whereIn('status', [
            self::STATUS_REPAIR_PENDING,
            self::STATUS_REPAIR_IN_PROGRESS,
        ]);
    }

    /**
     * Scope for sales process vehicles
     */
    public function scopeInSalesProcess($query)
    {
        return $query->whereIn('status', [
            self::STATUS_READY_FOR_SALE,
            self::STATUS_READY_FOR_SALE_ASSIGNED,
        ]);
    }

    /**
     * Scope for goodwill claims process vehicles
     */
    public function scopeInGoodwillClaimsProcess($query)
    {
        return $query->whereIn('status', [
            self::STATUS_GOODWILL_CLAIMS,
            self::STATUS_GOODWILL_CLAIMS_ASSIGNED,
        ]);
    }

    /**
     * Scope for sold vehicles
     */
    public function scopeSold($query)
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    /**
     * Scope for archived vehicles
     */
    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVE);
    }

    /**
     * Scope for vehicles by status category
     */
    public function scopeByStatusCategory($query, string $category)
    {
        $statusService = app(\App\Services\VehicleStatusService::class);
        $statuses = [];
        
        foreach ($statusService->getAllStatuses() as $status) {
            if ($statusService->getStatusCategory($status) === $category) {
                $statuses[] = $status;
            }
        }
        
        return $query->whereIn('status', $statuses);
    }

    /**
     * Get the vehicle reads for the vehicle.
     */
    public function vehicleReads(): HasMany
    {
        return $this->hasMany(VehicleRead::class);
    }

    /**
     * Check if vehicle has been read by the specified user.
     */
    public function isReadByUser($userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        
        return $this->vehicleReads()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Mark this vehicle as read by a user.
     */
    public function markAsRead($userId = null): void
    {
        $userId = $userId ?? auth()->id();
        
        if (!$this->isReadByUser($userId)) {
            $this->vehicleReads()->create([
                'user_id' => $userId
            ]);
        }
    }

    /**
     * Helper method to normalize status values for consistent comparison
     *
     * @param string $status The status to normalize
     * @return string The normalized status constant
     */
    public static function normalizeStatus($status)
    {
        // Map of common status strings to their constant values
        $statusMap = [
            'ready for sale' => self::STATUS_READY_FOR_SALE,
            'repairs completed' => self::STATUS_REPAIRS_COMPLETED,
            'repair completed' => self::STATUS_REPAIR_COMPLETED,
            'assigned to sales' => self::STATUS_ASSIGNED_TO_SALES,
        ];

        // Convert to lowercase for case-insensitive comparison
        $lowerStatus = strtolower($status);
        
        // Return mapped constant if it exists, otherwise return original
        return $statusMap[$lowerStatus] ?? $status;
    }

    /**
     * Check if vehicle status matches one of the provided statuses
     *
     * @param array|string $validStatuses One or more valid statuses to check against
     * @return bool Whether the vehicle's status matches any of the valid statuses
     */
    public function hasStatus($validStatuses)
    {
        if (!is_array($validStatuses)) {
            $validStatuses = [$validStatuses];
        }
        
        // Normalize both the vehicle's status and the valid statuses for comparison
        $normalizedStatus = self::normalizeStatus($this->status);
        $normalizedValidStatuses = array_map([self::class, 'normalizeStatus'], $validStatuses);
        
        return in_array($normalizedStatus, $normalizedValidStatuses);
    }
    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}

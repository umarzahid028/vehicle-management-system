<?php

namespace Tests\Feature;

use App\Events\NewVehicleEvent;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_vehicle_event_is_dispatched_when_vehicle_created()
    {
        // Arrange
        Event::fake([NewVehicleEvent::class]);
        
        $admin = User::factory()->create()->assignRole('Admin');
        $this->actingAs($admin);
        
        // Act
        $response = $this->post(route('vehicles.store'), [
            'stock_number' => 'TEST123',
            'vin' => 'TEST12345678901234',
            'year' => 2022,
            'make' => 'Test Make',
            'model' => 'Test Model',
        ]);
        
        // Assert
        $response->assertRedirect(route('vehicles.index'));
        $response->assertSessionHas('success');
        
        Event::assertDispatched(NewVehicleEvent::class, function ($event) {
            return $event->vehicleCount === 1 && 
                   count($event->vehicleIds) === 1 && 
                   $event->eventType === 'new';
        });
    }

    public function test_new_vehicle_event_is_dispatched_when_vehicle_updated()
    {
        // Arrange
        Event::fake([NewVehicleEvent::class]);
        
        $admin = User::factory()->create()->assignRole('Admin');
        $this->actingAs($admin);
        
        $vehicle = Vehicle::create([
            'stock_number' => 'TEST123',
            'vin' => 'TEST12345678901234',
            'year' => 2022,
            'make' => 'Test Make',
            'model' => 'Test Model',
        ]);
        
        // Act
        $response = $this->put(route('vehicles.update', $vehicle->id), [
            'stock_number' => 'TEST123',
            'vin' => 'TEST12345678901234',
            'year' => 2023, // Changed year
            'make' => 'Test Make',
            'model' => 'Test Model',
        ]);
        
        // Assert
        $response->assertRedirect(route('vehicles.index'));
        $response->assertSessionHas('success');
        
        Event::assertDispatched(NewVehicleEvent::class, function ($event) use ($vehicle) {
            return $event->vehicleCount === 1 && 
                   count($event->vehicleIds) === 1 && 
                   $event->vehicleIds[0] === $vehicle->id && 
                   $event->eventType === 'update';
        });
    }
}

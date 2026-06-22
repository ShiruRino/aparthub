<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacilityManagementController extends Controller
{
    /**
     * Show the facility workspace.
     */
    public function index(): View
    {
        $facilities = Facility::query()
            ->withCount('bookings')
            ->orderBy('name')
            ->get();

        $bookings = FacilityBooking::query()
            ->with(['facility', 'resident.unit'])
            ->latest('booking_date')
            ->get();

        return view('facility-management.index', [
            'facilities' => $facilities,
            'bookings' => $bookings,
            'residentOptions' => Resident::query()->with('unit')->orderBy('name')->get(),
            'statusOptions' => ['Available', 'Booked', 'Maintenance'],
            'bookingStatusOptions' => ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
            'categoryOptions' => ['Event Space', 'Meeting', 'Recreation', 'Sports', 'Community'],
        ]);
    }

    public function storeFacility(Request $request): RedirectResponse
    {
        Facility::query()->create($this->validatedFacility($request));

        return redirect()->route('facility-management.index')->with('status', 'Facility berhasil ditambahkan.');
    }

    public function updateFacility(Request $request, Facility $facility): RedirectResponse
    {
        $facility->update($this->validatedFacility($request));

        return redirect()->route('facility-management.index')->with('status', 'Facility berhasil diperbarui.');
    }

    public function destroyFacility(Facility $facility): RedirectResponse
    {
        if ($facility->bookings()->exists()) {
            return redirect()->route('facility-management.index')->withErrors([
                'facility' => 'Facility yang masih memiliki booking tidak bisa dihapus.',
            ]);
        }

        $facility->delete();

        return redirect()->route('facility-management.index')->with('status', 'Facility berhasil dihapus.');
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        FacilityBooking::query()->create($this->validatedBooking($request));

        return redirect()->route('facility-management.index')->with('status', 'Booking facility berhasil dibuat.');
    }

    public function updateBooking(Request $request, FacilityBooking $booking): RedirectResponse
    {
        $booking->update($this->validatedBooking($request));

        return redirect()->route('facility-management.index')->with('status', 'Booking facility berhasil diperbarui.');
    }

    public function destroyBooking(FacilityBooking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('facility-management.index')->with('status', 'Booking facility berhasil dihapus.');
    }

    /**
     * Validate facility payload.
     *
     * @return array<string, mixed>
     */
    private function validatedFacility(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Available', 'Booked', 'Maintenance'])],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);
    }

    /**
     * Validate booking payload.
     *
     * @return array<string, mixed>
     */
    private function validatedBooking(Request $request): array
    {
        return $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'resident_id' => ['required', 'exists:residents,id'],
            'booking_title' => ['required', 'string', 'max:255'],
            'booking_date' => ['required', 'date'],
            'time_slot' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Pending', 'Confirmed', 'Completed', 'Cancelled'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

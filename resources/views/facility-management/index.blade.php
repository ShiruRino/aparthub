@extends('layouts.app')

@section('title', 'Facility Management')
@section('topbar_context', 'Facility Management')
@section('topbar_subtitle', 'Kelola fasilitas, status ketersediaan, dan booking facility penghuni.')

@php
    $statusClass = fn (string $status) => match ($status) {
        'Available', 'Confirmed', 'Completed' => 'status-approved',
        'Booked', 'Pending' => 'status-pending',
        'Maintenance', 'Cancelled' => 'status-rejected',
        default => 'status-expired',
    };
@endphp

@section('content')
    <div class="facility-page">
        <section class="visitor-toolbar">
            <div class="visitor-heading">
                <span class="visitor-step">OPS</span>
                <div>
                    <h2>Facility Management</h2>
                    <p>Workspace operasional fasilitas dan booking resident dalam satu halaman.</p>
                </div>
            </div>
            <div class="visitor-toolbar-actions">
                <button class="btn secondary" type="button" data-modal-open="facility-booking-modal">Create Booking</button>
                <button class="btn" type="button" data-modal-open="facility-form-modal">Add Facility</button>
            </div>
        </section>

        <div class="visitor-grid">
            <section class="visitor-panel visitor-span-8">
                <div class="visitor-panel-head">
                    <h2 class="visitor-panel-title">Facilities</h2>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Facility</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Capacity</th>
                                <th>Bookings</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($facilities as $facility)
                                <tr>
                                    <td>
                                        <strong>{{ $facility->name }}</strong>
                                        <div style="color:#67758a;">{{ $facility->description }}</div>
                                    </td>
                                    <td>{{ $facility->category }}</td>
                                    <td>{{ $facility->location }}</td>
                                    <td><span class="resident-status {{ $statusClass($facility->status) }}">{{ $facility->status }}</span></td>
                                    <td>{{ $facility->capacity }}</td>
                                    <td>{{ $facility->bookings_count }}</td>
                                    <td>
                                        <div class="resident-action-row">
                                            @include('partials.icon-action-button', ['icon' => 'eye', 'label' => 'View Facility Detail', 'modal' => 'facility-detail-modal-'.$facility->id])
                                            @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Facility', 'modal' => 'facility-edit-modal-'.$facility->id])
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7">Belum ada facility.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="visitor-panel visitor-span-8">
                <div class="visitor-panel-head">
                    <h2 class="visitor-panel-title">Facility Bookings</h2>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Facility</th>
                                <th>Resident</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookings as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking->booking_title }}</strong>
                                        <div style="color:#67758a;">{{ $booking->notes }}</div>
                                    </td>
                                    <td>{{ $booking->facility?->name ?? '-' }}</td>
                                    <td>{{ $booking->resident?->name ?? '-' }}<br><span style="color:#67758a;">{{ $booking->resident?->unit?->code ? 'Unit '.$booking->resident->unit->code : '-' }}</span></td>
                                    <td>{{ $booking->booking_date?->format('d M Y') }}</td>
                                    <td>{{ $booking->time_slot }}</td>
                                    <td><span class="resident-status {{ $statusClass($booking->status) }}">{{ $booking->status }}</span></td>
                                    <td>
                                        <div class="resident-action-row">
                                            @include('partials.icon-action-button', ['icon' => 'eye', 'label' => 'View Booking Detail', 'modal' => 'facility-booking-detail-modal-'.$booking->id])
                                            @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Booking', 'modal' => 'facility-booking-edit-modal-'.$booking->id])
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7">Belum ada booking facility.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="visitor-modal" id="facility-form-modal" aria-hidden="true">
            <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
            <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="facility-form-title">
                <div class="visitor-modal-head">
                    <h2 class="visitor-modal-title" id="facility-form-title">Add Facility</h2>
                    <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                </div>
                <form class="visitor-modal-body" method="POST" action="{{ route('facility-management.facilities.store') }}">
                    @csrf
                    <div class="visitor-form-grid">
                        <label class="resident-filter-field"><span>Name</span><input type="text" name="name" required></label>
                        <label class="resident-filter-field"><span>Location</span><input type="text" name="location" required></label>
                        <label class="resident-filter-field">
                            <span>Category</span>
                            <select name="category" required>
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Status</span>
                            <select name="status" required>
                                @foreach ($statusOptions as $facilityStatus)
                                    <option value="{{ $facilityStatus }}">{{ $facilityStatus }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field"><span>Capacity</span><input type="number" min="1" name="capacity" value="1" required></label>
                    </div>
                    <label class="resident-filter-field" style="margin-top:14px;"><span>Description</span><textarea name="description" rows="4"></textarea></label>
                    <div class="visitor-form-actions">
                        <button class="btn secondary" type="button" data-modal-close>Batal</button>
                        <button class="btn" type="submit">Simpan Facility</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="visitor-modal" id="facility-booking-modal" aria-hidden="true">
            <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
            <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="facility-booking-title">
                <div class="visitor-modal-head">
                    <h2 class="visitor-modal-title" id="facility-booking-title">Create Facility Booking</h2>
                    <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                </div>
                <form class="visitor-modal-body" method="POST" action="{{ route('facility-management.bookings.store') }}">
                    @csrf
                    <div class="visitor-form-grid">
                        <label class="resident-filter-field"><span>Booking Title</span><input type="text" name="booking_title" required></label>
                        <label class="resident-filter-field">
                            <span>Facility</span>
                            <select name="facility_id" required>
                                @foreach ($facilities as $facility)
                                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Resident</span>
                            <select name="resident_id" required>
                                @foreach ($residentOptions as $resident)
                                    <option value="{{ $resident->id }}">{{ $resident->name }}{{ $resident->unit?->code ? ' - Unit '.$resident->unit->code : '' }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field"><span>Booking Date</span><input type="date" name="booking_date" required></label>
                        <label class="resident-filter-field"><span>Time Slot</span><input type="text" name="time_slot" placeholder="09:00 - 11:00" required></label>
                        <label class="resident-filter-field">
                            <span>Status</span>
                            <select name="status" required>
                                @foreach ($bookingStatusOptions as $bookingStatus)
                                    <option value="{{ $bookingStatus }}">{{ $bookingStatus }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <label class="resident-filter-field" style="margin-top:14px;"><span>Notes</span><textarea name="notes" rows="4"></textarea></label>
                    <div class="visitor-form-actions">
                        <button class="btn secondary" type="button" data-modal-close>Batal</button>
                        <button class="btn" type="submit">Simpan Booking</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($facilities as $facility)
            @include('partials.action-preview-modal', [
                'id' => 'facility-detail-modal-'.$facility->id,
                'title' => 'Facility Detail',
                'summary' => $facility->name,
                'subtitle' => $facility->category,
                'avatar' => 'FM',
                'rows' => [
                    ['Location', $facility->location],
                    ['Status', $facility->status],
                    ['Capacity', (string) $facility->capacity],
                    ['Bookings', (string) $facility->bookings_count],
                    ['Description', $facility->description ?: '-'],
                ],
                'confirmLabel' => 'Close',
            ])

            <div class="visitor-modal" id="facility-edit-modal-{{ $facility->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="facility-edit-title-{{ $facility->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="facility-edit-title-{{ $facility->id }}">Edit Facility</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('facility-management.facilities.update', $facility) }}">
                        @csrf
                        @method('PUT')
                        <div class="visitor-form-grid">
                            <label class="resident-filter-field"><span>Name</span><input type="text" name="name" value="{{ $facility->name }}" required></label>
                            <label class="resident-filter-field"><span>Location</span><input type="text" name="location" value="{{ $facility->location }}" required></label>
                            <label class="resident-filter-field">
                                <span>Category</span>
                                <select name="category" required>
                                    @foreach ($categoryOptions as $category)
                                        <option value="{{ $category }}" @selected($facility->category === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Status</span>
                                <select name="status" required>
                                    @foreach ($statusOptions as $facilityStatus)
                                        <option value="{{ $facilityStatus }}" @selected($facility->status === $facilityStatus)>{{ $facilityStatus }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field"><span>Capacity</span><input type="number" min="1" name="capacity" value="{{ $facility->capacity }}" required></label>
                        </div>
                        <label class="resident-filter-field" style="margin-top:14px;"><span>Description</span><textarea name="description" rows="4">{{ $facility->description }}</textarea></label>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn" type="submit">Update Facility</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('facility-management.facilities.destroy', $facility) }}" style="padding:0 24px 24px;">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger" type="submit" onclick="return confirm('Hapus facility ini?')">Hapus Facility</button>
                    </form>
                </div>
            </div>
        @endforeach

        @foreach ($bookings as $booking)
            @include('partials.action-preview-modal', [
                'id' => 'facility-booking-detail-modal-'.$booking->id,
                'title' => 'Facility Booking Detail',
                'summary' => $booking->booking_title,
                'subtitle' => $booking->facility?->name ?? 'Facility booking',
                'avatar' => 'BK',
                'rows' => [
                    ['Resident', $booking->resident?->name ?? '-'],
                    ['Unit', $booking->resident?->unit?->code ? 'Unit '.$booking->resident->unit->code : '-'],
                    ['Booking Date', $booking->booking_date?->format('d M Y') ?? '-'],
                    ['Time Slot', $booking->time_slot],
                    ['Status', $booking->status],
                    ['Notes', $booking->notes ?: '-'],
                ],
                'confirmLabel' => 'Close',
            ])

            <div class="visitor-modal" id="facility-booking-edit-modal-{{ $booking->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="facility-booking-edit-title-{{ $booking->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="facility-booking-edit-title-{{ $booking->id }}">Edit Facility Booking</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('facility-management.bookings.update', $booking) }}">
                        @csrf
                        @method('PUT')
                        <div class="visitor-form-grid">
                            <label class="resident-filter-field"><span>Booking Title</span><input type="text" name="booking_title" value="{{ $booking->booking_title }}" required></label>
                            <label class="resident-filter-field">
                                <span>Facility</span>
                                <select name="facility_id" required>
                                    @foreach ($facilities as $facility)
                                        <option value="{{ $facility->id }}" @selected($booking->facility_id === $facility->id)>{{ $facility->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Resident</span>
                                <select name="resident_id" required>
                                    @foreach ($residentOptions as $resident)
                                        <option value="{{ $resident->id }}" @selected($booking->resident_id === $resident->id)>{{ $resident->name }}{{ $resident->unit?->code ? ' - Unit '.$resident->unit->code : '' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field"><span>Booking Date</span><input type="date" name="booking_date" value="{{ $booking->booking_date?->format('Y-m-d') }}" required></label>
                            <label class="resident-filter-field"><span>Time Slot</span><input type="text" name="time_slot" value="{{ $booking->time_slot }}" required></label>
                            <label class="resident-filter-field">
                                <span>Status</span>
                                <select name="status" required>
                                    @foreach ($bookingStatusOptions as $bookingStatus)
                                        <option value="{{ $bookingStatus }}" @selected($booking->status === $bookingStatus)>{{ $bookingStatus }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <label class="resident-filter-field" style="margin-top:14px;"><span>Notes</span><textarea name="notes" rows="4">{{ $booking->notes }}</textarea></label>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn" type="submit">Update Booking</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('facility-management.bookings.destroy', $booking) }}" style="padding:0 24px 24px;">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger" type="submit" onclick="return confirm('Hapus booking ini?')">Hapus Booking</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection

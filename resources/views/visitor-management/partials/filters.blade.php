<form method="GET" action="{{ $action ?? url()->current() }}" class="visitor-table-filters" data-auto-submit-get>
    @if (! empty($showStatus ?? true))
        <select name="status" aria-label="Visitor status filter" data-auto-submit-control>
            <option value="">All Status</option>
            @foreach (($statusOptions ?? []) as $statusOption)
                <option value="{{ $statusOption }}" @selected(($filters['status'] ?? null) === $statusOption)>{{ $statusOption }}</option>
            @endforeach
        </select>
    @endif

    <input type="date" name="visit_date" value="{{ $filters['visit_date'] ?? '' }}" aria-label="Visit date">

    @if (! empty($showSource ?? false))
        <select name="registration_source" aria-label="Registration source filter" data-auto-submit-control>
            <option value="">All Source</option>
            @foreach (($registrationSources ?? []) as $sourceOption)
                <option value="{{ $sourceOption }}" @selected(($filters['registration_source'] ?? null) === $sourceOption)>{{ $sourceOption }}</option>
            @endforeach
        </select>
    @endif

    @if (! empty($showResident ?? false))
        <select name="resident_id" aria-label="Resident filter" data-auto-submit-control>
            <option value="">All Resident / Unit</option>
            @foreach (($residentOptions ?? []) as $residentOption)
                <option value="{{ $residentOption->id }}" @selected((int) ($filters['resident_id'] ?? 0) === $residentOption->id)>
                    {{ $residentOption->name }}{{ $residentOption->unit?->code ? ' - '.$residentOption->unit->code : '' }}
                </option>
            @endforeach
        </select>
    @endif

    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ $search ?? 'Search visitor...' }}" aria-label="Search visitor">
    <button class="btn secondary" type="submit">Filter</button>
</form>

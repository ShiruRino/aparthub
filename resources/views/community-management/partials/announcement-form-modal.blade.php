@php
    $categories = collect($categories ?? [])->filter()->values();
@endphp

<div class="visitor-modal" id="announcement-form-modal" aria-hidden="true">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="announcement-form-modal-title">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="announcement-form-modal-title" data-announcement-modal-title>Create Announcement</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>
        <div class="visitor-modal-body">
            <form method="POST" action="{{ route('community-management.announcements.store') }}" data-announcement-form>
                @csrf
                <input type="hidden" name="_method" value="POST" data-announcement-method>
                <input type="hidden" name="redirect_search" value="{{ request('search') }}">
                <input type="hidden" name="redirect_status" value="{{ request('status') }}">
                <input type="hidden" name="redirect_category" value="{{ request('category') }}">

                <div class="visitor-modal-grid">
                    <label class="form-group" style="grid-column: span 2;">
                        <span>Title</span>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            maxlength="255"
                            data-announcement-input="title"
                        >
                    </label>

                    <label class="form-group">
                        <span>Category</span>
                        <input
                            type="text"
                            name="category"
                            list="announcement-categories"
                            value="{{ old('category', 'General') }}"
                            maxlength="100"
                            data-announcement-input="category"
                        >
                    </label>

                    <label class="form-group">
                        <span>Status</span>
                        <select name="status" required data-announcement-input="status">
                            <option value="Draft" @selected(old('status', 'Draft') === 'Draft')>Draft</option>
                            <option value="Published" @selected(old('status') === 'Published')>Published</option>
                        </select>
                    </label>

                    <label class="form-group" style="grid-column: span 2;">
                        <span>Content</span>
                        <textarea
                            name="content"
                            rows="8"
                            required
                            data-announcement-input="content"
                        >{{ old('content') }}</textarea>
                    </label>

                    <label class="checkbox" style="grid-column: span 2; display:flex; align-items:center; gap:10px;">
                        <input
                            type="checkbox"
                            name="is_pinned"
                            value="1"
                            @checked(old('is_pinned'))
                            data-announcement-input="is_pinned"
                        >
                        <span>Pin to Resident Homepage</span>
                    </label>
                </div>

                <datalist id="announcement-categories">
                    @foreach ($categories as $category)
                        <option value="{{ $category }}"></option>
                    @endforeach
                </datalist>

                <div class="visitor-form-actions">
                    <button class="btn secondary" type="button" data-modal-close>Cancel</button>
                    <button class="btn" type="submit" data-announcement-submit-label>Save Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

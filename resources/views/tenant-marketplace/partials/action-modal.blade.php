<div class="visitor-modal" id="tenant-action-modal" aria-hidden="true">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="tenant-action-modal-title">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="tenant-action-modal-title" data-modal-slot="title">Tenant Marketplace Preview</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>
        <div class="visitor-modal-body">
            <div class="visitor-detail-top">
                <div class="tenant-logo blue" data-modal-accent="tenant-logo" data-modal-slot="accent">TM</div>
                <div>
                    <strong class="visitor-detail-name" data-modal-slot="headline">Static Tenant Workflow</strong>
                    <small class="muted" data-modal-slot="summary">Data dan form tenant masih dummy, belum disimpan ke backend.</small>
                </div>
            </div>

            <div class="visitor-detail-section">
                <h3 data-modal-slot="section-title">Tenant Context</h3>
                <div class="visitor-info-row"><span>Workspace</span><strong data-modal-slot="workspace">Tenant Directory</strong></div>
                <div class="visitor-info-row"><span>Entity</span><strong data-modal-slot="entity">Brew Cabin Coffee</strong></div>
                <div class="visitor-info-row"><span>Status</span><strong data-modal-slot="status">Preview Only</strong></div>
                <div class="visitor-info-row"><span>Next Step</span><strong data-modal-slot="next-step">Tinjau detail tenant atau lanjutkan aksi dummy.</strong></div>
            </div>

            <div class="visitor-detail-section">
                <h3>Action Notes</h3>
                <p class="muted" style="margin:0;" data-modal-slot="copy">Aksi ini hanya menampilkan preview interaksi tenant marketplace. Belum ada penyimpanan backend atau upload file nyata.</p>
            </div>

            <div class="visitor-form-actions">
                <button class="btn secondary" type="button" data-modal-close data-modal-slot="cancel-label">Close</button>
                <button class="btn" type="button" data-modal-slot="confirm-label">Confirm Preview</button>
            </div>
        </div>
    </div>
</div>

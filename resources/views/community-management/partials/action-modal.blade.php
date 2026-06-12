<div class="visitor-modal" id="community-action-modal" aria-hidden="true">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="community-action-modal-title">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="community-action-modal-title" data-modal-slot="title">Community Action Preview</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>
        <div class="visitor-modal-body">
            <div class="visitor-detail-top">
                <div class="visitor-detail-avatar blue" data-modal-accent="visitor-detail-avatar" data-modal-slot="accent">CM</div>
                <div>
                    <strong class="visitor-detail-name" data-modal-slot="headline">Static Community Workflow</strong>
                    <small class="muted" data-modal-slot="summary">Form dan tombol pada halaman ini masih dummy untuk preview UI.</small>
                </div>
            </div>

            <div class="visitor-detail-section">
                <h3 data-modal-slot="section-title">Community Context</h3>
                <div class="visitor-info-row"><span>Workspace</span><strong data-modal-slot="workspace">Announcement Center</strong></div>
                <div class="visitor-info-row"><span>Selected Item</span><strong data-modal-slot="entity">Elevator Maintenance Notice</strong></div>
                <div class="visitor-info-row"><span>Action</span><strong data-modal-slot="status">Preview Only</strong></div>
                <div class="visitor-info-row"><span>Next Step</span><strong data-modal-slot="next-step">Tinjau detail komunitas atau lanjutkan aksi dummy.</strong></div>
            </div>

            <div class="visitor-detail-section">
                <h3>Action Notes</h3>
                <p class="muted" style="margin:0;" data-modal-slot="copy">Semua aksi community management masih berupa preview visual. Belum ada penyimpanan, approval, atau broadcast backend sungguhan.</p>
            </div>

            <div class="visitor-form-actions">
                <button class="btn secondary" type="button" data-modal-close data-modal-slot="cancel-label">Close</button>
                <button class="btn" type="button" data-modal-slot="confirm-label">Confirm Preview</button>
            </div>
        </div>
    </div>
</div>

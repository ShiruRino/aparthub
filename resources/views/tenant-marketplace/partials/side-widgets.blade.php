<aside class="tenant-side" aria-label="Tenant marketplace side widgets">
    <section class="visitor-panel">
        <div class="visitor-panel-head">
            <h3 class="visitor-panel-title">Status Overview</h3>
        </div>
        <div class="visitor-panel-body">
            <div class="community-donut-panel">
                <div class="tenant-donut" data-value="48\A Total Tenants"></div>
                <div class="community-legend">
                    <span><span><i class="community-dot"></i> Active</span><strong>42 (87.5%)</strong></span>
                    <span><span><i class="community-dot gold"></i> Pending</span><strong>4 (8.3%)</strong></span>
                    <span><span><i class="community-dot" style="background:#e43f35;"></i> Inactive</span><strong>2 (4.2%)</strong></span>
                </div>
            </div>
        </div>
    </section>

    <section class="visitor-panel">
        <div class="visitor-panel-head">
            <h3 class="visitor-panel-title">Tenant Category</h3>
        </div>
        <div class="visitor-panel-body">
            <div class="tenant-category-list">
                @foreach ([
                    ['Cafe & Beverages', 10, 100],
                    ['Laundry', 8, 80],
                    ['Minimarket', 7, 70],
                    ['Cleaning Service', 6, 60],
                    ['Beauty & Salon', 5, 50],
                    ['Food & Beverages', 4, 40],
                    ['Pet Care', 3, 30],
                    ['Maintenance', 3, 30],
                    ['Lainnya', 2, 20],
                ] as [$label, $count, $width])
                    <div class="tenant-category-row">
                        <span>{{ $label }}</span>
                        <div class="tenant-category-track"><span style="width: {{ $width }}%;"></span></div>
                        <strong>{{ $count }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="visitor-panel">
        <div class="visitor-panel-head">
            <h3 class="visitor-panel-title">Informasi</h3>
        </div>
        <div class="visitor-panel-body">
            <div class="tenant-info-box">
                <div class="tenant-phone-icon">APP</div>
                <div>
                    <p class="muted">Data tenant diambil dari aplikasi Tenant by Mobile.</p>
                    <p class="muted">Untuk pengelolaan produk, pesanan, dan transaksi dilakukan melalui aplikasi tenant.</p>
                </div>
            </div>
        </div>
    </section>
</aside>

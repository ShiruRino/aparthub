@extends('layouts.app')

@php
    $navItems = [
        'announcements' => ['number' => 1, 'label' => 'Announcement Center', 'route' => 'community-management.announcements'],
        'events' => ['number' => 2, 'label' => 'Event Management', 'route' => 'community-management.events'],
        'polling-survey' => ['number' => 3, 'label' => 'Polling & Survey', 'route' => 'community-management.polling-survey'],
        'forum' => ['number' => 4, 'label' => 'Resident Forum', 'route' => 'community-management.forum'],
        'broadcasts' => ['number' => 5, 'label' => 'Broadcast Notification', 'route' => 'community-management.broadcasts'],
        'programs' => ['number' => 6, 'label' => 'Community Programs', 'route' => 'community-management.programs'],
        'calendar' => ['number' => 7, 'label' => 'Event Calendar', 'route' => 'community-management.calendar'],
        'engagement' => ['number' => 8, 'label' => 'Resident Engagement', 'route' => 'community-management.engagement'],
        'archive' => ['number' => 9, 'label' => 'Community Archive', 'route' => 'community-management.archive'],
        'settings' => ['number' => 10, 'label' => 'Community Settings', 'route' => 'community-management.settings'],
    ];

    $pageKey = $pageKey ?? 'announcements';

    $pages = [
        'announcements' => [
            'label' => 'Announcement Center',
            'title' => 'Community Management',
            'subtitle' => 'Kelola komunikasi, kegiatan, dan engagement komunitas penghuni.',
            'metrics' => [
                ['label' => 'Announcement', 'value' => '12', 'sub' => 'Published', 'icon' => 'A', 'tone' => 'purple'],
                ['label' => 'Events', 'value' => '8', 'sub' => 'Upcoming', 'icon' => 'E', 'tone' => 'green'],
                ['label' => 'Polling', 'value' => '4', 'sub' => 'Active', 'icon' => 'P', 'tone' => 'gold'],
                ['label' => 'Forum', 'value' => '24', 'sub' => 'Discussions', 'icon' => 'F', 'tone' => 'blue'],
                ['label' => 'Programs', 'value' => '6', 'sub' => 'Active', 'icon' => 'H', 'tone' => 'red'],
            ],
        ],
        'events' => [
            'label' => 'Event Management',
            'title' => 'Event Management',
            'subtitle' => 'Kelola komunikasi, kegiatan, dan engagement komunitas penghuni.',
            'metrics' => [
                ['label' => 'Total Events (YTD)', 'value' => '25', 'sub' => 'Total Events', 'icon' => 'T', 'tone' => 'purple'],
                ['label' => 'Upcoming Events', 'value' => '8', 'sub' => 'Upcoming', 'icon' => 'U', 'tone' => 'green'],
                ['label' => 'Live Now', 'value' => '1', 'sub' => 'Live Broadcasting', 'icon' => 'L', 'tone' => 'red'],
                ['label' => 'Past Events', 'value' => '16', 'sub' => 'Past Events', 'icon' => 'P', 'tone' => 'blue'],
                ['label' => 'Draft Events', 'value' => '2', 'sub' => 'Draft Events', 'icon' => 'D', 'tone' => 'gold'],
            ],
        ],
        'polling-survey' => [
            'label' => 'Polling & Survey',
            'title' => 'Polling & Survey Management',
            'subtitle' => 'Kumpulkan umpan balik, pendapat, dan masukan penghuni.',
            'metrics' => [
                ['label' => 'Total Polling (YTD)', 'value' => '15', 'sub' => 'Total Polling', 'icon' => 'T', 'tone' => 'purple'],
                ['label' => 'Polling Aktif', 'value' => '3', 'sub' => 'Polling Aktif', 'icon' => 'A', 'tone' => 'green'],
                ['label' => 'Live Saat Ini', 'value' => '1', 'sub' => 'Live Broadcasting', 'icon' => 'L', 'tone' => 'red'],
                ['label' => 'Selesai', 'value' => '10', 'sub' => 'Selesai', 'icon' => 'S', 'tone' => 'blue'],
                ['label' => 'Draft Polling', 'value' => '1', 'sub' => 'Draft Polling', 'icon' => 'D', 'tone' => 'gold'],
            ],
        ],
        'forum' => [
            'label' => 'Resident Forum',
            'title' => 'Resident Forum Management',
            'subtitle' => 'Kelola diskusi, topik, dan keterlibatan penduduk.',
            'metrics' => [
                ['label' => 'Total Topics (YTD)', 'value' => '110', 'sub' => 'Total Topics', 'icon' => 'T', 'tone' => 'purple'],
                ['label' => 'New Topics (June)', 'value' => '28', 'sub' => 'New Topics', 'icon' => 'N', 'tone' => 'green'],
                ['label' => 'Active Discussions', 'value' => '22', 'sub' => 'Live Broadcasting', 'icon' => 'A', 'tone' => 'red'],
                ['label' => 'Total Replies (YTD)', 'value' => '1.5k', 'sub' => 'Total Replies', 'icon' => 'R', 'tone' => 'blue'],
                ['label' => 'Most Active User', 'value' => 'Budi', 'sub' => 'A-1808', 'icon' => 'M', 'tone' => 'gold'],
            ],
        ],
        'broadcasts' => [
            'label' => 'Broadcast Notification',
            'title' => 'Broadcast Notification Management',
            'subtitle' => 'Kelola, jadwalkan, dan pantau siaran untuk semua penghuni.',
            'metrics' => [
                ['label' => 'Total Broadcasts (YTD)', 'value' => '150', 'sub' => 'Trend +10%', 'icon' => 'T', 'tone' => 'purple'],
                ['label' => 'Pending Broadcasts', 'value' => '5', 'sub' => 'Pending', 'icon' => 'P', 'tone' => 'gold'],
                ['label' => 'Scheduled Broadcasts', 'value' => '22', 'sub' => 'Scheduled', 'icon' => 'S', 'tone' => 'blue'],
                ['label' => 'Delivery Rate', 'value' => '98.5%', 'sub' => 'Delivered', 'icon' => 'D', 'tone' => 'green'],
                ['label' => 'Top Target', 'value' => 'Semua', 'sub' => 'Tower A', 'icon' => 'G', 'tone' => 'red'],
            ],
        ],
        'programs' => [
            'label' => 'Community Programs',
            'title' => 'Community Program Management',
            'subtitle' => 'Kelola program komunitas dan acara untuk penghuni.',
            'metrics' => [
                ['label' => 'Total Community Programs', 'value' => '25', 'sub' => 'Active or scheduled', 'icon' => 'T', 'tone' => 'purple'],
                ['label' => 'New Program Requests', 'value' => '2', 'sub' => 'Pending review', 'icon' => 'N', 'tone' => 'gold'],
                ['label' => 'Upcoming Programs', 'value' => '8', 'sub' => 'Next 30 days', 'icon' => 'U', 'tone' => 'blue'],
                ['label' => 'Registration Rate', 'value' => '85%', 'sub' => 'Average across programs', 'icon' => 'R', 'tone' => 'green'],
                ['label' => 'Popular Category', 'value' => 'Health', 'sub' => 'Highest attendance', 'icon' => 'P', 'tone' => 'gold'],
            ],
        ],
        'calendar' => [
            'label' => 'Event Calendar',
            'title' => 'Community Event Calendar',
            'subtitle' => 'View and manage all community programs and events in a full calendar view.',
            'metrics' => [],
        ],
        'engagement' => [
            'label' => 'Resident Engagement',
            'title' => 'Dasbor Keterlibatan Penduduk',
            'subtitle' => 'Enhance resident engagement, build community, and track interaction levels.',
            'metrics' => [],
        ],
        'archive' => [
            'label' => 'Community Archive',
            'title' => 'Arsip Komunitas',
            'subtitle' => 'Arsip dan kelola konten komunitas masa lalu: Pengumuman, Acara, Polling, dan Program.',
            'metrics' => [
                ['label' => 'Total Item Arsip', 'value' => '1,500', 'sub' => 'All content', 'icon' => 'I', 'tone' => 'gold'],
                ['label' => 'Pengumuman', 'value' => '350', 'sub' => 'Archived', 'icon' => 'A', 'tone' => 'purple'],
                ['label' => 'Acara Masa Lalu', 'value' => '210', 'sub' => 'Events', 'icon' => 'E', 'tone' => 'red'],
                ['label' => 'Posting Forum Diarsipkan', 'value' => '900', 'sub' => 'Forum posts', 'icon' => 'F', 'tone' => 'blue'],
                ['label' => 'Program', 'value' => '40', 'sub' => 'Reports', 'icon' => 'P', 'tone' => 'green'],
            ],
        ],
        'settings' => [
            'label' => 'Community Settings',
            'title' => 'Pengaturan Komunitas',
            'subtitle' => 'Konfigurasi dan kelola preferensi komunitas: Fitur, Akses, dan Integrasi.',
            'metrics' => [
                ['label' => 'Konfigurasi Fitur', 'value' => '25 Aktif', 'sub' => 'Feature toggles', 'icon' => 'F', 'tone' => 'gold'],
                ['label' => 'Izin Akses', 'value' => '3 Level', 'sub' => 'Role levels', 'icon' => 'A', 'tone' => 'green'],
                ['label' => 'Pengaturan Integrasi', 'value' => '2 Aktif', 'sub' => 'Active links', 'icon' => 'I', 'tone' => 'blue'],
            ],
        ],
    ];

    $page = $pages[$pageKey] ?? $pages['announcements'];

    $announcements = [
        ['icon' => '!', 'title' => 'Elevator Maintenance Notice', 'status' => 'Published', 'class' => 'status-approved', 'body' => 'Pemeliharaan elevator di Tower A akan dilakukan pada 10 Juni 2026 pukul 00:00 - 04:00 WIB.', 'views' => '1,248'],
        ['icon' => 'W', 'title' => 'Water Supply Interruption', 'status' => 'Scheduled', 'class' => 'status-pending', 'body' => 'Gangguan pasokan air bersih di seluruh area apartemen pada 12 Juni 2026.', 'views' => '856'],
        ['icon' => 'P', 'title' => 'New Parking Policy', 'status' => 'Published', 'class' => 'status-approved', 'body' => 'Kebijakan parkir baru mulai berlaku efektif 15 Juni 2026.', 'views' => '632'],
        ['icon' => 'G', 'title' => 'Garbage Disposal Update', 'status' => 'Draft', 'class' => 'status-expired', 'body' => 'Informasi perubahan jadwal pengambilan sampah organik dan anorganik.', 'views' => '212'],
    ];

    $upcomingEvents = [
        ['day' => '15', 'month' => 'Jun', 'title' => 'Morning Yoga Class', 'time' => '07:00 - 08:00 AM', 'place' => 'Sky Garden / Level 10', 'registered' => '45 Registered'],
        ['day' => '22', 'month' => 'Jun', 'title' => 'Community Gathering', 'time' => '04:00 - 07:00 PM', 'place' => 'Clubhouse / Level 1', 'registered' => '120 Registered'],
        ['day' => '29', 'month' => 'Jun', 'title' => 'Blood Donation', 'time' => '09:00 AM - 01:00 PM', 'place' => 'Function Hall / Level 1', 'registered' => '88 Registered'],
        ['day' => '05', 'month' => 'Jul', 'title' => 'Kids Drawing Competition', 'time' => '10:00 AM - 12:00 PM', 'place' => 'Function Hall / Level 1', 'registered' => '36 Registered'],
    ];

    $pollingBars = [
        ['label' => 'Gym / Fitness Center', 'value' => '42%', 'tone' => 'green'],
        ['label' => 'Kolam Renang', 'value' => '31%', 'tone' => 'blue'],
        ['label' => 'Children Playground', 'value' => '17%', 'tone' => 'gold'],
        ['label' => 'Ruang Serbaguna', 'value' => '10%', 'tone' => 'red'],
    ];

    $forumTopics = [
        ['title' => 'Rekomendasi Jasa Pindahan Terpercaya', 'author' => 'Budi Santoso', 'count' => 12],
        ['title' => 'Tips Menghemat Listrik di Apartemen', 'author' => 'Sarah Lim', 'count' => 8],
        ['title' => 'Jual Sofa Minimalis - Kondisi Like New', 'author' => 'Kevin Hartono', 'count' => 5],
        ['title' => 'Lost: Kunci Mobil Toyota Innova', 'author' => 'David Lee', 'count' => 7],
    ];

    $programs = [
        ['title' => 'Green Apartment Initiative', 'participants' => '124 Participants'],
        ['title' => 'Waste Management Program', 'participants' => '98 Participants'],
        ['title' => 'Blood Donor Community', 'participants' => '156 Participants'],
        ['title' => 'Safety Awareness Campaign', 'participants' => '76 Participants'],
    ];

    $tables = [
        'events' => [
            'filters' => ['Date Range', 'Venue', 'Category', 'Status'],
            'columns' => ['Date & Time', 'Event Name', 'Venue', 'Category', 'Status', 'Participants', 'Organizer', 'Actions'],
            'rows' => [
                ['date' => "07 Jun 2026\n07:00 AM", 'name' => "Morning Yoga Class\nSky Garden / Level 10", 'venue' => 'Sky Garden', 'category' => ['Fitness', 'status-approved'], 'status' => ['Upcoming', 'status-pending'], 'participants' => ['60', '120/150', 80], 'organizer' => 'Building Manager'],
                ['date' => "07 Jun 2026\n08:00 AM", 'name' => "Community Gathering\nClubhouse / Level 1", 'venue' => 'Clubhouse', 'category' => ['Social', 'status-approved'], 'status' => ['Social', 'status-approved'], 'participants' => ['75', '90/150', 60], 'organizer' => 'Resident Organization'],
                ['date' => "07 Jun 2026\n08:00 PM", 'name' => "Blood Donation\nFunction Hall / Level 1", 'venue' => 'Function Hall', 'category' => ['Admin', 'status-approved'], 'status' => ['Admin', 'status-approved'], 'participants' => ['58', '70/150', 46], 'organizer' => 'Nusantara Foundation'],
                ['date' => "08 Jun 2026\n08:00 PM", 'name' => "Kids Drawing Competition\nFunction Hall / Level 1", 'venue' => 'Function Hall', 'category' => ['Kids', 'status-approved'], 'status' => ['Health', 'status-approved'], 'participants' => ['48', '50/150', 33], 'organizer' => 'Resident Committee'],
                ['date' => "08 Jun 2026\n08:00 AM", 'name' => "Resident Town Hall Live Stream\nMain Hall", 'venue' => 'Function Hall', 'category' => ['Fitness', 'status-approved'], 'status' => ['Live Now', 'status-rejected'], 'participants' => ['120', '120/150', 80], 'organizer' => 'Resident Town Organization'],
            ],
        ],
        'polling-survey' => [
            'filters' => ['Date Range', 'Kategori Polling', 'Category', 'Status'],
            'columns' => ['No.', 'Tanggal', 'Judul Polling', 'Kategori', 'Topik', 'Status', 'Peserta', 'Pembuat', 'Tindakan'],
            'rows' => [
                ['no' => 1, 'date' => "07 Jun 2026\n08:00 AM", 'title' => 'Fasilitas Baru Apa yang Ingin Ditambahkan?', 'category' => 'Fasilitas', 'topic' => 'Amenity', 'status' => ['Selesai', 'status-approved'], 'participants' => ['327 Suara', '', 82], 'creator' => 'Ahmad Rizky'],
                ['no' => 2, 'date' => "07 Jun 2026\n09:00 AM", 'title' => 'Kepuasan Layanan Pemeliharaan Mei 2026', 'category' => 'Layanan', 'topic' => 'Maintenance', 'status' => ['Selesai', 'status-approved'], 'participants' => ['185 Responden', '', 68], 'creator' => 'Admin'],
                ['no' => 3, 'date' => "07 Jun 2026\n10:00 AM", 'title' => 'Waktu Pengambilan Sampah yang Diinginkan', 'category' => 'Operasional', 'topic' => 'Waste', 'status' => ['Draft', 'status-pending'], 'participants' => ['0 Suara', '', 4], 'creator' => 'Ahmad Rizky'],
                ['no' => 4, 'date' => "07 Jun 2026\n11:00 AM", 'title' => 'Keinginan Untuk Memasang Panel Surya di Roof Top', 'category' => 'Kebijakan', 'topic' => 'Eco', 'status' => ['Aktif', 'status-approved'], 'participants' => ['95 Suara', '', 55], 'creator' => 'Manajer'],
                ['no' => 5, 'date' => "07 Jun 2026\n12:00 PM", 'title' => 'Keamanan Area Parkir Level 10', 'category' => 'Keamanan', 'topic' => 'Parking', 'status' => ['Aktif', 'status-approved'], 'participants' => ['68 Suara', '', 42], 'creator' => 'Security'],
            ],
        ],
        'forum' => [
            'filters' => ['Tower', 'Kategori Forum', 'Status Forum'],
            'columns' => ['No.', 'Tanggal', 'Topik Diskusi', 'Kategori', 'Penulis', 'Balasan', 'Dilihat', 'Tindakan'],
            'rows' => [
                ['no' => 1, 'date' => "07 Jun 2026\n08:00 AM", 'title' => 'Proposal Peningkatan Fasilitas Gym', 'category' => ['Suggestion', 'status-pending'], 'author' => "Budi Santoso\nA-1808", 'reply' => '[25]', 'views' => '[150]'],
                ['no' => 2, 'date' => "07 Jun 2026\n09:00 AM", 'title' => 'Keluhan Suara di Tower A', 'category' => ['Maintenance', 'status-expired'], 'author' => "Sarah Lim\nA-1205", 'reply' => '[12]', 'views' => '[80]'],
                ['no' => 3, 'date' => "07 Jun 2026\n10:00 AM", 'title' => 'Jam Operasional Kolam Renang', 'category' => ['Suggestion', 'status-pending'], 'author' => "Manajer Gedung\nAhmad Rizky", 'reply' => '[30]', 'views' => '[210]'],
                ['no' => 4, 'date' => "07 Jun 2026\n11:00 PM", 'title' => 'Jual Sofa Minimalis - Tower B', 'category' => ['Social', 'status-approved'], 'author' => "David Lee\nB-0910", 'reply' => '[60]', 'views' => '[450]'],
                ['no' => 5, 'date' => "07 Jun 2026\n12:00 PM", 'title' => 'Lost & Found: Kunci Mobil Toyota', 'category' => ['General', 'status-expired'], 'author' => "Sarah Lim\nA-1205", 'reply' => '[22]', 'views' => '[90]'],
            ],
        ],
        'broadcasts' => [
            'filters' => ['Tower', 'Target Audience', 'Broadcast Status'],
            'columns' => ['No.', 'Tanggal', 'Judul Siaran', 'Channel', 'Target Audience', 'Status', 'Pengirim', 'Tindakan'],
            'rows' => [
                ['no' => 1, 'date' => "07 Jun 2026\n08:30 AM", 'title' => 'Pemeliharaan Elevator Tower A', 'channel' => 'Email, Push', 'target' => 'Tower A', 'status' => ['Sent', 'status-approved'], 'sender' => 'Admin System'],
                ['no' => 2, 'date' => "07 Jun 2026\n09:00 AM", 'title' => 'Pengingat Iuran Juni Tower A', 'channel' => 'Mobile', 'target' => 'Semua Penghuni', 'status' => ['Sent', 'status-approved'], 'sender' => 'Ahmad Rizky'],
                ['no' => 3, 'date' => "07 Jun 2026\n10:15 AM", 'title' => 'Pengumuman Acara BBQ Komunitas', 'channel' => 'Email, Push', 'target' => 'Tower A & B', 'status' => ['Sent', 'status-approved'], 'sender' => 'Community Manager'],
                ['no' => 4, 'date' => "07 Jun 2026\n11:30 AM", 'title' => 'Kebijakan Parkir Baru', 'channel' => 'Mobile, Email', 'target' => 'Tower A, Pemilik Unit', 'status' => ['Scheduled', 'status-pending'], 'sender' => 'Ahmad Rizky'],
                ['no' => 5, 'date' => "06 Jun 2026\n04:00 PM", 'title' => 'Tenant Baru: Warkop Pak De', 'channel' => 'Mobile', 'target' => 'Tower A', 'status' => ['Sent', 'status-approved'], 'sender' => 'Admin System'],
            ],
        ],
        'programs' => [
            'filters' => ['Tower', 'Target Audience', 'Category', 'Program Status'],
            'columns' => ['No.', 'Tanggal', 'Nama Program', 'Kategori', 'Target Audience', 'Status', 'Jumlah Peserta', 'Penyelenggara', 'Tindakan'],
            'rows' => [
                ['no' => 1, 'date' => "08 Jun 2026\n07:00 AM", 'title' => 'Morning Yoga Class', 'category' => ['Kesehatan', 'status-approved'], 'target' => 'Semua', 'status' => ['Aktif', 'status-approved'], 'participants' => ['25/30', '', 83], 'organizer' => 'Staf'],
                ['no' => 2, 'date' => "10 Jun 2026\n10:00 AM", 'title' => 'Kids Drawing Workshop', 'category' => ['Anak-Anak', 'status-pending'], 'target' => 'Tower A & B', 'status' => ['Akan Datang', 'status-pending'], 'participants' => ['18/20', '', 90], 'organizer' => 'Vendor'],
                ['no' => 3, 'date' => "11 Jun 2026\n06:00 PM", 'title' => 'Apartment BBQ Night', 'category' => ['Sosial', 'status-expired'], 'target' => 'Semua', 'status' => ['Akan Datang', 'status-pending'], 'participants' => ['110/150', '', 73], 'organizer' => 'Komite'],
                ['no' => 4, 'date' => "15 Jun 2026\n09:00 AM", 'title' => 'Senior Fitness Session', 'category' => ['Kesehatan', 'status-approved'], 'target' => 'Tower A', 'status' => ['Draft', 'status-pending'], 'participants' => ['0/15', '', 5], 'organizer' => 'Staf'],
                ['no' => 5, 'date' => "05 Jun 2026\n08:00 AM", 'title' => 'Community Gardening', 'category' => ['Lingkungan', 'status-approved'], 'target' => 'Tower A & B', 'status' => ['Selesai', 'status-approved'], 'participants' => ['40/40', '', 100], 'organizer' => 'Komite'],
            ],
        ],
        'archive' => [
            'filters' => ['Cari arsip...', 'Content Type', 'Tahun/Bulan', 'Kategori'],
            'columns' => ['Ikon', 'Content Type', 'Judul Konten', 'Tanggal', 'Penulis', 'Kategori', 'Tindakan'],
            'rows' => [
                ['icon' => 'A', 'type' => 'Announcement', 'title' => 'Lift Maintenance Q2 2026', 'date' => '15 Mar 2026', 'author' => 'Admin', 'category' => 'Maintenance'],
                ['icon' => 'E', 'type' => 'Event', 'title' => 'Community Summer BBQ', 'date' => '20 Aug 2025', 'author' => 'Social Committee', 'category' => 'Social'],
                ['icon' => 'P', 'type' => 'Poll', 'title' => 'Gym Equipment Prefs', 'date' => '10 Jan 2026', 'author' => 'Management', 'category' => 'Amenities'],
                ['icon' => 'F', 'type' => 'Forum Post', 'title' => 'Roof Garden Proposal', 'date' => '05 Nov 2025', 'author' => 'Resident (U-102)', 'category' => 'Suggestions'],
                ['icon' => 'R', 'type' => 'Program', 'title' => "Senior's Wellness Pilot", 'date' => 'Oct 2025', 'author' => 'Management', 'category' => 'Health'],
            ],
        ],
        'settings' => [
            'filters' => ['Cari pengaturan...'],
            'columns' => ['Ikon', 'Nama Pengaturan', 'Deskripsi', 'Status', 'Tindakan'],
            'rows' => [
                ['icon' => 'F', 'name' => 'Forum Resident', 'description' => 'Aktifkan forum resident untuk diskusi umum.', 'status' => 'on'],
                ['icon' => 'N', 'name' => 'Notifikasi Push', 'description' => 'Kirim pengumuman mendesak sebagai notifikasi push.', 'status' => 'on'],
                ['icon' => 'R', 'name' => 'Reservasi Fasilitas', 'description' => 'Izinkan reservasi fasilitas komunitas.', 'status' => 'off'],
                ['icon' => 'P', 'name' => 'Polling Berkala', 'description' => 'Aktifkan polling untuk umpan balik komunitas.', 'status' => 'on'],
                ['icon' => 'I', 'name' => 'Integrasi Kalender Google', 'description' => 'Hubungkan kalender acara komunitas ke Kalender Google.', 'status' => 'config'],
            ],
        ],
    ];

    $createOptions = [
        'announcements' => ['Create Announcement', 'Create Event', 'Send Broadcast', 'Create Polling'],
        'events' => ['Create New Event', 'Create New Recurring Event'],
        'polling-survey' => ['Buat Polling Baru', 'Buat Survey Baru'],
        'forum' => ['Create Forum Topic', 'Pin Announcement Topic'],
        'broadcasts' => ['Create Broadcast', 'Schedule Broadcast'],
        'programs' => ['Create Program', 'Review Program Request'],
        'calendar' => ['Create Calendar Event', 'Import Event'],
        'engagement' => ['Generate Engagement Report', 'Export Dashboard'],
        'archive' => ['Archive New Content', 'Export Archive'],
        'settings' => ['Create Setting', 'Sync Integration'],
    ];
@endphp

@section('title', $page['label'])
@section('topbar_context')
    Community Management > {{ $page['label'] }}
@endsection
@section('topbar_subtitle', $page['subtitle'])

@section('content')
    <div class="community-page">
        <section class="visitor-toolbar">
            <div class="visitor-heading">
                <span class="visitor-step">{{ $navItems[$pageKey]['number'] ?? 1 }}</span>
                <div>
                    <h2>{{ $page['title'] }}</h2>
                    <p>{{ $page['subtitle'] }}</p>
                </div>
            </div>

            <div class="top-dropdown community-create" data-dropdown>
                <button class="btn" type="button" data-dropdown-toggle aria-expanded="false">
                    + Create New <span class="dropdown-caret">v</span>
                </button>
                <div class="dropdown-menu">
                    @foreach ($createOptions[$pageKey] ?? $createOptions['announcements'] as $option)
                        <button type="button" data-modal-open="community-action-modal">{{ $option }}</button>
                    @endforeach
                </div>
            </div>
        </section>

        @if ($pageKey === 'announcements')
            <div class="community-workspace">
                <div class="community-main">
                    <section class="community-card-row" aria-label="Community metrics">
                        @foreach ($page['metrics'] as $metric)
                            <div class="community-metric {{ $metric['tone'] }}">
                                <span class="community-metric-icon">{{ $metric['icon'] }}</span>
                                <div>
                                    <span>{{ $metric['label'] }}</span>
                                    <strong>{{ $metric['value'] }}</strong>
                                    <span>{{ $metric['sub'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <div class="community-split">
                        <section class="visitor-panel">
                            <div class="visitor-panel-head">
                                <h3 class="visitor-panel-title">Latest Announcements</h3>
                                <a href="{{ route('community-management.announcements') }}">View All</a>
                            </div>
                            <div class="visitor-panel-body">
                                <div class="community-list">
                                    @foreach ($announcements as $announcement)
                                        <article class="community-row">
                                            <span class="community-tile-icon">{{ $announcement['icon'] }}</span>
                                            <div>
                                                <h3>{{ $announcement['title'] }} <span class="{{ $announcement['class'] }}" style="padding:3px 7px;border-radius:999px;font-size:10px;">{{ $announcement['status'] }}</span></h3>
                                                <p>{{ $announcement['body'] }}</p>
                                                <div class="community-meta">
                                                    <span>07 Jun 2026</span>
                                                    <span>All Residents</span>
                                                    <span>By Building Management</span>
                                                </div>
                                            </div>
                                            <div class="visitor-action-buttons">
                                                <button class="community-icon-btn" type="button" data-modal-open="community-action-modal">View</button>
                                                <span>{{ $announcement['views'] }}</span>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </section>

                        <section class="visitor-panel">
                            <div class="visitor-panel-head">
                                <h3 class="visitor-panel-title">Upcoming Events</h3>
                                <a href="{{ route('community-management.calendar') }}">View Calendar</a>
                            </div>
                            <div class="visitor-panel-body">
                                @foreach ($upcomingEvents as $event)
                                    <article class="community-event-row">
                                        <div class="community-date-tile"><strong>{{ $event['day'] }}</strong><span>{{ strtoupper($event['month']) }}</span></div>
                                        <div class="community-thumb" aria-hidden="true"></div>
                                        <div>
                                            <strong class="community-event-title">{{ $event['title'] }}</strong>
                                            <div class="community-meta">
                                                <span>{{ $event['time'] }}</span>
                                                <span>{{ $event['place'] }}</span>
                                                <span>{{ $event['registered'] }}</span>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    </div>

                    <div class="community-split">
                        <section class="visitor-panel">
                            <div class="visitor-panel-head"><h3 class="visitor-panel-title">Active Pollings</h3><a href="{{ route('community-management.polling-survey') }}">View All</a></div>
                            <div class="visitor-panel-body">
                                <strong>Fasilitas apa yang ingin ditambahkan?</strong>
                                @foreach ($pollingBars as $bar)
                                    <div class="service-progress-row" style="grid-template-columns:minmax(0,1fr) minmax(120px,1fr) 42px;">
                                        <span>{{ $bar['label'] }}</span>
                                        <div class="community-progress-line {{ $bar['tone'] }}"><span style="width: {{ $bar['value'] }}"></span></div>
                                        <strong>{{ $bar['value'] }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        <section class="visitor-panel">
                            <div class="visitor-panel-head"><h3 class="visitor-panel-title">Recent Forum Topics</h3><a href="{{ route('community-management.forum') }}">View All</a></div>
                            <div class="visitor-panel-body">
                                <div class="community-mini-list">
                                    @foreach ($forumTopics as $topic)
                                        <div class="community-mini-row">
                                            <span class="community-tile-icon" style="width:28px;height:28px;font-size:12px;">F</span>
                                            <div><strong>{{ $topic['title'] }}</strong><small class="muted">By {{ $topic['author'] }}</small></div>
                                            <span>{{ $topic['count'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>

                    <section class="visitor-panel">
                        <div class="visitor-panel-head"><h3 class="visitor-panel-title">Community Programs</h3><a href="{{ route('community-management.programs') }}">View All</a></div>
                        <div class="visitor-panel-body">
                            <div class="service-widget-grid">
                                @foreach ($programs as $program)
                                    <div class="service-widget">
                                        <h3>{{ $program['title'] }}</h3>
                                        <span class="status-approved" style="display:inline-block;padding:4px 8px;border-radius:999px;">Active</span>
                                        <p class="muted">{{ $program['participants'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>

                @include('community-management.partials.side-widgets')
            </div>
        @else
            <div class="community-workspace">
                <div class="community-main">
                    @if (! empty($page['metrics']))
                        <section class="community-card-row" aria-label="{{ $page['label'] }} metrics">
                            @foreach ($page['metrics'] as $metric)
                                <div class="community-metric {{ $metric['tone'] }}">
                                    <span class="community-metric-icon">{{ $metric['icon'] }}</span>
                                    <div>
                                        <span>{{ $metric['label'] }}</span>
                                        <strong>{{ $metric['value'] }}</strong>
                                        <span>{{ $metric['sub'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </section>
                    @endif

                    @if (in_array($pageKey, ['events', 'polling-survey', 'forum', 'broadcasts', 'programs'], true))
                        @php($table = $tables[$pageKey])
                        <section class="visitor-panel">
                            <div class="community-filter-bar">
                                <input type="search" placeholder="Search events, venues, organizers...">
                                @foreach ($table['filters'] as $filter)
                                    <select aria-label="{{ $filter }}">
                                        <option>{{ $filter }}</option>
                                        <option>All</option>
                                    </select>
                                @endforeach
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            @foreach ($table['columns'] as $column)
                                                <th>{{ $column }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($table['rows'] as $row)
                                            <tr>
                                                @foreach ($table['columns'] as $column)
                                                    @php($key = match ($column) {
                                                        'No.' => 'no',
                                                        'Date & Time', 'Tanggal' => 'date',
                                                        'Event Name', 'Judul Polling', 'Topik Diskusi', 'Judul Siaran', 'Nama Program' => 'name',
                                                        'Venue' => 'venue',
                                                        'Category', 'Kategori' => 'category',
                                                        'Status' => 'status',
                                                        'Participants', 'Peserta', 'Jumlah Peserta' => 'participants',
                                                        'Organizer', 'Pembuat', 'Pengirim', 'Penyelenggara' => 'organizer',
                                                        'Actions', 'Tindakan' => 'actions',
                                                        'Topik' => 'topic',
                                                        'Penulis' => 'author',
                                                        'Balasan' => 'reply',
                                                        'Dilihat' => 'views',
                                                        'Channel' => 'channel',
                                                        'Target Audience' => 'target',
                                                        default => strtolower($column),
                                                    })
                                                    <td>
                                                        @if ($key === 'name')
                                                            {!! nl2br(e($row['name'] ?? $row['title'])) !!}
                                                        @elseif ($key === 'status' || $key === 'category')
                                                            @if (is_array($row[$key] ?? null))
                                                                <span class="{{ $row[$key][1] }}" style="display:inline-block;padding:5px 9px;border-radius:999px;">{{ $row[$key][0] }}</span>
                                                            @else
                                                                {{ $row[$key] ?? '-' }}
                                                            @endif
                                                        @elseif ($key === 'participants')
                                                            @php($participant = $row['participants'] ?? ['-', '', 0])
                                                            <strong>{{ $participant[0] }}</strong>
                                                            @if (! empty($participant[1]))
                                                                <small class="muted">{{ $participant[1] }}</small>
                                                            @endif
                                                            <div class="community-progress-line"><span style="width: {{ $participant[2] ?? 0 }}%"></span></div>
                                                        @elseif ($key === 'actions')
                                                            <div class="community-action-icons">
                                                                <button class="community-icon-btn" type="button" data-modal-open="community-action-modal">View</button>
                                                                <button class="community-icon-btn gold" type="button" data-modal-open="community-action-modal">Edit</button>
                                                                <button class="community-icon-btn green" type="button" data-modal-open="community-action-modal">Pin</button>
                                                            </div>
                                                        @elseif ($key === 'organizer')
                                                            {{ $row['organizer'] ?? $row['creator'] ?? $row['sender'] ?? '-' }}
                                                        @else
                                                            {!! nl2br(e($row[$key] ?? '-')) !!}
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @elseif ($pageKey === 'calendar')
                        <section class="visitor-panel">
                            <div class="visitor-panel-head">
                                <div class="visitor-tabs">
                                    <span class="visitor-tab active">Month</span>
                                    <span class="visitor-tab">Week</span>
                                    <span class="visitor-tab">Day</span>
                                    <span class="visitor-tab">List</span>
                                </div>
                                <div class="visitor-table-filters">
                                    <select><option>Tower A</option></select>
                                    <select><option>Category</option></select>
                                    <input type="search" placeholder="Search events...">
                                </div>
                            </div>
                            <div class="visitor-panel-body">
                                <h3 style="margin:0;font-size:26px;">June 2026</h3>
                                <div class="table-wrap">
                                    <div class="community-calendar-large">
                                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                            <div class="community-calendar-head">{{ $day }}</div>
                                        @endforeach
                                        @foreach (range(1, 35) as $cell)
                                            @php($day = $cell === 1 ? '31' : ($cell - 1 <= 30 ? (string) ($cell - 1) : (string) ($cell - 31)))
                                            <div>
                                                <strong>{{ $day }}</strong>
                                                @if (in_array($day, ['1', '7', '10', '11', '15', '17', '25', '26'], true))
                                                    <span @class(['community-event-block', 'blue' => in_array($day, ['10', '17'], true), 'gold' => in_array($day, ['11'], true), 'red' => in_array($day, ['25'], true)])>
                                                        {{ match ($day) {
                                                            '1' => 'First Aid Workshop',
                                                            '7' => 'Community Gardening',
                                                            '10' => 'Morning Yoga Class',
                                                            '11' => 'Kids Drawing Workshop',
                                                            '15' => "Senior's Fitness Session",
                                                            '17' => 'Apartment BBQ Night',
                                                            '25' => 'Committee Meeting',
                                                            default => 'Waste Management Program',
                                                        } }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>
                    @elseif ($pageKey === 'engagement')
                        <div class="community-split">
                            <section class="visitor-panel">
                                <div class="visitor-panel-head"><h3 class="visitor-panel-title">Engagement Overview</h3></div>
                                <div class="visitor-panel-body">
                                    <div class="community-donut-panel">
                                        <div class="community-donut" data-value="68%\A Score"></div>
                                        <div class="community-legend">
                                            <span><span>Monthly Active Users</span><strong>2,100</strong></span>
                                            <span><span>Avg. Event Attendance Rate</span><strong>75%</strong></span>
                                            <span><span>Forum Interaction Rate</span><strong>40%</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section class="visitor-panel">
                                <div class="visitor-panel-head"><h3 class="visitor-panel-title">Growth Signals</h3></div>
                                <div class="visitor-panel-body">
                                    <div class="service-widget-grid">
                                        <div class="service-widget"><h3>New Member Growth</h3><strong>+15%</strong><div class="community-chart-line" style="min-height:90px;"></div></div>
                                        <div class="service-widget"><h3>Community Post Volume</h3><strong>+25%</strong><div class="community-chart-line" style="min-height:90px;"></div></div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="community-split">
                            <section class="visitor-panel">
                                <div class="visitor-panel-head"><h3 class="visitor-panel-title">Overall Engagement Score over 6 Months</h3></div>
                                <div class="visitor-panel-body"><div class="community-chart-line"></div></div>
                            </section>
                            <section class="visitor-panel">
                                <div class="visitor-panel-head"><h3 class="visitor-panel-title">Engagement Score vs Activity Type</h3></div>
                                <div class="visitor-panel-body">
                                    <div class="community-bar-chart">
                                        @foreach ([78, 48, 56, 82, 44, 64] as $height)
                                            <span class="community-bar {{ $height > 70 ? 'hot' : ($height > 55 ? 'green' : 'gold') }}" style="height: {{ $height }}%;"></span>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        </div>
                        <section class="visitor-panel">
                            <div class="visitor-panel-head"><h3 class="visitor-panel-title">Top Event Performance</h3></div>
                            <div class="table-wrap">
                                <table>
                                    <thead><tr><th>Event</th><th>Date</th><th>Participants/Capacity</th><th>Post-Event Satisfaction Rating</th></tr></thead>
                                    <tbody>
                                        <tr><td>Apartment BBQ Night</td><td>31 Jun 2026</td><td>100 - 1200</td><td>high</td></tr>
                                        <tr><td>Waste Management Program</td><td>07 Jun 2026</td><td>500 - 300</td><td>good</td></tr>
                                        <tr><td>Morning Yoga Class</td><td>07 Jun 2026</td><td>7:00 - 12:00</td><td>good</td></tr>
                                        <tr><td>Senior's Fitness Session</td><td>09 Jun 2026</td><td>15:00 - 07:00</td><td>high</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @elseif (in_array($pageKey, ['archive', 'settings'], true))
                        @php($table = $tables[$pageKey])
                        <section class="visitor-panel">
                            <div class="visitor-panel-head"><h3 class="visitor-panel-title">{{ $pageKey === 'archive' ? 'Archive Content Creation History' : 'Status Aktivitas Fitur Komunitas' }}</h3></div>
                            <div class="visitor-panel-body"><div class="community-chart-line"></div></div>
                        </section>
                        <section class="visitor-panel">
                            <div class="community-filter-bar" style="grid-template-columns: minmax(220px, 1.4fr) repeat(3, minmax(150px, 1fr));">
                                @foreach ($table['filters'] as $filter)
                                    @if ($loop->first)
                                        <input type="search" placeholder="{{ $filter }}">
                                    @else
                                        <select><option>{{ $filter }}</option></select>
                                    @endif
                                @endforeach
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <thead><tr>@foreach ($table['columns'] as $column)<th>{{ $column }}</th>@endforeach</tr></thead>
                                    <tbody>
                                        @foreach ($table['rows'] as $row)
                                            <tr>
                                                @if ($pageKey === 'archive')
                                                    <td><span class="community-tile-icon" style="width:30px;height:30px;font-size:12px;">{{ $row['icon'] }}</span></td>
                                                    <td>{{ $row['type'] }}</td>
                                                    <td>{{ $row['title'] }}</td>
                                                    <td>{{ $row['date'] }}</td>
                                                    <td>{{ $row['author'] }}</td>
                                                    <td>{{ $row['category'] }}</td>
                                                    <td><button class="btn compact secondary" type="button" data-modal-open="community-action-modal">View</button></td>
                                                @else
                                                    <td><span class="community-tile-icon" style="width:30px;height:30px;font-size:12px;">{{ $row['icon'] }}</span></td>
                                                    <td>{{ $row['name'] }}</td>
                                                    <td>{{ $row['description'] }}</td>
                                                    <td><span @class(['community-setting-toggle', 'off' => $row['status'] === 'off'])></span></td>
                                                    <td><button class="btn compact secondary" type="button" data-modal-open="community-action-modal">{{ $row['status'] === 'config' ? 'Konfigurasi' : 'Edit' }}</button></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif
                </div>

                @include('community-management.partials.side-widgets')
            </div>
        @endif

        @include('community-management.partials.action-modal')
    </div>
@endsection

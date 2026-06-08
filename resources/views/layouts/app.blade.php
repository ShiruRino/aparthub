<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Aparthub</title>
    <style>
        :root {
            --navy-950: #020b1e;
            --navy-900: #061636;
            --navy-850: #092049;
            --navy-800: #0c2b5d;
            --navy-700: #153d78;
            --gold: #dda544;
            --gold-2: #f4c15d;
            --blue: #1f7bef;
            --green: #1db56b;
            --red: #e04c4c;
            --page: #f3f6fb;
            --surface: #ffffff;
            --line: #dce4ef;
            --text: #071935;
            --muted: #67758a;
            --dark-card: linear-gradient(145deg, #061936 0%, #0a2a59 100%);
            --shadow: 0 14px 32px rgba(5, 18, 42, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--page);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 13px;
            line-height: 1.45;
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }
        svg { display: block; }

        .ops-shell {
            display: grid;
            grid-template-columns: 286px minmax(0, 1fr);
            min-height: 100vh;
            transition: grid-template-columns 180ms ease;
        }

        .ops-sidebar {
            position: sticky;
            top: 0;
            z-index: 30;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            background:
                radial-gradient(circle at 12% 0%, rgba(238, 182, 72, 0.18), transparent 30%),
                linear-gradient(180deg, #061531 0%, #020b1e 100%);
            color: #f4f8ff;
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 92px;
            padding: 22px 22px 18px;
        }

        .brand-tower {
            flex: 0 0 48px;
            width: 48px;
            height: 52px;
            color: var(--gold-2);
        }

        .brand-name {
            display: block;
            font-size: 24px;
            font-weight: 820;
            letter-spacing: 1px;
            line-height: 1;
        }

        .brand-sub {
            display: block;
            margin-top: 4px;
            color: #d9e5f6;
            font-size: 11px;
            font-weight: 760;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .sidebar-scroll {
            min-height: 0;
            overflow-y: auto;
            padding: 0 14px 18px;
        }

        .side-nav {
            display: grid;
            gap: 7px;
        }

        .side-section { border-radius: 8px; }

        .side-link,
        .side-parent {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            min-height: 47px;
            padding: 11px 12px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #eef5ff;
            cursor: pointer;
            font-size: 14px;
            font-weight: 740;
            text-align: left;
        }

        .side-link:hover,
        .side-parent:hover,
        .side-link.active,
        .side-parent.active {
            background: linear-gradient(90deg, rgba(221, 165, 68, 0.68), rgba(221, 165, 68, 0.28));
            box-shadow: inset 0 0 0 1px rgba(244, 193, 93, 0.14);
        }

        .side-link:focus-visible,
        .side-parent:focus-visible,
        .menu-button:focus-visible,
        .top-chip:focus-visible,
        .top-profile:focus-visible,
        .btn:focus-visible {
            outline: 3px solid rgba(244, 193, 93, 0.45);
            outline-offset: 2px;
        }

        .side-icon {
            display: grid;
            flex: 0 0 22px;
            width: 22px;
            height: 22px;
            place-items: center;
            color: #ffffff;
        }

        .side-caret {
            margin-left: auto;
            color: #cbd9ee;
            font-size: 12px;
            transition: transform 160ms ease;
        }

        .side-section.is-open .side-caret { transform: rotate(180deg); }

        .side-sub {
            display: grid;
            gap: 2px;
            max-height: 420px;
            margin: 1px 0 9px 27px;
            overflow: hidden;
            padding-left: 16px;
            border-left: 1px solid rgba(216, 229, 246, 0.24);
            opacity: 1;
            transition: max-height 180ms ease, margin 180ms ease, opacity 160ms ease;
        }

        .side-section:not(.is-open) .side-sub {
            max-height: 0;
            margin-bottom: 0;
            opacity: 0;
            pointer-events: none;
        }

        .side-sub a {
            min-height: 31px;
            padding: 7px 8px;
            border-radius: 6px;
            color: #cbd9ee;
            font-size: 13px;
            font-weight: 560;
        }

        .side-sub a:hover,
        .side-sub a.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-profile {
            margin: auto 14px 0;
            padding: 17px 8px 18px;
            border-top: 1px solid rgba(216, 229, 246, 0.12);
        }

        .profile-row {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .avatar {
            display: grid;
            flex: 0 0 39px;
            width: 39px;
            height: 39px;
            place-items: center;
            border-radius: 50%;
            background: #f7f9fd;
            color: #0a1d3f;
            font-weight: 900;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .profile-name { color: #ffffff; font-weight: 760; }
        .profile-site { color: #b9c9df; font-size: 12px; }

        .online-dot {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #65d98b;
            font-size: 12px;
        }

        .online-dot::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #52d273;
        }

        .ops-main { min-width: 0; }

        .ops-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 20px;
            min-height: 92px;
            padding: 15px 26px;
            background: linear-gradient(90deg, rgba(5, 16, 39, 0.98), rgba(9, 37, 83, 0.98)), var(--navy-900);
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(3, 13, 31, 0.22);
        }

        .menu-button {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #ffffff;
            cursor: pointer;
        }

        .menu-button:hover { background: rgba(255, 255, 255, 0.1); }

        .top-title { min-width: 0; }

        .top-context {
            margin: 0 0 2px;
            color: var(--gold-2);
            font-size: 14px;
            font-weight: 860;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .top-title h1 {
            margin: 0;
            color: #ffffff;
            font-size: 25px;
            font-weight: 860;
            line-height: 1.14;
            text-transform: uppercase;
        }

        .top-title p {
            margin: 4px 0 0;
            color: #dbe8f8;
            font-size: 15px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .top-dropdown { position: relative; }

        .top-chip,
        .top-profile {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 46px;
            padding: 10px 14px;
            border: 1px solid rgba(219, 232, 248, 0.34);
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.04);
            color: #ffffff;
            cursor: pointer;
            font-weight: 720;
            white-space: nowrap;
        }

        .top-profile {
            min-width: 176px;
            padding: 6px 10px;
            text-align: left;
        }

        .top-chip:hover,
        .top-profile:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .dropdown-caret { transition: transform 160ms ease; }
        .top-dropdown.is-open .dropdown-caret { transform: rotate(180deg); }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 40;
            display: grid;
            min-width: 238px;
            padding: 8px;
            border: 1px solid rgba(219, 232, 248, 0.22);
            border-radius: 8px;
            background: #ffffff;
            color: var(--text);
            box-shadow: 0 18px 44px rgba(2, 11, 30, 0.25);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-4px);
            transition: opacity 160ms ease, transform 160ms ease;
        }

        .top-dropdown.is-open .dropdown-menu {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            min-height: 38px;
            padding: 9px 10px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #122849;
            cursor: pointer;
            font-size: 13px;
            font-weight: 720;
            text-align: left;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover { background: #edf3fb; }

        .dropdown-note {
            padding: 8px 10px 10px;
            color: var(--muted);
            font-size: 12px;
        }

        .bell {
            position: relative;
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            color: #ffffff;
        }

        .bell-count {
            position: absolute;
            top: 2px;
            right: 2px;
            display: grid;
            width: 20px;
            height: 20px;
            place-items: center;
            border-radius: 50%;
            background: #e43838;
            color: #ffffff;
            font-size: 11px;
            font-weight: 820;
        }

        .top-profile .avatar {
            width: 40px;
            height: 40px;
            flex-basis: 40px;
        }

        .welcome { color: #d8e5f6; font-size: 12px; }
        .role-title { color: #ffffff; font-weight: 780; line-height: 1.2; }

        .content {
            width: min(100%, 1720px);
            padding: 20px 18px 28px;
        }

        .dashboard-content { padding: 18px 16px 28px; }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 25;
            display: none;
            background: rgba(2, 11, 30, 0.62);
            backdrop-filter: blur(2px);
        }

        .alert {
            margin-bottom: 14px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: var(--shadow);
        }

        .alert.success { border-color: #bee8cf; color: #0f7d45; }
        .alert.error { border-color: #ffc1bd; color: #b42318; }

        .dash-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px;
        }

        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }
        .span-4 { grid-column: span 4; }
        .span-5 { grid-column: span 5; }
        .span-6 { grid-column: span 6; }
        .span-8 { grid-column: span 8; }
        .span-9 { grid-column: span 9; }
        .span-12 { grid-column: span 12; }

        .stat-card {
            grid-column: span 2;
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            align-items: center;
            min-height: 128px;
            padding: 18px;
            border-radius: 8px;
            background: var(--dark-card);
            color: #ffffff;
            box-shadow: var(--shadow);
        }

        .stat-icon {
            display: grid;
            width: 58px;
            height: 58px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            color: var(--gold-2);
        }

        .stat-label { color: #f1f6ff; font-size: 12px; font-weight: 650; }
        .stat-value { margin-top: 8px; color: #ffffff; font-size: 26px; font-weight: 850; line-height: 1; }
        .stat-sub { margin-top: 7px; color: #d9e6f7; font-size: 12px; }

        .trend-up,
        .trend-down,
        .trend-warn {
            margin-top: 7px;
            font-size: 12px;
            font-weight: 760;
        }

        .trend-up { color: #54dd84; }
        .trend-down { color: #ff8b62; }
        .trend-warn { color: var(--gold-2); }

        .ops-panel,
        .panel {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(5, 18, 42, 0.07);
        }

        .panel-pad { padding: 22px; }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 47px;
            padding: 12px 16px;
            border-bottom: 1px solid #e3eaf3;
        }

        .panel-title {
            margin: 0;
            color: #0b2149;
            font-size: 14px;
            font-weight: 850;
            text-transform: uppercase;
        }

        .panel-body { padding: 14px 16px; }

        .building-overview {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 118px;
            gap: 14px;
        }

        .building-scene,
        .map-scene {
            position: relative;
            min-height: 238px;
            overflow: hidden;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(2, 10, 26, 0.05), rgba(2, 10, 26, 0.8)),
                linear-gradient(135deg, #9eb7d0 0%, #253f62 42%, #08172f 100%);
        }

        .building-scene::before,
        .building-scene::after,
        .map-scene::before {
            content: "";
            position: absolute;
            bottom: 48px;
            width: 74px;
            background:
                repeating-linear-gradient(90deg, transparent 0 12px, rgba(244, 193, 93, 0.75) 12px 16px),
                repeating-linear-gradient(0deg, #0d223e 0 16px, #183655 16px 18px);
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.34);
        }

        .building-scene::before { left: 44px; height: 158px; transform: skewY(-4deg); }
        .building-scene::after { left: 150px; width: 88px; height: 188px; transform: skewY(3deg); }

        .tower-third {
            position: absolute;
            right: 64px;
            bottom: 49px;
            width: 76px;
            height: 134px;
            background:
                repeating-linear-gradient(90deg, transparent 0 11px, rgba(244, 193, 93, 0.68) 11px 15px),
                repeating-linear-gradient(0deg, #0f2747 0 15px, #1e4165 15px 17px);
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.34);
        }

        .podium {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 10px 14px;
            background: linear-gradient(180deg, transparent, rgba(3, 15, 34, 0.9) 24%);
        }

        .tower-tab {
            min-height: 48px;
            padding: 9px 10px;
            border-radius: 7px;
            background: rgba(7, 27, 59, 0.86);
            color: #ffffff;
            text-align: center;
        }

        .tower-tab.active { background: linear-gradient(135deg, #e3a843, #b87924); }
        .tower-tab strong { display: block; font-size: 12px; }
        .tower-tab span { display: block; margin-top: 2px; font-size: 11px; opacity: 0.92; }

        .overview-list { display: grid; gap: 12px; align-content: center; }
        .overview-item { display: grid; grid-template-columns: 26px minmax(0, 1fr); gap: 9px; align-items: center; }
        .overview-item span { color: #465a73; font-size: 11px; font-weight: 720; }
        .overview-item strong { display: block; color: #0a2148; font-size: 17px; }

        .chart { position: relative; height: 238px; padding: 18px 10px 18px 38px; }
        .chart-grid { position: absolute; inset: 20px 16px 34px 38px; background: repeating-linear-gradient(0deg, #ffffff 0 42px, #dce5ef 43px 44px); }
        .bars { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(6, 1fr); align-items: end; gap: 18px; height: 170px; }
        .bar-pair { display: flex; align-items: end; justify-content: center; gap: 6px; height: 100%; }
        .bar { width: 9px; border-radius: 999px 999px 0 0; background: var(--blue); }
        .bar.target { background: var(--gold-2); }
        .chart-labels { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(6, 1fr); gap: 18px; margin-top: 8px; color: #53647c; font-size: 11px; text-align: center; }

        .mini-list { display: grid; gap: 0; }
        .mini-row, .activity-row, .alert-row { display: grid; align-items: center; min-height: 45px; border-bottom: 1px solid #e8eef6; }
        .mini-row { grid-template-columns: minmax(0, 1fr) auto 40px; gap: 12px; }
        .mini-row:last-child, .activity-row:last-child, .alert-row:last-child { border-bottom: 0; }
        .mini-label { color: #1c3356; font-size: 12px; font-weight: 720; }
        .mini-value { color: #061b3f; font-size: 17px; font-weight: 840; }

        .spark {
            width: 34px;
            height: 20px;
            border-radius: 4px;
            background:
                linear-gradient(135deg, transparent 12%, rgba(31, 123, 239, 0.16) 12% 40%, transparent 40%),
                linear-gradient(45deg, transparent 18%, currentColor 18% 23%, transparent 23% 45%, currentColor 45% 50%, transparent 50% 72%, currentColor 72% 77%, transparent 77%);
            color: var(--blue);
        }

        .spark.red { color: var(--red); }
        .spark.green { color: var(--green); }

        .donut-row { display: grid; grid-template-columns: 120px minmax(0, 1fr); gap: 16px; align-items: center; min-height: 146px; }
        .donut { display: grid; width: 112px; height: 112px; place-items: center; border-radius: 50%; background: conic-gradient(var(--green) 0 62%, var(--gold-2) 62% 80%, var(--red) 80% 100%); }
        .donut::after { content: ""; width: 58px; height: 58px; border-radius: 50%; background: #ffffff; }
        .legend { display: grid; gap: 9px; }
        .legend-row { display: grid; grid-template-columns: 12px minmax(0, 1fr) auto; gap: 8px; align-items: center; color: #1f3556; font-size: 12px; font-weight: 650; }

        .dot { width: 9px; height: 9px; border-radius: 2px; background: var(--blue); }
        .dot.green { background: var(--green); }
        .dot.gold { background: var(--gold-2); }
        .dot.red { background: var(--red); }

        .progress-list { display: grid; gap: 14px; padding: 4px 0; }
        .progress-row { display: grid; grid-template-columns: 92px minmax(0, 1fr) 34px; align-items: center; gap: 10px; color: #1a3154; font-size: 12px; font-weight: 700; }
        .progress-track { height: 11px; overflow: hidden; border: 1px solid #d6e0ed; border-radius: 999px; background: #eff4fa; }
        .progress-fill { height: 100%; border-radius: inherit; background: var(--blue); }

        .metric-list { display: grid; gap: 10px; }
        .metric-row { display: grid; grid-template-columns: 25px minmax(0, 1fr) auto; gap: 10px; align-items: center; color: #1a3154; font-size: 12px; font-weight: 700; }
        .metric-row strong { color: #061b3f; font-size: 18px; }

        .alert-row { grid-template-columns: 28px minmax(0, 1fr) auto; gap: 10px; min-height: 58px; }
        .alert-title { font-size: 12px; font-weight: 820; }
        .alert-desc { margin-top: 2px; color: #42556f; font-size: 11px; }
        .alert-time { color: #52647d; font-size: 11px; }

        .dark-section { border: 0; background: var(--dark-card); color: #ffffff; }
        .dark-section .panel-head { border-bottom-color: rgba(255, 255, 255, 0.12); }
        .dark-section .panel-title, .dark-section .metric-row, .dark-section .metric-row strong { color: #ffffff; }
        .dark-section .badge { color: #ffffff; background: rgba(255, 255, 255, 0.12); }

        .monitor-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 9px; }
        .monitor-card { min-height: 130px; padding: 14px; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 8px; background: rgba(255, 255, 255, 0.05); }
        .monitor-icon { color: var(--gold-2); font-size: 24px; font-weight: 850; }
        .monitor-card span { display: block; margin-top: 12px; color: #d8e5f7; font-size: 11px; font-weight: 710; }
        .monitor-card strong { display: block; margin-top: 8px; font-size: 19px; }
        .monitor-card small { display: block; margin-top: 10px; color: #63df8a; font-size: 11px; }

        .map-scene { min-height: 196px; background: linear-gradient(180deg, rgba(2, 10, 26, 0.04), rgba(2, 10, 26, 0.78)), linear-gradient(135deg, #213f63 0%, #0b1f3e 50%, #061225 100%); }
        .map-scene::before { right: 44%; bottom: 38px; height: 128px; }
        .road { position: absolute; right: -20px; bottom: 20px; left: -20px; height: 50px; background: linear-gradient(12deg, transparent 0 28%, rgba(255, 255, 255, 0.18) 28% 33%, #102642 33% 66%, rgba(255, 255, 255, 0.18) 66% 70%, transparent 70%); }
        .pin { position: absolute; display: grid; width: 18px; height: 18px; place-items: center; border: 2px solid #ffffff; border-radius: 50% 50% 50% 0; background: #25bf73; transform: rotate(-45deg); }
        .pin::after { content: ""; width: 5px; height: 5px; border-radius: 50%; background: #ffffff; }
        .pin:nth-child(2) { left: 52%; top: 28%; }
        .pin:nth-child(3) { left: 64%; top: 46%; }
        .pin:nth-child(4) { left: 77%; top: 36%; }
        .pin:nth-child(5) { left: 57%; top: 64%; background: var(--blue); }

        .quick-summary { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); }
        .summary-cell { min-height: 96px; padding: 16px; border-right: 1px solid rgba(255, 255, 255, 0.11); }
        .summary-cell:last-child { border-right: 0; }
        .summary-cell span { display: block; color: #d8e5f7; font-size: 11px; font-weight: 760; }
        .summary-cell strong { display: block; margin-top: 8px; color: #ffffff; font-size: 20px; }
        .stars { margin-top: 8px; color: var(--gold-2); font-size: 17px; letter-spacing: 2px; }
        .tiny-donut { width: 60px; height: 60px; margin-top: 4px; border-radius: 50%; background: conic-gradient(var(--gold-2) 0 84%, #17375d 84% 100%); }

        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 8px 12px;
            border: 1px solid transparent;
            border-radius: 7px;
            background: var(--navy-800);
            color: #ffffff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 760;
            line-height: 1;
            white-space: nowrap;
        }

        .btn:hover { background: var(--navy-700); }
        .btn.secondary { border-color: #cbd6e4; background: #ffffff; color: var(--navy-900); }
        .btn.secondary:hover { background: #eef4fb; }
        .btn.danger { background: #c73b33; }
        .btn.danger:hover { background: #a72c25; }
        .btn.gold { background: linear-gradient(135deg, #e3a843, #b87924); }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 14px; border-bottom: 1px solid #e4ebf4; text-align: left; vertical-align: middle; }
        th { color: #465a73; background: #edf3fb; font-size: 12px; font-weight: 850; text-transform: uppercase; }
        tr:last-child td { border-bottom: 0; }
        .table-wrap { overflow-x: auto; }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }

        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .field { display: grid; gap: 7px; }
        .field.full { grid-column: 1 / -1; }
        label { color: #54647b; font-size: 12px; font-weight: 850; text-transform: uppercase; }
        .row { display: flex; align-items: center; gap: 8px; color: #54647b; font-size: 14px; font-weight: 650; text-transform: none; }

        input, select, textarea {
            width: 100%;
            min-height: 40px;
            border: 1px solid #cbd6e4;
            border-radius: 7px;
            background: #ffffff;
            color: var(--text);
            padding: 9px 11px;
            outline: none;
        }

        textarea { min-height: 96px; resize: vertical; }
        input:focus, select:focus, textarea:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(31, 123, 239, 0.13); }
        input[type="checkbox"] { width: 18px; height: 18px; min-height: 18px; accent-color: var(--blue); }
        input[disabled], select[disabled], textarea[disabled] { background: #edf2f8; color: #718095; cursor: not-allowed; }

        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }

        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #eef3fa;
            color: #53647d;
            font-size: 12px;
            font-weight: 820;
        }

        .badge.green { background: #e6f8ee; color: #0f7d45; }
        .badge.yellow { background: #fff5df; color: #8b5d09; }
        .muted { color: var(--muted); }
        .empty { padding: 28px; color: var(--muted); text-align: center; }
        .pagination { padding: 14px; }
        .pagination nav > div:first-child { display: none; }
        .access-card { margin-bottom: 18px; }
        .access-card header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; border-bottom: 1px solid #e4ebf4; background: #ffffff; }
        .access-table td, .access-table th { text-align: center; }
        .access-table td:first-child, .access-table th:first-child { text-align: left; }
        .error-text { color: #b42318; font-size: 12px; font-weight: 720; }

        .resident-content { padding: 18px 14px 30px; }
        .resident-page { display: grid; gap: 12px; }

        .resident-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px;
            align-items: start;
        }

        .res-span-3 { grid-column: span 3; }
        .res-span-4 { grid-column: span 4; }
        .res-span-5 { grid-column: span 5; }
        .res-span-6 { grid-column: span 6; }
        .res-span-7 { grid-column: span 7; }
        .res-span-8 { grid-column: span 8; }
        .res-span-12 { grid-column: span 12; }

        .resident-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(5, 18, 42, 0.07);
        }

        .resident-card.dark {
            border: 0;
            background: var(--dark-card);
            color: #ffffff;
        }

        .resident-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 46px;
            padding: 12px 14px;
            border-bottom: 1px solid #e3eaf3;
        }

        .resident-card.dark .resident-card-head { border-bottom-color: rgba(255, 255, 255, 0.12); }

        .resident-card-title {
            margin: 0;
            color: #0b2149;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .resident-card.dark .resident-card-title { color: #ffffff; }

        .resident-card-body {
            display: grid;
            gap: 12px;
            padding: 14px;
        }

        .resident-form { display: grid; gap: 10px; }

        .resident-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .resident-stat {
            min-height: 72px;
            padding: 12px;
            border: 1px solid #e3eaf3;
            border-radius: 7px;
            background: #f8fbff;
        }

        .resident-stat span {
            display: block;
            color: #607087;
            font-size: 11px;
            font-weight: 760;
        }

        .resident-stat strong {
            display: block;
            margin-top: 6px;
            color: #071935;
            font-size: 19px;
            font-weight: 900;
        }

        .resident-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            min-height: 44px;
            border-bottom: 1px solid #e8eef6;
        }

        .resident-row:last-child { border-bottom: 0; }
        .resident-row strong { color: #10264a; font-size: 13px; }
        .resident-row small { display: block; margin-top: 2px; color: #687990; font-size: 11px; }

        .status-line {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1d355b;
            font-size: 12px;
            font-weight: 760;
        }

        .status-line::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--green);
        }

        .status-line.warn::before { background: var(--gold-2); }
        .status-line.danger::before { background: var(--red); }

        .resident-timeline {
            display: grid;
            gap: 0;
            border-left: 2px solid #dbe5f1;
            margin-left: 9px;
            padding-left: 16px;
        }

        .timeline-item {
            position: relative;
            padding: 0 0 16px;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            top: 3px;
            left: -22px;
            width: 10px;
            height: 10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            background: var(--gold);
            box-shadow: 0 0 0 2px #dbe5f1;
        }

        .timeline-item strong { display: block; color: #10264a; font-size: 12px; }
        .timeline-item span { display: block; margin-top: 3px; color: #607087; font-size: 11px; }

        .resident-benefits {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0;
        }

        .benefit-cell {
            min-height: 96px;
            padding: 18px;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
        }

        .benefit-cell:last-child { border-right: 0; }
        .benefit-cell strong { display: block; color: #ffffff; font-size: 13px; text-transform: uppercase; }
        .benefit-cell span { display: block; margin-top: 8px; color: #d8e5f7; font-size: 12px; }

        .resident-visual {
            position: relative;
            min-height: 210px;
            overflow: hidden;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(2, 10, 26, 0.04), rgba(2, 10, 26, 0.82)),
                linear-gradient(135deg, #b7c8da 0%, #36587d 44%, #08172f 100%);
        }

        .resident-visual::before,
        .resident-visual::after {
            content: "";
            position: absolute;
            bottom: 42px;
            width: 74px;
            background:
                repeating-linear-gradient(90deg, transparent 0 12px, rgba(244, 193, 93, 0.76) 12px 15px),
                repeating-linear-gradient(0deg, #0e2442 0 15px, #24496f 15px 17px);
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.34);
        }

        .resident-visual::before { left: 34px; height: 146px; transform: skewY(-4deg); }
        .resident-visual::after { left: 132px; height: 176px; transform: skewY(3deg); }

        .resident-visual-label {
            position: absolute;
            right: 14px;
            bottom: 14px;
            left: 14px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            color: #ffffff;
            font-weight: 800;
        }

        .resident-list-page {
            display: grid;
            gap: 14px;
        }

        .resident-page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 2px 0 4px;
        }

        .resident-page-head h2 {
            margin: 0;
            color: #050d1e;
            font-size: 24px;
            font-weight: 940;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .resident-filter-panel,
        .resident-table-panel {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(5, 18, 42, 0.06);
        }

        .resident-filter-panel {
            display: grid;
            grid-template-columns: 1.2fr repeat(4, minmax(140px, 1fr));
            gap: 12px;
            align-items: end;
            padding: 16px;
        }

        .resident-filter-field {
            display: grid;
            gap: 7px;
        }

        .resident-filter-field label {
            color: #0f203e;
            font-size: 12px;
            font-weight: 860;
            text-transform: none;
        }

        .resident-search {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 44px;
            overflow: hidden;
            border: 1px solid #cbd6e4;
            border-radius: 7px;
            background: #ffffff;
        }

        .resident-search input {
            min-height: 40px;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .resident-search button {
            display: grid;
            place-items: center;
            border: 0;
            border-left: 1px solid #dce4ef;
            background: #f8fbff;
            color: #061936;
            cursor: pointer;
        }

        .resident-table-panel .table-wrap {
            border-top: 1px solid #eef2f7;
        }

        .resident-table-panel table {
            min-width: 900px;
        }

        .resident-table-panel th {
            color: #111827;
            background: #f4f5f7;
            font-size: 13px;
            text-transform: none;
        }

        .resident-table-panel td {
            color: #111827;
            font-size: 14px;
            font-weight: 650;
        }

        .resident-check {
            width: 18px;
            height: 18px;
            min-height: 18px;
            margin: 0;
        }

        .resident-avatar,
        .resident-unit-thumb {
            display: grid;
            width: 54px;
            height: 54px;
            place-items: end center;
            overflow: hidden;
            border: 1px solid #dce4ef;
            border-radius: 6px;
            background:
                radial-gradient(circle at 50% 25%, rgba(255, 255, 255, 0.9) 0 12%, transparent 13%),
                linear-gradient(140deg, #253f62, #d7e3ef);
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
        }

        .resident-avatar.female { background: radial-gradient(circle at 50% 24%, rgba(255, 255, 255, 0.92) 0 12%, transparent 13%), linear-gradient(140deg, #6c2d5a, #e7cad8); }
        .resident-avatar.pending { background: radial-gradient(circle at 50% 24%, rgba(255, 255, 255, 0.92) 0 12%, transparent 13%), linear-gradient(140deg, #174568, #a8d8ee); }
        .resident-avatar.out { background: radial-gradient(circle at 50% 24%, rgba(255, 255, 255, 0.92) 0 12%, transparent 13%), linear-gradient(140deg, #6a4d34, #eed8bf); }

        .resident-unit-thumb {
            width: 82px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.16), rgba(10, 29, 63, 0.18)),
                repeating-linear-gradient(90deg, #d6c0a5 0 14px, #f2eadf 14px 28px);
        }

        .resident-unit-thumb.empty { background: linear-gradient(135deg, #e7edf5, #f9fbfd); color: #6b7a8f; }
        .resident-unit-thumb.repair { background: linear-gradient(135deg, #d6c0a5, #f4e9db 42%, #4b382b 43% 55%, #f4e9db 56%); }

        .resident-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 5px 11px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 780;
            line-height: 1.1;
            text-align: center;
        }

        .resident-status.active { border-color: #6bc78d; background: #bff2d0; color: #086032; }
        .resident-status.pending { border-color: #e8c34a; background: #ffec91; color: #6b4a00; }
        .resident-status.out,
        .resident-status.repair { border-color: #ed9393; background: #ffb6b6; color: #8e1717; }
        .resident-status.empty { border-color: #e8c34a; background: #fff1a8; color: #765700; }
        .resident-status.done { border-color: #78d69a; background: #bff2d0; color: #0c6538; }
        .resident-status.process { border-color: #79b8e7; background: #bde0ff; color: #0b4f86; }

        .resident-action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            align-items: center;
        }

        .resident-action-btn {
            display: grid;
            gap: 3px;
            min-width: 38px;
            padding: 3px 4px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #071935;
            cursor: pointer;
            font-size: 10px;
            font-weight: 760;
            text-align: center;
        }

        .resident-action-btn svg {
            justify-self: center;
            width: 20px;
            height: 20px;
        }

        .resident-action-btn:hover {
            background: #edf3fb;
            color: var(--navy-800);
        }

        .resident-action-btn.success { color: #0f7d45; }
        .resident-action-btn.danger { color: #b42318; }

        .resident-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            color: #111827;
            font-size: 13px;
            font-weight: 650;
        }

        .resident-page-btn {
            display: grid;
            min-width: 30px;
            height: 30px;
            place-items: center;
            border: 1px solid #dce4ef;
            border-radius: 6px;
            background: #ffffff;
            color: #061936;
            font-weight: 840;
        }

        .resident-page-btn.active {
            border-color: #061936;
            background: #061936;
            color: #ffffff;
        }

        .resident-benefit-bar {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            overflow: hidden;
            border-radius: 8px;
            background: var(--dark-card);
            color: #ffffff;
            box-shadow: var(--shadow);
        }

        .resident-benefit {
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            min-height: 92px;
            padding: 16px;
            border-right: 1px solid rgba(255, 255, 255, 0.14);
        }

        .resident-benefit:last-child { border-right: 0; }

        .resident-benefit-icon {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            color: var(--gold-2);
        }

        .resident-benefit strong {
            display: block;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .resident-benefit span {
            display: block;
            margin-top: 4px;
            color: #dce8f8;
            font-size: 11px;
            font-weight: 620;
            line-height: 1.35;
        }

        .resident-modal-summary {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            padding: 12px;
            border: 1px solid #e3eaf3;
            border-radius: 8px;
            background: #f8fbff;
        }

        .resident-modal-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .visitor-content { padding: 18px 14px 30px; }
        .visitor-page { display: grid; gap: 12px; }

        .visitor-toolbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            padding: 4px 2px 10px;
        }

        .visitor-heading {
            display: grid;
            grid-template-columns: 38px minmax(0, 1fr);
            gap: 12px;
            align-items: start;
        }

        .visitor-step {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 50%;
            background: linear-gradient(145deg, #061936, #0a2a59);
            color: #ffffff;
            font-weight: 900;
        }

        .visitor-heading h2 {
            margin: 0;
            color: #0b2149;
            font-size: 20px;
            font-weight: 900;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .visitor-heading p {
            margin: 8px 0 0;
            color: #607087;
            font-size: 12px;
            font-weight: 650;
        }

        .visitor-toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .visitor-stat-strip {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .visitor-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 9px 14px;
            border: 1px solid #dce4ef;
            border-radius: 7px;
            background: #f8fafd;
            color: #10264a;
            font-weight: 850;
            white-space: nowrap;
        }

        .visitor-tabs {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 4px;
            border: 1px solid #dce4ef;
            border-radius: 8px;
            background: #f8fafd;
        }

        .visitor-tab {
            display: inline-flex;
            min-height: 34px;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #53647d;
            font-size: 12px;
            font-weight: 850;
            cursor: default;
        }

        .visitor-tab.active {
            background: var(--navy-800);
            color: #ffffff;
        }

        .visitor-action-rail {
            display: grid;
            grid-template-columns: repeat(9, minmax(145px, 1fr));
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: thin;
        }

        .visitor-action {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 9px;
            align-items: center;
            min-height: 62px;
            padding: 10px;
            border: 1px solid rgba(219, 232, 248, 0.34);
            border-radius: 8px;
            background: linear-gradient(145deg, #061936, #0a2a59);
            color: #ffffff;
            cursor: pointer;
            text-align: left;
        }

        .visitor-action:hover,
        .visitor-action.active {
            background: linear-gradient(90deg, rgba(221, 165, 68, 0.9), rgba(221, 165, 68, 0.44)), #0a2a59;
        }

        .visitor-action-icon {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 50%;
            color: #ffffff;
        }

        .visitor-action strong { display: block; font-size: 12px; line-height: 1.2; }
        .visitor-action span { color: #dbe8f8; font-size: 11px; font-weight: 760; }

        .visitor-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px;
            align-items: start;
        }

        .visitor-span-3 { grid-column: span 3; }
        .visitor-span-4 { grid-column: span 4; }
        .visitor-span-5 { grid-column: span 5; }
        .visitor-span-7 { grid-column: span 7; }
        .visitor-span-8 { grid-column: span 8; }
        .visitor-span-9 { grid-column: span 9; }
        .visitor-span-12 { grid-column: span 12; }

        .visitor-panel {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(5, 18, 42, 0.07);
        }

        .visitor-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 48px;
            padding: 12px 16px;
            border-bottom: 1px solid #e3eaf3;
        }

        .visitor-panel-title {
            margin: 0;
            color: #0b2149;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .visitor-panel-body {
            display: grid;
            gap: 12px;
            padding: 14px 16px;
        }

        .visitor-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 16px;
        }

        .visitor-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .visitor-action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 7px;
        }

        .visitor-list { display: grid; gap: 0; }

        .visitor-list-row {
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr) auto auto;
            gap: 12px;
            align-items: center;
            min-height: 86px;
            border-bottom: 1px solid #e8eef6;
        }

        .visitor-list-row:last-child { border-bottom: 0; }

        .visitor-avatar {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 50%;
            background: #edf3fb;
            color: #0b2149;
            font-weight: 900;
        }

        .visitor-list-row strong,
        .visitor-detail-name {
            display: block;
            color: #10264a;
            font-size: 13px;
            font-weight: 900;
        }

        .visitor-list-row small {
            display: block;
            margin-top: 4px;
            color: #607087;
            font-size: 11px;
            font-weight: 650;
        }

        .visitor-kebab {
            display: grid;
            width: 32px;
            height: 32px;
            place-items: center;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #0b2149;
            cursor: pointer;
            font-size: 18px;
            font-weight: 900;
        }

        .visitor-kebab:hover { background: #edf3fb; }

        .visitor-detail-top {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            padding-bottom: 14px;
            border-bottom: 1px solid #e3eaf3;
        }

        .visitor-detail-avatar {
            display: grid;
            width: 58px;
            height: 58px;
            place-items: center;
            border-radius: 50%;
            background: #edf3fb;
            color: #071935;
        }

        .visitor-detail-section {
            display: grid;
            gap: 10px;
            padding-top: 4px;
        }

        .visitor-detail-section h3 {
            margin: 0;
            color: #0b2149;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .visitor-info-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
            gap: 10px;
            color: #10264a;
            font-size: 12px;
        }

        .visitor-info-row span:first-child {
            color: #607087;
            font-weight: 720;
        }

        .status-pending { background: #fff5df; color: #8b5d09; }
        .status-approved { background: #e6f8ee; color: #0f7d45; }
        .status-rejected { background: #ffe9e7; color: #b42318; }
        .status-expired { background: #eef3fa; color: #53647d; }

        .btn.success { background: #159a63; }
        .btn.success:hover { background: #0f7d45; }
        .btn.info { background: #437fac; }
        .btn.info:hover { background: #32678e; }
        .btn.compact { min-height: 30px; padding: 6px 10px; font-size: 12px; }

        .visitor-table-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
        }

        .visitor-table-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
        }

        .visitor-panel > .visitor-table-filters {
            justify-content: flex-start;
            padding: 12px 16px;
            border-bottom: 1px solid #e3eaf3;
        }

        .visitor-table-filters select,
        .visitor-table-filters input {
            min-height: 36px;
            width: auto;
            min-width: 170px;
        }

        .visitor-integration {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
            align-items: stretch;
        }

        .integration-step {
            display: grid;
            gap: 7px;
            justify-items: center;
            min-height: 104px;
            padding: 10px 8px;
            border: 1px solid #e3eaf3;
            border-radius: 7px;
            background: #f8fbff;
            color: #10264a;
            text-align: center;
        }

        .integration-step strong { font-size: 11px; line-height: 1.2; }
        .integration-step span { color: #607087; font-size: 10px; line-height: 1.25; }

        .visitor-chart {
            min-height: 330px;
            padding: 18px;
            border-top: 1px solid #e3eaf3;
        }

        .visitor-line-chart {
            position: relative;
            height: 260px;
            border-left: 1px solid #cbd6e4;
            border-bottom: 1px solid #cbd6e4;
            background:
                linear-gradient(180deg, rgba(67, 127, 172, 0.2), rgba(67, 127, 172, 0.04)),
                repeating-linear-gradient(0deg, #ffffff 0 51px, #e5ecf5 52px 53px);
            overflow: hidden;
        }

        .visitor-line-chart::before {
            content: "";
            position: absolute;
            inset: 28px 24px 42px 28px;
            background: linear-gradient(135deg, transparent 0 8%, #5b83a7 8.4% 9.4%, transparent 9.8% 18%, #5b83a7 18.4% 19.4%, transparent 19.8% 31%, #5b83a7 31.4% 32.4%, transparent 32.8% 44%, #5b83a7 44.4% 45.4%, transparent 45.8% 58%, #5b83a7 58.4% 59.4%, transparent 59.8% 72%, #5b83a7 72.4% 73.4%, transparent 73.8% 86%, #5b83a7 86.4% 87.4%, transparent 87.8%);
        }

        .visitor-chart-labels {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
            color: #52647d;
            font-size: 12px;
            text-align: center;
        }

        .visitor-report-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .visitor-donut-wrap {
            display: grid;
            grid-template-columns: 170px minmax(0, 1fr);
            gap: 18px;
            align-items: center;
            min-height: 240px;
        }

        .visitor-donut-purpose {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: conic-gradient(#0d2a62 0 30%, #4d86b5 30% 57%, #c73b33 57% 73%, #c7802f 73% 100%);
            display: grid;
            place-items: center;
        }

        .visitor-donut-purpose::after {
            content: "";
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: #ffffff;
        }

        .visitor-bars-chart {
            display: grid;
            grid-template-columns: repeat(10, minmax(0, 1fr));
            align-items: end;
            gap: 9px;
            min-height: 210px;
            padding: 14px 4px 0;
            border-bottom: 1px solid #cbd6e4;
            background: repeating-linear-gradient(0deg, #ffffff 0 41px, #e5ecf5 42px 43px);
        }

        .visitor-bar {
            min-height: 38px;
            border-radius: 4px 4px 0 0;
            background: #4d86b5;
        }

        .visitor-bar.hot { background: #0d2a62; }

        .service-content { padding: 18px 14px 30px; }
        .service-page { display: grid; gap: 12px; }

        .sidebar-quick-actions {
            display: grid;
            gap: 8px;
            margin-top: 13px;
            padding: 12px;
            border: 1px solid rgba(216, 229, 246, 0.18);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.04);
        }

        .sidebar-quick-title {
            color: var(--gold-2);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .sidebar-quick-link {
            display: grid;
            grid-template-columns: 22px minmax(0, 1fr);
            gap: 8px;
            align-items: center;
            min-height: 30px;
            color: #dce8f8;
            font-size: 12px;
            font-weight: 720;
        }

        .sidebar-quick-link:hover { color: #ffffff; }

        .service-metrics {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
        }

        .service-metric {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            min-height: 74px;
            padding: 13px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(5, 18, 42, 0.06);
        }

        .service-metric-icon {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 9px;
            background: #eef5fb;
            color: #0d2a62;
        }

        .service-metric span {
            display: block;
            color: #607087;
            font-size: 11px;
            font-weight: 780;
            line-height: 1.2;
        }

        .service-metric strong {
            display: block;
            margin-top: 4px;
            color: #071935;
            font-size: 20px;
            font-weight: 920;
            line-height: 1;
        }

        .service-widget-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .service-widget {
            min-height: 122px;
            padding: 12px;
            border: 1px solid #e3eaf3;
            border-radius: 8px;
            background: #ffffff;
        }

        .service-widget h3 {
            margin: 0 0 10px;
            color: #0b2149;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .service-progress-list { display: grid; gap: 9px; }

        .service-progress-row {
            display: grid;
            grid-template-columns: 82px minmax(0, 1fr) 36px;
            gap: 9px;
            align-items: center;
            color: #10264a;
            font-size: 11px;
            font-weight: 760;
        }

        .service-progress-track {
            height: 9px;
            overflow: hidden;
            border-radius: 999px;
            background: #e8eef6;
        }

        .service-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: #159a63;
        }

        .service-progress-fill.warn { background: #e3a843; }
        .service-progress-fill.danger { background: #c73b33; }

        .service-mini-chart {
            position: relative;
            min-height: 150px;
            border-left: 1px solid #cbd6e4;
            border-bottom: 1px solid #cbd6e4;
            background: repeating-linear-gradient(0deg, #ffffff 0 35px, #e5ecf5 36px 37px);
            overflow: hidden;
        }

        .service-mini-chart::before {
            content: "";
            position: absolute;
            inset: 24px 20px 28px 18px;
            background: linear-gradient(135deg, transparent 0 11%, #527ca2 11.4% 12.4%, transparent 12.8% 23%, #527ca2 23.4% 24.4%, transparent 24.8% 36%, #527ca2 36.4% 37.4%, transparent 37.8% 50%, #527ca2 50.4% 51.4%, transparent 51.8% 63%, #527ca2 63.4% 64.4%, transparent 64.8% 78%, #527ca2 78.4% 79.4%, transparent 79.8%);
        }

        .service-bars {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            align-items: end;
            gap: 10px;
            min-height: 150px;
            padding: 10px 4px 0;
            border-bottom: 1px solid #cbd6e4;
            background: repeating-linear-gradient(0deg, #ffffff 0 35px, #e5ecf5 36px 37px);
        }

        .service-bar {
            min-height: 24px;
            border-radius: 4px 4px 0 0;
            background: #437fac;
        }

        .service-bar.hot { background: #0d2a62; }
        .service-bar.warn { background: #e3a843; }
        .service-bar.danger { background: #c73b33; }

        .service-donut {
            display: grid;
            width: 150px;
            height: 150px;
            place-items: center;
            border-radius: 50%;
            background: conic-gradient(#159a63 0 94%, #e3a843 94% 98%, #c73b33 98% 100%);
        }

        .service-donut::after {
            content: attr(data-value);
            display: grid;
            width: 82px;
            height: 82px;
            place-items: center;
            border-radius: 50%;
            background: #ffffff;
            color: #0b2149;
            font-size: 20px;
            font-weight: 920;
        }

        .service-detail-photo,
        .service-upload-box,
        .service-signature {
            min-height: 90px;
            border: 1px solid #dce4ef;
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(6, 25, 54, 0.04), rgba(6, 25, 54, 0.12)),
                repeating-linear-gradient(45deg, #eef4fb 0 12px, #e3eaf3 12px 24px);
        }

        .service-photo-pair {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .service-signature {
            display: grid;
            place-items: center;
            min-height: 56px;
            color: #0b2149;
            font-family: "Segoe Script", cursive;
            font-size: 18px;
            font-weight: 700;
            background: #f7f9fd;
        }

        .service-timeline {
            min-width: 720px;
            overflow: hidden;
            border: 1px solid #dce4ef;
            border-radius: 8px;
            background: #ffffff;
        }

        .service-timeline-head,
        .service-timeline-row {
            display: grid;
            grid-template-columns: 128px repeat(10, minmax(64px, 1fr));
        }

        .service-timeline-head span,
        .service-timeline-row > span {
            min-height: 44px;
            padding: 11px 10px;
            border-right: 1px solid #e3eaf3;
            border-bottom: 1px solid #e3eaf3;
            color: #53647d;
            font-size: 11px;
            font-weight: 850;
            text-align: center;
        }

        .service-timeline-row {
            position: relative;
            min-height: 72px;
        }

        .service-tech-name {
            display: grid !important;
            place-items: center start;
            text-align: left !important;
            color: #10264a !important;
        }

        .service-task-pill {
            position: absolute;
            top: 18px;
            min-height: 34px;
            padding: 8px 10px;
            border: 1px solid #8db9d9;
            border-radius: 7px;
            background: #e8f5fb;
            color: #0b2149;
            font-size: 11px;
            font-weight: 820;
            box-shadow: 0 8px 16px rgba(5, 18, 42, 0.09);
        }

        .service-task-pill.gold { border-color: #e4c06b; background: #fff5df; }
        .service-task-pill.navy { border-color: #0d2a62; background: #0d2a62; color: #ffffff; }
        .service-task-pill.red { border-color: #efaaa4; background: #ffe9e7; }

        .service-kanban {
            display: grid;
            grid-template-columns: 190px minmax(0, 1fr) 220px;
            gap: 12px;
        }

        .service-ticket-card {
            display: grid;
            gap: 8px;
            padding: 12px;
            border: 1px solid #dce4ef;
            border-radius: 8px;
            background: #ffffff;
        }

        .service-attachment-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .service-audit {
            display: grid;
            gap: 0;
            max-height: 540px;
            overflow: auto;
        }

        .service-audit-row {
            display: grid;
            grid-template-columns: 10px minmax(0, 1fr);
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #e8eef6;
            color: #10264a;
            font-size: 12px;
            font-weight: 700;
        }

        .service-audit-row::before {
            content: "";
            width: 8px;
            height: 8px;
            margin-top: 5px;
            border-radius: 50%;
            background: #159a63;
        }

        .community-content { padding: 18px 14px 30px; }
        .community-page { display: grid; gap: 12px; }

        .community-workspace {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 330px;
            gap: 12px;
            align-items: start;
        }

        .community-main,
        .community-side {
            display: grid;
            min-width: 0;
            gap: 12px;
        }

        .community-split {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.85fr);
            gap: 12px;
            align-items: start;
        }

        .community-card-row {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }

        .community-metric {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            min-height: 92px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(5, 18, 42, 0.06);
        }

        .community-metric-icon,
        .community-tile-icon {
            display: grid;
            place-items: center;
            border-radius: 10px;
            color: #0b2149;
            font-weight: 900;
        }

        .community-metric-icon {
            width: 46px;
            height: 46px;
            background: #edf3fb;
        }

        .community-metric.purple .community-metric-icon { background: #f4eafd; color: #7b3dd1; }
        .community-metric.green .community-metric-icon { background: #e8f7ee; color: #13925a; }
        .community-metric.gold .community-metric-icon { background: #fff5df; color: #b87924; }
        .community-metric.blue .community-metric-icon { background: #e7f1ff; color: #1f7bef; }
        .community-metric.red .community-metric-icon { background: #ffecef; color: #c73b33; }

        .community-metric span {
            display: block;
            color: #607087;
            font-size: 11px;
            font-weight: 780;
            line-height: 1.2;
        }

        .community-metric strong {
            display: block;
            margin-top: 4px;
            color: #071935;
            font-size: 22px;
            font-weight: 930;
            line-height: 1;
        }

        .community-list { display: grid; gap: 0; }

        .community-row {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            min-height: 92px;
            padding: 12px 0;
            border-bottom: 1px solid #e8eef6;
        }

        .community-row:last-child { border-bottom: 0; }

        .community-tile-icon {
            width: 54px;
            height: 54px;
            background: #edf3fb;
            font-size: 20px;
        }

        .community-thumb {
            width: 96px;
            height: 54px;
            border-radius: 7px;
            background:
                linear-gradient(135deg, rgba(6, 25, 54, 0.08), rgba(6, 25, 54, 0.18)),
                repeating-linear-gradient(45deg, #edf4fb 0 12px, #dce8f6 12px 24px);
            object-fit: cover;
        }

        .community-row h3,
        .community-event-title {
            margin: 0;
            color: #10264a;
            font-size: 13px;
            font-weight: 920;
        }

        .community-row p {
            margin: 5px 0 0;
            color: #53647d;
            font-size: 12px;
            font-weight: 650;
        }

        .community-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 12px;
            margin-top: 8px;
            color: #607087;
            font-size: 11px;
            font-weight: 720;
        }

        .community-date-tile {
            display: grid;
            width: 62px;
            height: 70px;
            place-items: center;
            border: 1px solid #e3eaf3;
            border-radius: 8px;
            background: #f8fbff;
            color: #0b2149;
            text-align: center;
        }

        .community-date-tile strong {
            display: block;
            font-size: 20px;
            line-height: 1;
        }

        .community-date-tile span {
            display: block;
            margin-top: 4px;
            color: #53647d;
            font-size: 11px;
            font-weight: 850;
        }

        .community-event-row {
            display: grid;
            grid-template-columns: 62px 96px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            min-height: 92px;
            padding: 12px 0;
            border-bottom: 1px solid #e8eef6;
        }

        .community-event-row:last-child { border-bottom: 0; }

        .community-progress-line {
            height: 7px;
            overflow: hidden;
            border-radius: 999px;
            background: #e8eef6;
        }

        .community-progress-line span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #1f7bef;
        }

        .community-progress-line.green span { background: #159a63; }
        .community-progress-line.gold span { background: #e3a843; }
        .community-progress-line.red span { background: #c73b33; }

        .community-donut {
            display: grid;
            width: 132px;
            height: 132px;
            place-items: center;
            border-radius: 50%;
            background: conic-gradient(#159a63 0 42%, #1f7bef 42% 74%, #f4c15d 74% 100%);
        }

        .community-donut::after {
            content: attr(data-value);
            display: grid;
            width: 72px;
            height: 72px;
            place-items: center;
            border-radius: 50%;
            background: #ffffff;
            color: #0b2149;
            font-size: 17px;
            font-weight: 930;
            text-align: center;
            white-space: pre-line;
        }

        .community-donut-panel {
            display: grid;
            grid-template-columns: 140px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
        }

        .community-legend {
            display: grid;
            gap: 10px;
            color: #10264a;
            font-size: 12px;
            font-weight: 760;
        }

        .community-legend span {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .community-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #159a63;
        }

        .community-dot.blue { background: #1f7bef; }
        .community-dot.gold { background: #f4c15d; }

        .community-broadcast {
            display: grid;
            gap: 10px;
        }

        .community-broadcast textarea {
            min-height: 96px;
            resize: vertical;
        }

        .community-mini-list {
            display: grid;
            gap: 10px;
        }

        .community-mini-row {
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            color: #10264a;
            font-size: 12px;
            font-weight: 760;
        }

        .community-calendar-mini,
        .community-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
            text-align: center;
        }

        .community-calendar-mini span,
        .community-calendar-grid > span,
        .community-calendar-day {
            min-height: 30px;
            padding: 7px 4px;
            border-radius: 7px;
            color: #10264a;
            font-size: 11px;
            font-weight: 760;
        }

        .community-calendar-mini .active,
        .community-calendar-day.active {
            background: #0d2a62;
            color: #ffffff;
        }

        .community-filter-bar {
            display: grid;
            grid-template-columns: minmax(220px, 1.4fr) repeat(4, minmax(150px, 1fr));
            gap: 9px;
            padding: 12px 16px;
            border-bottom: 1px solid #e3eaf3;
        }

        .community-filter-bar input,
        .community-filter-bar select {
            min-height: 36px;
        }

        .community-action-icons {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 6px;
        }

        .community-icon-btn {
            display: grid;
            width: 30px;
            height: 30px;
            place-items: center;
            border: 1px solid #dce4ef;
            border-radius: 7px;
            background: #ffffff;
            color: #0d2a62;
            cursor: pointer;
            font-size: 12px;
            font-weight: 900;
        }

        .community-icon-btn.gold { background: #fff5df; color: #8b5d09; }
        .community-icon-btn.green { background: #e6f8ee; color: #0f7d45; }
        .community-icon-btn:hover { border-color: #9eb2ca; background: #edf3fb; }

        .community-chart-line {
            position: relative;
            min-height: 230px;
            border-left: 1px solid #cbd6e4;
            border-bottom: 1px solid #cbd6e4;
            background:
                linear-gradient(180deg, rgba(67, 127, 172, 0.18), rgba(67, 127, 172, 0.03)),
                repeating-linear-gradient(0deg, #ffffff 0 43px, #e5ecf5 44px 45px);
            overflow: hidden;
        }

        .community-chart-line::before,
        .community-chart-line::after {
            content: "";
            position: absolute;
            inset: 32px 24px 38px 24px;
            background: linear-gradient(135deg, transparent 0 9%, #0d2a62 9.4% 10.2%, transparent 10.6% 21%, #0d2a62 21.4% 22.2%, transparent 22.6% 34%, #0d2a62 34.4% 35.2%, transparent 35.6% 48%, #0d2a62 48.4% 49.2%, transparent 49.6% 62%, #0d2a62 62.4% 63.2%, transparent 63.6% 77%, #0d2a62 77.4% 78.2%, transparent 78.6% 90%, #0d2a62 90.4% 91.2%, transparent 91.6%);
        }

        .community-chart-line::after {
            inset: 48px 24px 54px 24px;
            opacity: 0.8;
            background: linear-gradient(135deg, transparent 0 11%, #159a63 11.4% 12.2%, transparent 12.6% 25%, #159a63 25.4% 26.2%, transparent 26.6% 39%, #159a63 39.4% 40.2%, transparent 40.6% 52%, #159a63 52.4% 53.2%, transparent 53.6% 66%, #159a63 66.4% 67.2%, transparent 67.6% 81%, #159a63 81.4% 82.2%, transparent 82.6%);
        }

        .community-bar-chart {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            align-items: end;
            gap: 12px;
            min-height: 220px;
            padding: 12px 6px 0;
            border-bottom: 1px solid #cbd6e4;
            background: repeating-linear-gradient(0deg, #ffffff 0 43px, #e5ecf5 44px 45px);
        }

        .community-bar {
            min-height: 34px;
            border-radius: 5px 5px 0 0;
            background: #4d86b5;
        }

        .community-bar.hot { background: #0d2a62; }
        .community-bar.green { background: #159a63; }
        .community-bar.gold { background: #e3a843; }

        .community-calendar-large {
            display: grid;
            grid-template-columns: repeat(7, minmax(110px, 1fr));
            min-width: 780px;
            border: 1px solid #dce4ef;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
        }

        .community-calendar-large > div {
            min-height: 112px;
            padding: 9px;
            border-right: 1px solid #e3eaf3;
            border-bottom: 1px solid #e3eaf3;
        }

        .community-calendar-large > div:nth-child(7n) { border-right: 0; }

        .community-calendar-head {
            min-height: auto !important;
            padding: 8px !important;
            background: #f2f5f9;
            color: #0b2149;
            font-weight: 900;
            text-align: center;
        }

        .community-event-block {
            display: block;
            margin-top: 7px;
            padding: 7px;
            border-radius: 7px;
            background: #e8f7ee;
            color: #0b4d31;
            font-size: 11px;
            font-weight: 780;
        }

        .community-event-block.blue { background: #e7f1ff; color: #0d2a62; }
        .community-event-block.gold { background: #fff5df; color: #8b5d09; }
        .community-event-block.red { background: #ffecef; color: #9f1d24; }

        .community-setting-toggle {
            display: inline-flex;
            width: 42px;
            height: 22px;
            align-items: center;
            padding: 3px;
            border-radius: 999px;
            background: #159a63;
        }

        .community-setting-toggle::after {
            content: "";
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            margin-left: auto;
        }

        .community-setting-toggle.off {
            background: #9eb2ca;
        }

        .community-setting-toggle.off::after {
            margin-left: 0;
        }

        .tenant-content { padding: 18px 14px 30px; }
        .tenant-page { display: grid; gap: 14px; }

        .tenant-workspace {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 330px;
            gap: 14px;
            align-items: start;
        }

        .tenant-main,
        .tenant-side {
            display: grid;
            min-width: 0;
            gap: 14px;
        }

        .tenant-titlebar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: 4px 2px 8px;
        }

        .tenant-titlebar h2 {
            margin: 0;
            color: #0b2149;
            font-size: 24px;
            font-weight: 920;
            line-height: 1.1;
        }

        .tenant-titlebar p {
            margin: 8px 0 0;
            color: #607087;
            font-size: 13px;
            font-weight: 650;
        }

        .tenant-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .tenant-metrics {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }

        .tenant-metric {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            gap: 13px;
            align-items: center;
            min-height: 108px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(5, 18, 42, 0.06);
        }

        .tenant-metric-icon,
        .tenant-logo {
            display: grid;
            place-items: center;
            border-radius: 12px;
            color: #0d2a62;
            font-weight: 920;
        }

        .tenant-metric-icon {
            width: 50px;
            height: 50px;
            background: #e7f1ff;
        }

        .tenant-metric.green .tenant-metric-icon { background: #e6f8ee; color: #0f7d45; }
        .tenant-metric.gold .tenant-metric-icon { background: #fff5df; color: #b87924; }
        .tenant-metric.red .tenant-metric-icon { background: #ffe9e7; color: #b42318; }
        .tenant-metric.purple .tenant-metric-icon { background: #f4eafd; color: #7b3dd1; }

        .tenant-metric span {
            display: block;
            color: #607087;
            font-size: 12px;
            font-weight: 760;
        }

        .tenant-metric strong {
            display: block;
            margin: 6px 0 5px;
            color: #071935;
            font-size: 26px;
            font-weight: 930;
            line-height: 1;
        }

        .tenant-filter-bar {
            display: grid;
            grid-template-columns: minmax(240px, 1.5fr) repeat(3, minmax(155px, 1fr)) 110px;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid #e3eaf3;
            background: #ffffff;
        }

        .tenant-logo {
            width: 44px;
            height: 44px;
            overflow: hidden;
            border-radius: 50%;
            background: #0d2a62;
            color: #ffffff;
            font-size: 9px;
            line-height: 1.05;
            text-align: center;
        }

        .tenant-logo.blue { background: #1f7bef; }
        .tenant-logo.green { background: #38b893; }
        .tenant-logo.teal { background: #4d9fc0; }
        .tenant-logo.pink { background: #f2a9ba; color: #63202d; }
        .tenant-logo.gold { background: #e58a12; }
        .tenant-logo.black { background: #111827; }

        .tenant-name strong {
            display: block;
            color: #10264a;
            font-weight: 900;
        }

        .tenant-name span {
            display: block;
            margin-top: 4px;
            color: #53647d;
            font-size: 12px;
            font-weight: 650;
        }

        .tenant-donut {
            display: grid;
            width: 156px;
            height: 156px;
            place-items: center;
            border-radius: 50%;
            background: conic-gradient(#159a63 0 87.5%, #f4c15d 87.5% 95.8%, #e43f35 95.8% 100%);
        }

        .tenant-donut::after {
            content: attr(data-value);
            display: grid;
            width: 86px;
            height: 86px;
            place-items: center;
            border-radius: 50%;
            background: #ffffff;
            color: #0b2149;
            font-size: 20px;
            font-weight: 930;
            text-align: center;
            white-space: pre-line;
        }

        .tenant-category-list {
            display: grid;
            gap: 13px;
        }

        .tenant-category-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 90px 28px;
            gap: 10px;
            align-items: center;
            color: #10264a;
            font-size: 12px;
            font-weight: 760;
        }

        .tenant-category-track {
            height: 6px;
            overflow: hidden;
            border-radius: 999px;
            background: #e8eef6;
        }

        .tenant-category-track span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #0d2a62;
        }

        .tenant-info-box {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
            color: #10264a;
        }

        .tenant-phone-icon {
            display: grid;
            width: 36px;
            height: 54px;
            place-items: center;
            border: 2px solid #71809a;
            border-radius: 8px;
            color: #0d2a62;
            font-weight: 900;
        }

        .tenant-form-panel {
            display: grid;
            gap: 22px;
            padding: 18px;
        }

        .tenant-form-section {
            display: grid;
            gap: 13px;
        }

        .tenant-form-section h3 {
            margin: 0;
            color: #111827;
            font-size: 18px;
            font-weight: 900;
        }

        .tenant-form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px 16px;
        }

        .tenant-form-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .tenant-location-grid {
            display: grid;
            grid-template-columns: 110px minmax(0, 1fr) 40px;
            gap: 8px;
        }

        .tenant-upload {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 46px;
            padding: 10px 12px;
            border: 1px dashed #bcc9da;
            border-radius: 8px;
            background: #f9fbfe;
            color: #10264a;
            font-size: 13px;
            font-weight: 760;
        }

        .tenant-form-footer {
            position: sticky;
            bottom: 0;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            border-top: 1px solid #e3eaf3;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
        }

        .package-content { padding: 18px 14px 30px; }
        .package-page,
        .package-main,
        .package-bottom-grid,
        .package-benefits,
        .package-modal-form,
        .package-modal-section {
            display: grid;
            gap: 14px;
        }

        .package-page { min-width: 0; }

        .package-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(260px, 0.85fr);
            gap: 20px;
            align-items: stretch;
            padding: 20px 24px;
            border: 1px solid #dde6f1;
            border-radius: 10px;
            background:
                radial-gradient(circle at 10% 10%, rgba(221, 165, 68, 0.16), transparent 30%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 253, 0.98));
            box-shadow: 0 16px 34px rgba(5, 18, 42, 0.08);
        }

        .package-hero-main {
            display: grid;
            grid-template-columns: 104px minmax(0, 1fr);
            gap: 20px;
            align-items: center;
        }

        .package-hero-icon {
            display: grid;
            width: 104px;
            height: 104px;
            place-items: center;
            border-radius: 22px;
            background: linear-gradient(180deg, #0a2450, #08204a);
            color: #ffffff;
            box-shadow: inset 0 0 0 3px rgba(221, 165, 68, 0.78), 0 18px 30px rgba(5, 18, 42, 0.16);
        }

        .package-hero-copy h2 {
            margin: 0;
            color: #0b2149;
            font-size: 28px;
            font-weight: 930;
            line-height: 1.06;
        }

        .package-hero-copy p {
            max-width: 620px;
            margin: 12px 0 0;
            color: #41526a;
            font-size: 15px;
            font-weight: 580;
        }

        .package-illustration {
            position: relative;
            min-height: 176px;
            overflow: hidden;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0)),
                radial-gradient(circle at 85% 15%, rgba(244, 193, 93, 0.42), transparent 22%),
                linear-gradient(180deg, rgba(241, 245, 251, 0.95), rgba(249, 251, 254, 0.72));
        }

        .package-plant {
            position: absolute;
            right: 22px;
            bottom: 18px;
            width: 46px;
            height: 92px;
            border-radius: 18px 18px 8px 8px;
            background:
                radial-gradient(circle at 50% 18%, #6fbe7a 0 22px, transparent 22px),
                linear-gradient(180deg, rgba(0, 0, 0, 0) 0 60%, #8d6841 60% 100%);
        }

        .package-plant::before,
        .package-plant::after {
            content: "";
            position: absolute;
            top: 12px;
            width: 16px;
            height: 44px;
            border-radius: 16px 16px 0 16px;
            background: linear-gradient(180deg, #74c67c, #3d8a49);
        }

        .package-plant::before {
            left: -8px;
            transform: rotate(-28deg);
        }

        .package-plant::after {
            right: -8px;
            transform: scaleX(-1) rotate(-28deg);
        }

        .package-box {
            position: absolute;
            border-radius: 10px;
            background: linear-gradient(145deg, #dba24c, #c98528);
            box-shadow: 0 14px 28px rgba(127, 84, 18, 0.18);
        }

        .package-box::before,
        .package-box::after {
            content: "";
            position: absolute;
            background: rgba(255, 255, 255, 0.24);
        }

        .package-box::before {
            top: 16%;
            left: 46%;
            width: 8%;
            height: 68%;
        }

        .package-box::after {
            top: 44%;
            left: 10%;
            width: 80%;
            height: 8%;
        }

        .package-box.one {
            right: 110px;
            bottom: 18px;
            width: 136px;
            height: 104px;
        }

        .package-box.two {
            right: 30px;
            bottom: 40px;
            width: 112px;
            height: 86px;
        }

        .package-box.three {
            right: 70px;
            top: 16px;
            width: 88px;
            height: 68px;
        }

        .package-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .package-metric {
            display: grid;
            grid-template-columns: 56px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            min-height: 118px;
            padding: 18px;
            border: 1px solid #dde6f1;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(5, 18, 42, 0.06);
        }

        .package-metric-icon {
            display: grid;
            width: 54px;
            height: 54px;
            place-items: center;
            border-radius: 50%;
            color: #0d2a62;
            background: #edf4ff;
        }

        .package-metric-icon.gold { background: #fff6e0; color: #c38320; }
        .package-metric-icon.green { background: #e8f8ef; color: #12915a; }
        .package-metric-icon.purple { background: #f3eafd; color: #7f4ad3; }

        .package-metric span {
            display: block;
            color: #607087;
            font-size: 12px;
            font-weight: 760;
        }

        .package-metric strong {
            display: block;
            margin: 7px 0 4px;
            color: #071935;
            font-size: 23px;
            font-weight: 930;
            line-height: 1;
        }

        .package-metric .package-change {
            color: #14925c;
            font-size: 12px;
            font-weight: 860;
        }

        .package-panel {
            overflow: hidden;
            border: 1px solid #dde6f1;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 14px 30px rgba(5, 18, 42, 0.06);
        }

        .package-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px 0;
        }

        .package-panel-head h3 {
            margin: 0;
            color: #0b2149;
            font-size: 18px;
            font-weight: 920;
        }

        .package-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 20px;
        }

        .package-search {
            position: relative;
            flex: 1 1 320px;
            min-width: 0;
        }

        .package-search input {
            width: 100%;
            min-height: 46px;
            padding-right: 44px;
        }

        .package-search svg {
            position: absolute;
            top: 50%;
            right: 14px;
            color: #73839b;
            transform: translateY(-50%);
        }

        .package-toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 12px;
        }

        .package-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 840;
            white-space: nowrap;
        }

        .package-status.waiting { background: #fff5df; color: #a96d10; }
        .package-status.collected { background: #e7f8ec; color: #0d8a48; }
        .package-status.ready { background: #e4f6eb; color: #107851; }
        .package-status.expired { background: #fdebec; color: #c0363d; }

        .package-table-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .package-kebab {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 1px solid #dbe4ef;
            border-radius: 8px;
            background: #ffffff;
            color: #73561f;
            cursor: pointer;
        }

        .package-benefits {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            padding: 0;
            overflow: hidden;
            border-radius: 10px;
            background: linear-gradient(180deg, #ffffff, #fbfcff);
            border: 1px solid #dde6f1;
            box-shadow: 0 14px 30px rgba(5, 18, 42, 0.05);
        }

        .package-benefit {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
            padding: 18px 16px;
            border-right: 1px solid #ebf0f7;
        }

        .package-benefit:last-child { border-right: 0; }

        .package-benefit-icon {
            display: grid;
            width: 46px;
            height: 46px;
            place-items: center;
            border-radius: 12px;
            background: #edf4ff;
            color: #0b2149;
        }

        .package-benefit h4 {
            margin: 0 0 8px;
            color: #0b2149;
            font-size: 15px;
            font-weight: 860;
        }

        .package-benefit p {
            margin: 0;
            color: #586a82;
            font-size: 12px;
            font-weight: 620;
        }

        .package-modal-dialog {
            width: min(920px, 100%);
        }

        .package-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: 20px 22px 14px;
            border-bottom: 1px solid #e3eaf3;
        }

        .package-modal-titlewrap {
            display: grid;
            grid-template-columns: 54px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
        }

        .package-modal-symbol {
            display: grid;
            width: 54px;
            height: 54px;
            place-items: center;
            border-radius: 50%;
            background: #fff5df;
            color: #c38320;
        }

        .package-modal-titlewrap h3 {
            margin: 0;
            color: #0b2149;
            font-size: 18px;
            font-weight: 920;
        }

        .package-modal-titlewrap p {
            margin: 6px 0 0;
            color: #607087;
            font-size: 13px;
            font-weight: 650;
        }

        .package-modal-body {
            display: grid;
            gap: 18px;
            padding: 18px 22px 22px;
        }

        .package-modal-section {
            padding-bottom: 16px;
            border-bottom: 1px solid #ebf0f7;
        }

        .package-modal-section:last-of-type {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .package-modal-section h4 {
            margin: 0;
            color: #111827;
            font-size: 16px;
            font-weight: 900;
        }

        .package-modal-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px 16px;
        }

        .package-modal-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .package-modal-grid.full {
            grid-template-columns: 1fr;
        }

        .package-radio-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            color: #10264a;
            font-size: 13px;
            font-weight: 700;
        }

        .package-radio-row label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .package-upload {
            display: grid;
            justify-items: center;
            gap: 8px;
            min-height: 112px;
            padding: 22px 18px;
            border: 1px dashed #bcc9da;
            border-radius: 10px;
            background: #fbfcff;
            color: #4e6078;
            text-align: center;
        }

        .package-steps {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            align-items: center;
        }

        .package-step {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            color: #66768d;
            font-size: 13px;
            font-weight: 760;
        }

        .package-step::after {
            content: "";
            grid-column: 2 / -1;
            width: 100%;
            height: 1px;
            background: #d8e1ec;
            margin-top: -16px;
        }

        .package-step:last-child::after { display: none; }

        .package-step-index {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 50%;
            border: 1px solid #cbd7e5;
            background: #ffffff;
            color: #7a8ba2;
            font-weight: 900;
        }

        .package-step.active { color: #0b2149; }
        .package-step.active .package-step-index {
            border-color: #0d2a62;
            background: #0d2a62;
            color: #ffffff;
        }

        .package-signature {
            display: grid;
            place-items: center;
            min-height: 112px;
            border: 1px solid #d7e1ed;
            border-radius: 10px;
            background: linear-gradient(180deg, #ffffff, #f9fbfe);
            color: #111827;
            font-size: 42px;
            font-family: "Segoe Script", "Brush Script MT", cursive;
        }

        .package-inline-status {
            display: inline-flex;
            width: fit-content;
            margin-top: 6px;
        }

        .package-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 2px;
        }

        body.is-modal-open { overflow: hidden; }

        .visitor-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .visitor-modal.is-open { display: flex; }

        .visitor-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 11, 30, 0.64);
            backdrop-filter: blur(3px);
        }

        .visitor-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(760px, 100%);
            max-height: calc(100vh - 48px);
            overflow: auto;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 26px 70px rgba(2, 11, 30, 0.35);
        }

        .visitor-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid #e3eaf3;
        }

        .visitor-modal-title {
            margin: 0;
            color: #0b2149;
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .visitor-modal-close {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border: 0;
            border-radius: 7px;
            background: #edf3fb;
            color: #10264a;
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
        }

        .visitor-modal-body {
            display: grid;
            gap: 12px;
            padding: 16px 18px 18px;
        }

        .visitor-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        @media (min-width: 981px) {
            .ops-shell.is-sidebar-collapsed { grid-template-columns: 82px minmax(0, 1fr); }
            .ops-shell.is-sidebar-collapsed .ops-sidebar { overflow: visible; }
            .ops-shell.is-sidebar-collapsed .brand-block { justify-content: center; min-height: 78px; padding: 18px 10px; }
            .ops-shell.is-sidebar-collapsed .brand-tower { width: 38px; height: 42px; flex-basis: 38px; }
            .ops-shell.is-sidebar-collapsed .brand-block > div,
            .ops-shell.is-sidebar-collapsed .side-link span:not(.side-icon),
            .ops-shell.is-sidebar-collapsed .side-parent span:not(.side-icon),
            .ops-shell.is-sidebar-collapsed .side-sub,
            .ops-shell.is-sidebar-collapsed .sidebar-profile .profile-row > div:not(.avatar) { display: none; }
            .ops-shell.is-sidebar-collapsed .sidebar-scroll { padding: 0 10px 18px; }
            .ops-shell.is-sidebar-collapsed .side-link,
            .ops-shell.is-sidebar-collapsed .side-parent { justify-content: center; padding: 12px 8px; }
            .ops-shell.is-sidebar-collapsed .sidebar-quick-actions { display: none; }
            .ops-shell.is-sidebar-collapsed .sidebar-profile { margin: auto 10px 0; padding-inline: 0; }
            .ops-shell.is-sidebar-collapsed .profile-row { justify-content: center; }
        }

        @media (max-width: 1360px) {
            .ops-shell { grid-template-columns: 250px minmax(0, 1fr); }
            .stat-card { grid-column: span 4; }
            .span-3, .span-4, .span-5, .span-6, .span-8, .span-9 { grid-column: span 6; }
            .monitor-grid, .quick-summary { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .res-span-3, .res-span-4 { grid-column: span 6; }
            .res-span-5, .res-span-6, .res-span-7, .res-span-8 { grid-column: span 12; }
            .resident-benefits { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .resident-filter-panel { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .resident-filter-field:first-child { grid-column: span 2; }
            .resident-benefit-bar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .resident-benefit { border-bottom: 1px solid rgba(255, 255, 255, 0.14); }
            .benefit-cell { border-bottom: 1px solid rgba(255, 255, 255, 0.12); }
            .visitor-span-3, .visitor-span-4, .visitor-span-5, .visitor-span-7, .visitor-span-8, .visitor-span-9 { grid-column: span 6; }
            .visitor-integration { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .visitor-report-grid { grid-template-columns: 1fr; }
            .service-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .service-widget-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-kanban { grid-template-columns: minmax(0, 1fr); }
            .community-workspace { grid-template-columns: 1fr; }
            .community-card-row { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .community-split { grid-template-columns: 1fr; }
            .community-filter-bar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .tenant-workspace { grid-template-columns: 1fr; }
            .tenant-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .tenant-filter-bar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 980px) {
            .ops-shell { grid-template-columns: 1fr; }
            .ops-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 50;
                width: min(312px, 86vw);
                height: 100vh;
                transform: translateX(-104%);
                transition: transform 220ms ease;
                box-shadow: 18px 0 44px rgba(2, 11, 30, 0.34);
            }
            body.is-mobile-sidebar-open { overflow: hidden; }
            body.is-mobile-sidebar-open .ops-sidebar { transform: translateX(0); }
            body.is-mobile-sidebar-open .sidebar-overlay { display: block; }
            .ops-topbar { grid-template-columns: auto minmax(0, 1fr); gap: 12px; min-height: auto; padding: 12px 14px; }
            .top-title h1 { font-size: 17px; }
            .top-title p { font-size: 12px; }
            .top-actions { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); width: 100%; gap: 8px; }
            .top-chip, .top-profile, .top-actions form, .top-actions form .btn { width: 100%; }
            .top-profile { min-width: 0; justify-content: flex-start; }
            .bell { width: 100%; min-height: 42px; border: 1px solid rgba(219, 232, 248, 0.34); border-radius: 7px; background: rgba(255, 255, 255, 0.04); }
            .dropdown-menu { right: auto; left: 0; width: min(280px, calc(100vw - 32px)); }
            .content, .dashboard-content { padding: 12px; }
            .stat-card, .span-2, .span-3, .span-4, .span-5, .span-6, .span-8, .span-9, .span-12 { grid-column: span 12; }
            .building-overview, .donut-row, .form-grid { grid-template-columns: 1fr; }
            .monitor-grid, .quick-summary { grid-template-columns: 1fr; }
            .res-span-3, .res-span-4, .res-span-5, .res-span-6, .res-span-7, .res-span-8, .res-span-12 { grid-column: span 12; }
            .resident-mini-grid, .resident-benefits { grid-template-columns: 1fr; }
            .resident-page-head { align-items: stretch; flex-direction: column; }
            .resident-page-head .btn { width: 100%; }
            .resident-filter-panel { grid-template-columns: 1fr; }
            .resident-filter-field:first-child { grid-column: auto; }
            .resident-benefit-bar { grid-template-columns: 1fr; }
            .resident-benefit { border-right: 0; border-bottom: 1px solid rgba(255, 255, 255, 0.14); }
            .resident-benefit:last-child { border-bottom: 0; }
            .resident-modal-fields { grid-template-columns: 1fr; }
            .visitor-toolbar { align-items: stretch; flex-direction: column; }
            .visitor-toolbar-actions, .visitor-stat-strip, .visitor-action-buttons { justify-content: flex-start; }
            .visitor-chip { width: 100%; justify-content: center; }
            .visitor-tabs { width: 100%; }
            .visitor-tab { flex: 1 1 140px; }
            .visitor-action-rail { grid-template-columns: repeat(9, 168px); }
            .visitor-span-3, .visitor-span-4, .visitor-span-5, .visitor-span-7, .visitor-span-8, .visitor-span-9, .visitor-span-12 { grid-column: span 12; }
            .visitor-form-grid, .visitor-modal-grid, .visitor-integration, .visitor-report-grid, .visitor-donut-wrap { grid-template-columns: 1fr; }
            .visitor-list-row { grid-template-columns: 42px minmax(0, 1fr) auto; }
            .visitor-list-row .visitor-kebab { display: none; }
            .visitor-info-row { grid-template-columns: 1fr; gap: 3px; }
            .visitor-table-toolbar { align-items: stretch; flex-direction: column; }
            .visitor-table-filters, .visitor-table-filters select, .visitor-table-filters input { width: 100%; }
            .service-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-widget-grid { grid-template-columns: 1fr; }
            .service-attachment-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .community-workspace { grid-template-columns: 1fr; }
            .community-card-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .community-filter-bar { grid-template-columns: 1fr; }
            .community-event-row { grid-template-columns: 62px minmax(0, 1fr); }
            .community-event-row .community-thumb { display: none; }
            .community-donut-panel { grid-template-columns: 1fr; justify-items: center; text-align: center; }
            .tenant-workspace { grid-template-columns: 1fr; }
            .tenant-titlebar { align-items: stretch; flex-direction: column; }
            .tenant-actions { justify-content: flex-start; }
            .tenant-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .tenant-filter-bar,
            .tenant-form-grid,
            .tenant-form-grid.two { grid-template-columns: 1fr; }
            .package-hero,
            .package-hero-main,
            .package-modal-grid,
            .package-modal-grid.two,
            .package-benefits { grid-template-columns: 1fr; }
            .package-toolbar { align-items: stretch; }
            .package-toolbar-actions { justify-content: flex-start; }
            .package-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .benefit-cell { border-right: 0; border-bottom: 1px solid rgba(255, 255, 255, 0.12); }
            .benefit-cell:last-child { border-bottom: 0; }
            .summary-cell { border-right: 0; border-bottom: 1px solid rgba(255, 255, 255, 0.11); }
            .toolbar, .access-card header { align-items: stretch; flex-direction: column; }
            .actions { justify-content: flex-start; }
        }

        @media (max-width: 560px) {
            body { font-size: 12px; }
            .top-actions { grid-template-columns: 1fr; }
            .stat-card { grid-template-columns: 48px minmax(0, 1fr); min-height: 106px; padding: 14px; }
            .stat-icon { width: 48px; height: 48px; }
            .stat-value { font-size: 22px; }
            .building-scene, .map-scene { min-height: 190px; }
            .chart { height: 210px; padding-left: 22px; }
            .chart-grid { left: 22px; }
            .bars, .chart-labels { gap: 8px; }
            .resident-card-body { padding: 12px; }
            .resident-visual { min-height: 180px; }
            .resident-page-head h2 { font-size: 18px; }
            .resident-filter-panel { padding: 12px; }
            .resident-pagination { align-items: flex-start; flex-direction: column; }
            .resident-benefit { grid-template-columns: 38px minmax(0, 1fr); padding: 12px; }
            .resident-benefit-icon { width: 34px; height: 34px; }
            .visitor-content { padding: 12px; }
            .service-content { padding: 12px; }
            .community-content { padding: 12px; }
            .tenant-content { padding: 12px; }
            .package-content { padding: 12px; }
            .visitor-toolbar { padding-top: 0; }
            .visitor-heading { grid-template-columns: 34px minmax(0, 1fr); }
            .visitor-heading h2 { font-size: 16px; }
            .visitor-panel-body, .visitor-panel-head { padding-inline: 12px; }
            .visitor-chart { padding: 12px; }
            .visitor-line-chart { height: 210px; }
            .visitor-chart-labels { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .visitor-modal { padding: 10px; }
            .visitor-modal-dialog { max-height: calc(100vh - 20px); }
            .service-metrics { grid-template-columns: 1fr; }
            .service-metric { min-height: 64px; }
            .service-photo-pair, .service-attachment-row { grid-template-columns: 1fr; }
            .community-card-row { grid-template-columns: 1fr; }
            .community-row { grid-template-columns: 46px minmax(0, 1fr); }
            .community-row > .visitor-action-buttons { grid-column: 1 / -1; justify-content: flex-start; }
            .tenant-titlebar h2 { font-size: 20px; }
            .tenant-metrics { grid-template-columns: 1fr; }
            .tenant-location-grid { grid-template-columns: 1fr; }
            .tenant-form-footer { align-items: stretch; flex-direction: column; }
            .tenant-form-footer .tenant-actions,
            .tenant-form-footer .btn { width: 100%; }
            .package-hero { padding: 16px; }
            .package-hero-icon { width: 78px; height: 78px; }
            .package-hero-copy h2 { font-size: 22px; }
            .package-metrics { grid-template-columns: 1fr; }
            .package-panel-head,
            .package-toolbar { padding-inline: 14px; }
            .package-modal { padding: 10px; }
            .package-modal-dialog { max-height: calc(100vh - 20px); }
            .package-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .package-step::after { display: none; }
            .package-modal-head,
            .package-modal-body { padding-inline: 14px; }
            th, td { padding: 11px 10px; }
        }
    </style>
</head>
<body>
    @php
        $authUser = auth()->user();
        $canUsersRead = $authUser?->canAccessModule('users', 'read');
        $canRolesRead = $authUser?->canAccessModule('roles', 'read');
        $canModulesRead = $authUser?->canAccessModule('modules', 'read');
        $canResidentRead = $authUser?->canAccessModule('resident-management', 'read');
        $canVisitorRead = $authUser?->canAccessModule('visitor-management', 'read');
        $canServiceRead = $authUser?->canAccessModule('service-request', 'read');
        $canCommunityRead = $authUser?->canAccessModule('community-management', 'read');
        $canTenantRead = $authUser?->canAccessModule('tenant-marketplace', 'read');
        $canPackageRead = $authUser?->canAccessModule('package-center', 'read');
    @endphp

    <div class="sidebar-overlay" data-sidebar-close></div>

    <div class="ops-shell" id="opsShell">
        <aside class="ops-sidebar">
            <div class="brand-block">
                <svg class="brand-tower" viewBox="0 0 64 72" fill="none" aria-hidden="true">
                    <path d="M8 64V27l11-7v44M19 64V9l13 8v47M32 64V18l14 7v39M46 64V33l10 6v25M4 64h56" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M25 25h4M25 35h4M25 45h4M39 35h4M39 45h4M13 36h3M13 46h3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <div>
                    <span class="brand-name">APARTHUB</span>
                    <span class="brand-sub">Management Suite</span>
                </div>
            </div>

            <div class="sidebar-scroll">
                <nav class="side-nav" aria-label="Navigasi utama">
                    <a href="{{ route('dashboard') }}" title="Dashboard" @class(['side-link', 'active' => request()->routeIs('dashboard')])>
                        <span class="side-icon">
                            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/><path d="M9 20v-6h6v6"/></svg>
                        </span>
                        <span>Dashboard</span>
                    </a>

                    @if ($canResidentRead)
                        <div @class(['side-section', 'is-open' => request()->routeIs('resident-management.*')])>
                            <button type="button" title="Resident Management" data-sidebar-toggle @class(['side-parent', 'active' => request()->routeIs('resident-management.*')]) aria-expanded="{{ request()->routeIs('resident-management.*') ? 'true' : 'false' }}">
                                <span class="side-icon">
                                    <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                                </span>
                                <span>Resident Management</span>
                                <span class="side-caret">v</span>
                            </button>
                            <div class="side-sub">
                                <a href="{{ route('resident-management.residents') }}" @class(['active' => request()->routeIs('resident-management.residents')])>Residents</a>
                                <a href="{{ route('resident-management.units') }}" @class(['active' => request()->routeIs('resident-management.units')])>Unit Management</a>
                                <a href="{{ route('resident-management.move-in-out') }}" @class(['active' => request()->routeIs('resident-management.move-in-out')])>Move In / Out</a>
                                <a href="{{ route('resident-management.family-members') }}" @class(['active' => request()->routeIs('resident-management.family-members')])>Family Member</a>
                                <a href="{{ route('resident-management.vehicles') }}" @class(['active' => request()->routeIs('resident-management.vehicles')])>Vehicle Management</a>
                            </div>
                        </div>
                    @endif

                    <div class="side-section">
                        <button type="button" class="side-parent" title="Billing & Finance" data-sidebar-toggle aria-expanded="false">
                            <span class="side-icon">
                                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h12v20H6z"/><path d="M9 6h6M9 10h6M9 14h3"/></svg>
                            </span>
                            <span>Billing & Finance</span>
                            <span class="side-caret">v</span>
                        </button>
                        <div class="side-sub">
                            <a href="#">Invoice Management</a>
                            <a href="#">Collection & Payment</a>
                            <a href="#">Late Fee & Penalty</a>
                            <a href="#">Financial Report</a>
                        </div>
                    </div>

                    @if ($canVisitorRead)
                        <div @class(['side-section', 'is-open' => request()->routeIs('visitor-management.*')])>
                            <button type="button" title="Visitor Management" data-sidebar-toggle @class(['side-parent', 'active' => request()->routeIs('visitor-management.*')]) aria-expanded="{{ request()->routeIs('visitor-management.*') ? 'true' : 'false' }}">
                                <span class="side-icon">
                                    <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2M8 8a4 4 0 1 0 8 0" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span>Visitor Management</span>
                                <span class="side-caret">v</span>
                            </button>
                            <div class="side-sub">
                                <a href="{{ route('visitor-management.registration') }}" @class(['active' => request()->routeIs('visitor-management.registration')])>Visitor Registration</a>
                                <a href="{{ route('visitor-management.pending-approval') }}" @class(['active' => request()->routeIs('visitor-management.pending-approval')])>Pending Approval</a>
                                <a href="{{ route('visitor-management.expected-visitors') }}" @class(['active' => request()->routeIs('visitor-management.expected-visitors')])>Expected Visitors</a>
                                <a href="{{ route('visitor-management.check-in-out') }}" @class(['active' => request()->routeIs('visitor-management.check-in-out')])>Check-In / Check-Out</a>
                                <a href="{{ route('visitor-management.history') }}" @class(['active' => request()->routeIs('visitor-management.history')])>Visitor History</a>
                                <a href="{{ route('visitor-management.vehicles') }}" @class(['active' => request()->routeIs('visitor-management.vehicles')])>Vehicle Visitor</a>
                                <a href="{{ route('visitor-management.blacklist') }}" @class(['active' => request()->routeIs('visitor-management.blacklist')])>Blacklist Management</a>
                                <a href="{{ route('visitor-management.reports') }}" @class(['active' => request()->routeIs('visitor-management.reports')])>Reports</a>
                            </div>
                        </div>
                    @endif

                    @if ($canServiceRead)
                        <div @class(['side-section', 'is-open' => request()->routeIs('service-request.*')])>
                            <button type="button" title="Service Request" data-sidebar-toggle @class(['side-parent', 'active' => request()->routeIs('service-request.*')]) aria-expanded="{{ request()->routeIs('service-request.*') ? 'true' : 'false' }}">
                                <span class="side-icon">
                                    <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11h6M9 15h6M8 3h8l2 3h2v15H4V6h2l2-3Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span>Service Request</span>
                                <span class="side-caret">v</span>
                            </button>
                            <div class="side-sub">
                                <a href="{{ route('service-request.ticket-queue') }}" @class(['active' => request()->routeIs('service-request.ticket-queue')])>Ticket Queue</a>
                                <a href="{{ route('service-request.new-request') }}" @class(['active' => request()->routeIs('service-request.new-request')])>New Request</a>
                                <a href="{{ route('service-request.assignment-board') }}" @class(['active' => request()->routeIs('service-request.assignment-board')])>Assignment Board</a>
                                <a href="{{ route('service-request.work-orders') }}" @class(['active' => request()->routeIs('service-request.work-orders')])>Work Orders</a>
                                <a href="{{ route('service-request.technician-schedule') }}" @class(['active' => request()->routeIs('service-request.technician-schedule')])>Technician Schedule</a>
                                <a href="{{ route('service-request.work-in-progress') }}" @class(['active' => request()->routeIs('service-request.work-in-progress')])>Work In Progress</a>
                                <a href="{{ route('service-request.completed-requests') }}" @class(['active' => request()->routeIs('service-request.completed-requests')])>Completed Requests</a>
                                <a href="{{ route('service-request.sla-monitoring') }}" @class(['active' => request()->routeIs('service-request.sla-monitoring')])>SLA Monitoring</a>
                                <a href="{{ route('service-request.service-history') }}" @class(['active' => request()->routeIs('service-request.service-history')])>Service History</a>
                                <a href="{{ route('service-request.settings') }}" @class(['active' => request()->routeIs('service-request.settings')])>Settings</a>
                            </div>
                        </div>
                    @endif

                    @foreach ([
                        ['Facility Management', 'M4 21V7l8-4 8 4v14M9 21v-7h6v7'],
                    ] as [$label, $path])
                        <a href="#" class="side-link" title="{{ $label }}">
                            <span class="side-icon">
                                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $path }}" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <span>{{ $label }}</span>
                        </a>
                    @endforeach

                    @if ($canPackageRead)
                        <a href="{{ route('package-center.index') }}" title="Package Center" @class(['side-link', 'active' => request()->routeIs('package-center.*')])>
                            <span class="side-icon">
                                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5ZM3 7l9 5 9-5M12 12v10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <span>Package Center</span>
                        </a>
                    @endif

                    @if ($canCommunityRead)
                        <div @class(['side-section', 'is-open' => request()->routeIs('community-management.*')])>
                            <button type="button" title="Community Management" data-sidebar-toggle @class(['side-parent', 'active' => request()->routeIs('community-management.*')]) aria-expanded="{{ request()->routeIs('community-management.*') ? 'true' : 'false' }}">
                                <span class="side-icon">
                                    <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 20a5 5 0 0 1 10 0M9 8a3 3 0 1 0 6 0 3 3 0 0 0-6 0M3 19a4 4 0 0 1 5-3.87M16 15.13A4 4 0 0 1 21 19" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span>Community Management</span>
                                <span class="side-caret">v</span>
                            </button>
                            <div class="side-sub">
                                <a href="{{ route('community-management.announcements') }}" @class(['active' => request()->routeIs('community-management.announcements')])>Announcement Center</a>
                                <a href="{{ route('community-management.events') }}" @class(['active' => request()->routeIs('community-management.events')])>Event Management</a>
                                <a href="{{ route('community-management.polling-survey') }}" @class(['active' => request()->routeIs('community-management.polling-survey')])>Polling &amp; Survey</a>
                                <a href="{{ route('community-management.forum') }}" @class(['active' => request()->routeIs('community-management.forum')])>Resident Forum</a>
                                <a href="{{ route('community-management.broadcasts') }}" @class(['active' => request()->routeIs('community-management.broadcasts')])>Broadcast Notification</a>
                                <a href="{{ route('community-management.programs') }}" @class(['active' => request()->routeIs('community-management.programs')])>Community Programs</a>
                                <a href="{{ route('community-management.calendar') }}" @class(['active' => request()->routeIs('community-management.calendar')])>Event Calendar</a>
                                <a href="{{ route('community-management.engagement') }}" @class(['active' => request()->routeIs('community-management.engagement')])>Resident Engagement</a>
                                <a href="{{ route('community-management.archive') }}" @class(['active' => request()->routeIs('community-management.archive')])>Community Archive</a>
                                <a href="{{ route('community-management.settings') }}" @class(['active' => request()->routeIs('community-management.settings')])>Community Settings</a>
                            </div>
                        </div>
                    @endif

                    @if ($canTenantRead)
                        <div @class(['side-section', 'is-open' => request()->routeIs('tenant-marketplace.*')])>
                            <button type="button" title="Tenant Marketplace" data-sidebar-toggle @class(['side-parent', 'active' => request()->routeIs('tenant-marketplace.*')]) aria-expanded="{{ request()->routeIs('tenant-marketplace.*') ? 'true' : 'false' }}">
                                <span class="side-icon">
                                    <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 9h14l-1 11H6L5 9ZM8 9a4 4 0 0 1 8 0M8 13h.01M12 13h.01M16 13h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span>Tenant Marketplace</span>
                                <span class="side-caret">v</span>
                            </button>
                            <div class="side-sub">
                                <a href="{{ route('tenant-marketplace.directory') }}" @class(['active' => request()->routeIs('tenant-marketplace.directory')])>Tenant Directory</a>
                                <a href="{{ route('tenant-marketplace.add-input') }}" @class(['active' => request()->routeIs('tenant-marketplace.add-input')])>Add / Input Tenant</a>
                            </div>
                        </div>
                    @endif

                    @foreach ([
                        ['Reports & Analytics', 'M4 19V5M8 19v-6M12 19V8M16 19v-9M20 19V4'],
                    ] as [$label, $path])
                        <a href="#" class="side-link" title="{{ $label }}">
                            <span class="side-icon">
                                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $path }}" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <span>{{ $label }}</span>
                        </a>
                    @endforeach

                    <div @class(['side-section', 'is-open' => request()->routeIs('roles.*') || request()->routeIs('modules.*')])>
                        <button type="button" title="System Settings" data-sidebar-toggle @class(['side-parent', 'active' => request()->routeIs('roles.*') || request()->routeIs('modules.*')]) aria-expanded="{{ request()->routeIs('roles.*') || request()->routeIs('modules.*') ? 'true' : 'false' }}">
                            <span class="side-icon">
                                <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.03.03a2 2 0 1 1-2.83 2.83l-.03-.03A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6V20a2 2 0 1 1-4 0v-.05a1.7 1.7 0 0 0-1-.6 1.7 1.7 0 0 0-1.88.34l-.03.03a2 2 0 1 1-2.83-2.83l.03-.03A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1H4a2 2 0 1 1 0-4h.05a1.7 1.7 0 0 0 .6-1 1.7 1.7 0 0 0-.34-1.88l-.03-.03a2 2 0 1 1 2.83-2.83l.03.03A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6V4a2 2 0 1 1 4 0v.05a1.7 1.7 0 0 0 1 .6 1.7 1.7 0 0 0 1.88-.34l.03-.03a2 2 0 1 1 2.83 2.83l-.03.03A1.7 1.7 0 0 0 19.4 9c.2.34.4.66.6 1H20a2 2 0 1 1 0 4h-.05a1.7 1.7 0 0 0-.55 1Z"/></svg>
                            </span>
                            <span>System Settings</span>
                            <span class="side-caret">v</span>
                        </button>
                        <div class="side-sub">
                            @if ($canUsersRead)
                                <a href="{{ route('users.index') }}" @class(['active' => request()->routeIs('users.*')])>Users</a>
                            @endif
                            @if ($canRolesRead)
                                <a href="{{ route('roles.index') }}" @class(['active' => request()->routeIs('roles.*')])>Roles</a>
                            @endif
                            @if ($canModulesRead)
                                <a href="{{ route('modules.index') }}" @class(['active' => request()->routeIs('modules.*')])>Modules</a>
                            @endif
                        </div>
                    </div>
                </nav>

                @if ($canPackageRead && request()->routeIs('package-center.*'))
                    <div class="sidebar-quick-actions" aria-label="Package center quick actions">
                        <div class="sidebar-quick-title">Quick Actions</div>
                        <a class="sidebar-quick-link" href="{{ route('package-center.index') }}">
                            <span>+</span><span>Register Package</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('package-center.index') }}">
                            <span>&gt;</span><span>Pickup Status</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('package-center.index') }}">
                            <span>#</span><span>Package Reports</span>
                        </a>
                    </div>
                @elseif ($canTenantRead && request()->routeIs('tenant-marketplace.*'))
                    <div class="sidebar-quick-actions" aria-label="Tenant marketplace quick actions">
                        <div class="sidebar-quick-title">Quick Actions</div>
                        <a class="sidebar-quick-link" href="{{ route('tenant-marketplace.add-input') }}">
                            <span>+</span><span>Add / Input Tenant</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('tenant-marketplace.directory') }}">
                            <span>#</span><span>Generate Report</span>
                        </a>
                    </div>
                @elseif ($canCommunityRead && request()->routeIs('community-management.*'))
                    <div class="sidebar-quick-actions" aria-label="Community management quick actions">
                        <div class="sidebar-quick-title">Quick Actions</div>
                        <a class="sidebar-quick-link" href="{{ route('community-management.announcements') }}">
                            <span>+</span><span>Create Announcement</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('community-management.events') }}">
                            <span>*</span><span>Create Event</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('community-management.broadcasts') }}">
                            <span>&gt;</span><span>Send Broadcast</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('community-management.polling-survey') }}">
                            <span>#</span><span>Create Polling</span>
                        </a>
                    </div>
                @elseif ($canServiceRead)
                    <div class="sidebar-quick-actions" aria-label="Service request quick actions">
                        <div class="sidebar-quick-title">Quick Actions</div>
                        <a class="sidebar-quick-link" href="{{ route('service-request.new-request') }}">
                            <span>+</span><span>Create New Request</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('service-request.assignment-board') }}">
                            <span>&gt;</span><span>Assign Technician</span>
                        </a>
                        <a class="sidebar-quick-link" href="{{ route('service-request.service-history') }}">
                            <span>#</span><span>Generate Report</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="sidebar-profile">
                <div class="profile-row">
                    <div class="avatar">{{ strtoupper(substr($authUser?->name ?? 'A', 0, 1)) }}</div>
                    <div>
                        <div class="profile-name">{{ $authUser?->name ?? 'Building Manager' }}</div>
                        <div class="profile-site">Aether Residences</div>
                        <div class="online-dot">Online</div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="ops-main">
            <header class="ops-topbar">
                <button class="menu-button" type="button" data-shell-toggle aria-label="Toggle sidebar" aria-expanded="true">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
                </button>

                <div class="top-title">
                    @hasSection('topbar_context')
                        <div class="top-context">@yield('topbar_context')</div>
                    @endif
                    <h1>Management Operations Center</h1>
                    <p>@yield('topbar_subtitle', 'Real-Time Property Operations Intelligence')</p>
                </div>

                <div class="top-actions">
                    <div class="top-dropdown" data-dropdown>
                        <button class="top-chip" type="button" data-dropdown-toggle aria-expanded="false">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V7l8-4 8 4v14M9 21v-8h6v8M8 8h.01M12 7h.01M16 8h.01"/></svg>
                            <span>Aether Residences</span>
                            <span class="dropdown-caret">v</span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="#">Aether Residences <span>Active</span></a>
                            <a href="#">North Garden Tower</a>
                            <a href="#">Skyline Suites</a>
                            <div class="dropdown-note">Property switcher uses static data for now.</div>
                        </div>
                    </div>

                    <div class="top-chip" aria-label="Selected date">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 2v4M17 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg>
                        <span>07 Jun 2026</span>
                    </div>

                    <div class="top-dropdown" data-dropdown>
                        <button class="bell" type="button" data-dropdown-toggle aria-label="Notifications" aria-expanded="false">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
                            <span class="bell-count">8</span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="#">Fire Alarm Test <span>09:30</span></a>
                            <a href="#">Elevator Maintenance <span>08:15</span></a>
                            <a href="#">Water Shutdown <span>Info</span></a>
                        </div>
                    </div>

                    <div class="top-dropdown" data-dropdown>
                        <button class="top-profile" type="button" data-dropdown-toggle aria-expanded="false">
                            <div class="avatar">{{ strtoupper(substr($authUser?->name ?? 'B', 0, 1)) }}</div>
                            <div>
                                <div class="welcome">Welcome,</div>
                                <div class="role-title">{{ $authUser?->role?->name ?? 'Building Manager' }}</div>
                            </div>
                            <span class="dropdown-caret">v</span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                            @if ($canUsersRead)
                                <a href="{{ route('users.index') }}">User Management</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <section @class([
                'content',
                'dashboard-content' => request()->routeIs('dashboard'),
                'resident-content' => request()->routeIs('resident-management.*'),
                'visitor-content' => request()->routeIs('visitor-management.*'),
                'service-content' => request()->routeIs('service-request.*'),
                'community-content' => request()->routeIs('community-management.*'),
                'tenant-content' => request()->routeIs('tenant-marketplace.*'),
                'package-content' => request()->routeIs('package-center.*'),
            ])>
                @if (session('status'))
                    <div class="alert success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <script>
        (() => {
            const shell = document.getElementById('opsShell');
            const sidebarButton = document.querySelector('[data-shell-toggle]');
            const sidebarCloseTargets = document.querySelectorAll('[data-sidebar-close]');
            const desktopQuery = window.matchMedia('(min-width: 981px)');

            const closeMobileSidebar = () => {
                document.body.classList.remove('is-mobile-sidebar-open');
                sidebarButton?.setAttribute('aria-expanded', desktopQuery.matches && !shell?.classList.contains('is-sidebar-collapsed') ? 'true' : 'false');
            };

            const openMobileSidebar = () => {
                document.body.classList.add('is-mobile-sidebar-open');
                sidebarButton?.setAttribute('aria-expanded', 'true');
            };

            const syncSidebarFromStorage = () => {
                if (!shell || !desktopQuery.matches) {
                    shell?.classList.remove('is-sidebar-collapsed');
                    return;
                }

                const collapsed = localStorage.getItem('aparthub.sidebarCollapsed') === 'true';
                shell.classList.toggle('is-sidebar-collapsed', collapsed);
                sidebarButton?.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            };

            sidebarButton?.addEventListener('click', () => {
                if (!shell) return;

                if (desktopQuery.matches) {
                    const collapsed = shell.classList.toggle('is-sidebar-collapsed');
                    localStorage.setItem('aparthub.sidebarCollapsed', collapsed ? 'true' : 'false');
                    sidebarButton.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    document.body.classList.remove('is-mobile-sidebar-open');
                    return;
                }

                document.body.classList.contains('is-mobile-sidebar-open') ? closeMobileSidebar() : openMobileSidebar();
            });

            sidebarCloseTargets.forEach((target) => target.addEventListener('click', closeMobileSidebar));

            document.querySelectorAll('.ops-sidebar a[href]').forEach((link) => {
                link.addEventListener('click', () => {
                    if (!desktopQuery.matches) {
                        closeMobileSidebar();
                    }
                });
            });

            document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const section = button.closest('.side-section');
                    const isOpen = section?.classList.toggle('is-open') ?? false;
                    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            });

            const closeDropdowns = (except = null) => {
                document.querySelectorAll('[data-dropdown].is-open').forEach((dropdown) => {
                    if (dropdown === except) return;
                    dropdown.classList.remove('is-open');
                    dropdown.querySelector('[data-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
                });
            };

            document.querySelectorAll('[data-dropdown-toggle]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const dropdown = button.closest('[data-dropdown]');
                    const isOpen = dropdown?.classList.contains('is-open');
                    closeDropdowns(dropdown);
                    dropdown?.classList.toggle('is-open', !isOpen);
                    button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                });
            });

            const closeModals = () => {
                document.querySelectorAll('.visitor-modal.is-open').forEach((modal) => {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                });
                document.body.classList.remove('is-modal-open');
            };

            document.querySelectorAll('[data-modal-open]').forEach((button) => {
                button.addEventListener('click', () => {
                    const modal = document.getElementById(button.dataset.modalOpen);
                    if (!modal) return;

                    closeModals();
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('is-modal-open');
                    modal.querySelector('[data-modal-close]')?.focus();
                });
            });

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', closeModals);
            });

            document.querySelectorAll('.visitor-modal').forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal || event.target?.hasAttribute?.('data-modal-close')) {
                        closeModals();
                    }
                });
            });

            document.addEventListener('click', () => closeDropdowns());
            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                closeDropdowns();
                closeModals();
                closeMobileSidebar();
            });

            desktopQuery.addEventListener('change', () => {
                closeMobileSidebar();
                syncSidebarFromStorage();
            });

            syncSidebarFromStorage();
        })();
    </script>
</body>
</html>

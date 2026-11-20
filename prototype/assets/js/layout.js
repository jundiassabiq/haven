/* Haven prototype — shared layout.
   Loaded as a blocking script at the top of <body> so header, sidebar, and footer render without a flash.
   In the PHP build (Task 1) these become partials in app/views. */
(function () {
  "use strict";

  const script = document.currentScript;
  const base = new URL("../../", script.src).href;
  const u = (p) => base + p;
  const layout = script.dataset.layout || "public";
  const active = script.dataset.active || "";
  const role = script.dataset.role || "";

  /* ---------- Icon sprite (24px grid, 2px stroke, round caps) ---------- */
  const ICONS = {
    "arrow-right": '<path d="M5 12h14M13 6l6 6-6 6"/>',
    "arrow-left": '<path d="M19 12H5M11 6l-6 6 6 6"/>',
    menu: '<path d="M3 7h18M3 12h18M3 17h18"/>',
    close: '<path d="M6 6l12 12M18 6L6 18"/>',
    exit: '<path d="M14 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/><path d="M10 17l-5-5 5-5M5 12h11"/>',
    bell: '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10.3 20a2 2 0 0 0 3.4 0"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    users: '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5a6.5 6.5 0 0 1 3.5 5.5"/>',
    home: '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/>',
    file: '<path d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>',
    "file-plus": '<path d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8z"/><path d="M14 3v5h5M12 11v6M9 14h6"/>',
    inbox: '<path d="M3 13h5l2 3h4l2-3h5"/><path d="M5 5h14l2 8v6H3v-6z"/>',
    search: '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
    calendar: '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    message: '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"/>',
    book: '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M4 19V5M19 19v2H6"/>',
    pin: '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
    chart: '<path d="M4 20V4M4 20h16"/><path d="M8 16v-5M12 16V8M16 16v-3"/>',
    shield: '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/>',
    "shield-check": '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>',
    lock: '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
    check: '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
    "check-circle": '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/>',
    "x-circle": '<circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>',
    clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    phone: '<path d="M5 3h3l2 5-2.5 1.5a11 11 0 0 0 7 7L16 14l5 2v3a2 2 0 0 1-2 2A17 17 0 0 1 3 5a2 2 0 0 1 2-2"/>',
    mail: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
    upload: '<path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/>',
    copy: '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/>',
    eye: '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
    "eye-off": '<path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.6A17 17 0 0 0 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>',
    plus: '<path d="M12 5v14M5 12h14"/>',
    edit: '<path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M14 6l4 4"/>',
    trash: '<path d="M4 7h16M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13M9 7V4h6v3"/>',
    list: '<path d="M9 6h11M9 12h11M9 18h11"/><path d="M4 6h.01M4 12h.01M4 18h.01"/>',
    settings: '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
    alert: '<path d="M12 3l10 18H2z"/><path d="M12 10v4M12 17.5h.01"/>',
    info: '<circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5h.01"/>',
    "chevron-down": '<path d="M6 9l6 6 6-6"/>',
    "chevron-right": '<path d="M9 6l6 6-6 6"/>',
    send: '<path d="M21 3L10 14"/><path d="M21 3l-7 18-4-7-7-4z"/>',
    external: '<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
    heart: '<path d="M12 20s-8-4.7-8-10.5A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8 2.5C20 15.3 12 20 12 20z"/>',
    building: '<path d="M4 21V5a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v16M15 9h4a1 1 0 0 1 1 1v11M2 21h20"/><path d="M8 8h3M8 12h3M8 16h3"/>',
    log: '<path d="M8 3h8l4 4v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M8 11h8M8 15h5"/><circle cx="17" cy="17" r="0"/>',
    paperclip: '<path d="M20 11.5l-8.5 8.5a5 5 0 0 1-7-7L13 4.5a3.3 3.3 0 0 1 4.7 4.7L9.2 17.7a1.7 1.7 0 0 1-2.4-2.4L15 7"/>',
    download: '<path d="M12 4v12M7 11l5 5 5-5"/><path d="M4 20h16"/>',
    video: '<rect x="3" y="6" width="13" height="12" rx="2"/><path d="M16 10l5-3v10l-5-3"/>',
    refresh: '<path d="M20 11a8 8 0 0 0-14.3-4.9L4 8"/><path d="M4 3v5h5M4 13a8 8 0 0 0 14.3 4.9L20 16"/><path d="M20 21v-5h-5"/>',
    globe: '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
  };
  const sprite =
    '<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">' +
    Object.entries(ICONS).map(([k, v]) => `<symbol id="i-${k}" viewBox="0 0 24 24">${v}</symbol>`).join("") +
    "</svg>";
  const icon = (n, cls = "icon") => `<svg class="${cls}" aria-hidden="true"><use href="#i-${n}"/></svg>`;

  const cur = (key) => (key === active ? ' aria-current="page"' : "");

  /* ---------- Public header ---------- */
  const PUBLIC_NAV = [
    ["lapor", "lapor.html", "Lapor"],
    ["lacak", "lacak.html", "Lacak laporan"],
    ["edukasi", "edukasi.html", "Edukasi"],
    ["layanan", "layanan.html", "Layanan bantuan"],
    ["tentang", "tentang.html", "Tentang"],
  ];
  const navLinks = PUBLIC_NAV.map(([k, h, t]) => `<li><a href="${u(h)}"${cur(k)}>${t}</a></li>`).join("");

  const publicHeader = `
  <a class="skip-link" href="#main">Lewati ke konten</a>
  <header class="site-header" data-header>
    <div class="container header-inner">
      <a class="wordmark" href="${u("beranda.html")}" aria-label="Haven, ke beranda">haven</a>
      <nav class="primary-nav" aria-label="Utama"><ul>${navLinks}</ul></nav>
      <div class="header-actions">
        <a class="link-quiet" href="${u("masuk.html")}">Masuk</a>
        <a class="btn btn-yellow btn-sm" href="${u("lapor.html")}">Buat laporan</a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav" data-menu-toggle>
          <span class="sr-only">Buka menu</span>
          ${icon("menu", "icon icon-menu")}${icon("close", "icon icon-close")}
        </button>
      </div>
    </div>
    <nav class="mobile-nav" id="mobile-nav" aria-label="Menu seluler" hidden data-mobile-nav>
      <ul>${navLinks}<li><a href="${u("masuk.html")}">Masuk</a></li></ul>
      <a class="btn btn-yellow btn-block" href="${u("lapor.html")}">Buat laporan</a>
    </nav>
  </header>`;

  /* ---------- Public footer ---------- */
  const footer = `
  <footer class="site-footer">
    <div class="container">
      <div class="footer-top">
        <a class="wordmark wordmark-light" href="${u("beranda.html")}" aria-label="Haven, ke beranda">haven</a>
        <p class="footer-emergency">Dalam bahaya sekarang? Hubungi <a href="tel:112" class="mono">112</a> atau SAPA <a href="tel:129" class="mono">129</a>.</p>
      </div>
      <div class="footer-cols">
        <nav aria-label="Layanan"><h2 class="footer-h">Layanan</h2><ul>
          <li><a href="${u("lapor.html")}">Buat laporan</a></li>
          <li><a href="${u("lacak.html")}">Lacak laporan</a></li>
          <li><a href="${u("layanan.html")}">Direktori layanan</a></li>
        </ul></nav>
        <nav aria-label="Belajar"><h2 class="footer-h">Belajar</h2><ul>
          <li><a href="${u("edukasi.html")}">Edukasi</a></li>
          <li><a href="${u("tips-aman.html")}">Tips menjelajah aman</a></li>
        </ul></nav>
        <nav aria-label="Tentang"><h2 class="footer-h">Tentang</h2><ul>
          <li><a href="${u("tentang.html")}">Tentang &amp; SDGs 5</a></li>
          <li><a href="${u("masuk.html")}">Masuk</a></li>
          <li><a href="${u("daftar.html")}">Daftar</a></li>
        </ul></nav>
        <div><h2 class="footer-h">Kerahasiaan</h2>
          <p class="footer-note">Isi laporan dan pesan dienkripsi. Hanya pendamping yang ditugaskan yang bisa membacanya.</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>© 2026 Haven. Proyek Responsi Pemrograman Web I, Informatika Unsoed.</p>
        <p>Haven tidak menggantikan layanan darurat.</p>
      </div>
    </div>
  </footer>`;

  /* ---------- App (per role) ---------- */
  const ROLES = {
    penyintas: {
      label: "Penyintas",
      name: "Rani",
      initials: "R",
      groups: [
        ["Utama", [
          ["dashboard", "penyintas/dashboard.html", "home", "Beranda saya"],
          ["laporan", "penyintas/laporan.html", "file", "Laporan saya"],
          ["jadwal", "penyintas/jadwal.html", "calendar", "Jadwal sesi"],
          ["notifikasi", "penyintas/notifikasi.html", "bell", "Notifikasi", "3"],
          ["profil", "penyintas/profil.html", "settings", "Profil &amp; keamanan"],
        ]],
        ["Bantuan", [
          ["lapor", "lapor.html", "file-plus", "Buat laporan baru"],
          ["edukasi", "edukasi.html", "book", "Edukasi"],
          ["layanan", "layanan.html", "pin", "Layanan bantuan"],
        ]],
      ],
      notifs: [
        ["Pesan baru dari pendamping", "HV-7Q4K2M · 10 menit lalu", "penyintas/kasus.html", true],
        ["Sesi Kamis, 26 Nov dikonfirmasi", "HV-7Q4K2M · 2 jam lalu", "penyintas/jadwal.html", true],
        ["Status laporan: Didampingi", "HV-7Q4K2M · kemarin", "penyintas/kasus.html", true],
      ],
      profile: "penyintas/profil.html",
    },
    pendamping: {
      label: "Pendamping",
      name: "Dewi Anggraini",
      initials: "DA",
      groups: [
        ["Kerja", [
          ["dashboard", "pendamping/dashboard.html", "home", "Ringkasan"],
          ["kasus", "pendamping/kasus.html", "inbox", "Kasus saya", "4"],
          ["slot", "pendamping/slot.html", "calendar", "Jadwal &amp; slot"],
          ["notifikasi", "pendamping/notifikasi.html", "bell", "Notifikasi", "2"],
        ]],
        ["Rujukan", [
          ["layanan", "layanan.html", "pin", "Direktori layanan"],
          ["edukasi", "edukasi.html", "book", "Materi edukasi"],
        ]],
      ],
      notifs: [
        ["Kasus baru ditugaskan: HV-3T8N5R", "Oleh Admin · 25 menit lalu", "pendamping/kasus-detail.html", true],
        ["Pesan baru di HV-7Q4K2M", "Penyintas · 40 menit lalu", "pendamping/kasus-detail.html", true],
        ["Slot Kamis 10.00 dipesan", "HV-7Q4K2M · 2 jam lalu", "pendamping/slot.html", false],
      ],
      profile: "pendamping/dashboard.html",
    },
    admin: {
      label: "Admin",
      name: "Admin Haven",
      initials: "AH",
      groups: [
        ["Kasus", [
          ["dashboard", "admin/dashboard.html", "chart", "Statistik"],
          ["antrean", "admin/antrean.html", "inbox", "Antrean kasus", "3"],
          ["pengguna", "admin/pengguna.html", "users", "Pengguna"],
        ]],
        ["Konten", [
          ["artikel", "admin/artikel.html", "book", "Artikel"],
          ["layanan", "admin/layanan.html", "building", "Layanan"],
        ]],
        ["Keamanan", [
          ["audit", "admin/audit.html", "log", "Audit log"],
        ]],
      ],
      notifs: [
        ["Laporan baru: HV-9B2X6P (urgensi tinggi)", "Anonim · 5 menit lalu", "admin/kasus.html", true],
        ["Laporan baru: HV-6W1J8S", "Akun · 1 jam lalu", "admin/antrean.html", true],
        ["Rujukan dibuat untuk HV-5M7C1D", "Dewi Anggraini · kemarin", "admin/kasus.html", false],
      ],
      profile: "admin/dashboard.html",
    },
  };

  function appHeader(r) {
    const R = ROLES[r];
    const unread = R.notifs.filter((n) => n[3]).length;
    const list = R.notifs
      .map(([t, m, h, un]) => `<li><a href="${u(h)}" class="${un ? "is-unread" : ""}"><span class="notif-dot" aria-hidden="true"></span><span><span class="notif-title">${t}${un ? '<span class="sr-only"> (belum dibaca)</span>' : ""}</span><span class="notif-meta">${m}</span></span></a></li>`)
      .join("");
    const notifPage = r === "admin" ? "admin/audit.html" : `${r}/notifikasi.html`;
    return `
  <a class="skip-link" href="#main">Lewati ke konten</a>
  <header class="app-header">
    <div class="app-header-inner">
      <button class="icon-btn sidebar-toggle" type="button" aria-expanded="false" aria-controls="sidebar" data-sidebar-toggle>
        ${icon("menu")}<span class="sr-only">Buka navigasi</span>
      </button>
      <a class="wordmark" href="${u(R.groups[0][1][0][1])}" aria-label="Haven, ke beranda ${R.label.toLowerCase()}">haven</a>
      <span class="role-chip">${R.label}</span>
      <div class="app-header-actions">
        <div class="dropdown" data-dropdown>
          <button class="icon-btn" type="button" aria-expanded="false" aria-controls="notif-panel" data-dropdown-toggle>
            ${icon("bell")}<span class="count" aria-hidden="true">${unread}</span><span class="sr-only">Notifikasi, ${unread} belum dibaca</span>
          </button>
          <div class="dropdown-panel" id="notif-panel" hidden>
            <div class="dropdown-head"><strong>Notifikasi</strong><button type="button" class="link-btn" data-mark-read>Tandai semua dibaca</button></div>
            <ul class="notif-list">${list}</ul>
            <a class="dropdown-foot" href="${u(notifPage)}">${r === "admin" ? "Buka audit log" : "Lihat semua notifikasi"} ${icon("arrow-right")}</a>
          </div>
        </div>
        <div class="dropdown" data-dropdown>
          <button class="user-btn" type="button" aria-expanded="false" aria-controls="user-panel" data-dropdown-toggle>
            <span class="avatar" aria-hidden="true">${R.initials}</span><span class="user-name">${R.name}</span>${icon("chevron-down")}
          </button>
          <div class="dropdown-panel dropdown-sm" id="user-panel" hidden>
            <ul class="menu-list">
              <li><a href="${u(R.profile)}">${icon("settings")} Profil &amp; keamanan</a></li>
              <li><a href="${u("beranda.html")}">${icon("globe")} Situs publik</a></li>
              <li><a href="${u("masuk.html")}">${icon("exit")} Keluar dari akun</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>`;
  }

  function sidebar(r) {
    const R = ROLES[r];
    const groups = R.groups
      .map(([label, items]) => `<div class="nav-group"><p class="nav-label">${label}</p><ul>${items
        .map(([k, h, ic, t, c]) => `<li><a class="nav-item" href="${u(h)}"${cur(k)}>${icon(ic)}<span>${t}</span>${c ? `<span class="nav-count">${c}</span>` : ""}</a></li>`)
        .join("")}</ul></div>`)
      .join("");
    return `<aside class="sidebar" id="sidebar" aria-label="Navigasi ${R.label.toLowerCase()}" data-sidebar>${groups}
      <div class="sidebar-foot"><p>${icon("lock")} Sesi otomatis berakhir setelah 30 menit tanpa aktivitas.</p></div>
    </aside><div class="sidebar-scrim" data-sidebar-scrim hidden></div>`;
  }

  /* ---------- Quick exit: Esc twice ---------- */
  const NEUTRAL = "https://www.google.com/search?q=prakiraan+cuaca+hari+ini";
  function quickExit() {
    try { sessionStorage.clear(); } catch (e) {}
    document.body.style.visibility = "hidden";
    window.location.replace(NEUTRAL);
  }

  let lastEsc = 0;
  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    const now = Date.now();
    if (now - lastEsc < 600) quickExit();
    lastEsc = now;
  });


  /* ---------- Render ---------- */
  const top = sprite + (layout === "app" ? appHeader(role) : publicHeader);
  script.insertAdjacentHTML("afterend", top);
  if (layout === "app") document.body.classList.add("app-body");

  window.HavenLayout = {
    footer() { document.currentScript.insertAdjacentHTML("afterend", footer); },
    sidebar() { document.currentScript.insertAdjacentHTML("afterend", sidebar(role)); },
    icon,
    base: u,
  };
})();

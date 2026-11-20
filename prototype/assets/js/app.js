/* Haven prototype — page behaviour. All data here is dummy; the PHP build replaces it with fetch() calls. */
(() => {
  "use strict";
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const icon = (n) => (window.HavenLayout ? HavenLayout.icon(n) : "");
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));

  /* ---------- Toast ---------- */
  let region;
  function toast(msg) {
    if (!region) {
      region = document.createElement("div");
      region.className = "toast-region";
      region.setAttribute("role", "status");
      region.setAttribute("aria-live", "polite");
      document.body.appendChild(region);
    }
    const t = document.createElement("div");
    t.className = "toast";
    t.innerHTML = `${icon("check-circle")}<span>${esc(msg)}</span>`;
    region.appendChild(t);
    setTimeout(() => t.remove(), 4200);
  }
  window.HavenToast = toast;
  $$("[data-toast]").forEach((b) => b.addEventListener("click", () => toast(b.dataset.toast)));

  /* ---------- Missing image frames ---------- */
  $$(".media img").forEach((img) => {
    const f = img.closest(".media");
    const mark = () => f.classList.add("is-missing");
    if (img.complete && img.naturalWidth === 0) mark();
    img.addEventListener("error", mark, { once: true });
  });

  /* ---------- Public header ---------- */
  const header = $("[data-header]");
  if (header) {
    const onScroll = () => header.classList.toggle("is-scrolled", window.scrollY > 8);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }
  const toggle = $("[data-menu-toggle]");
  const mobileNav = $("[data-mobile-nav]");
  if (toggle && mobileNav) {
    const set = (open) => {
      toggle.setAttribute("aria-expanded", String(open));
      toggle.querySelector(".sr-only").textContent = open ? "Tutup menu" : "Buka menu";
      mobileNav.hidden = !open;
      document.body.style.overflow = open ? "hidden" : "";
    };
    toggle.addEventListener("click", () => set(toggle.getAttribute("aria-expanded") !== "true"));
    mobileNav.addEventListener("click", (e) => { if (e.target.closest("a")) set(false); });
    window.matchMedia("(min-width: 961px)").addEventListener("change", (e) => { if (e.matches) set(false); });
  }

  /* ---------- App sidebar (off-canvas below 1024px) ---------- */
  const sbToggle = $("[data-sidebar-toggle]");
  const sidebar = $("[data-sidebar]");
  const scrim = $("[data-sidebar-scrim]");
  if (sbToggle && sidebar) {
    const mq = window.matchMedia("(max-width: 1024px)");
    const set = (open) => {
      sbToggle.setAttribute("aria-expanded", String(open));
      sbToggle.querySelector(".sr-only").textContent = open ? "Tutup navigasi" : "Buka navigasi";
      sidebar.classList.toggle("is-open", open);
      scrim.hidden = !open;
      if (mq.matches) sidebar.inert = !open; else sidebar.inert = false;
      if (open) sidebar.querySelector("a")?.focus();
    };
    set(false);
    sbToggle.addEventListener("click", () => set(!sidebar.classList.contains("is-open")));
    scrim.addEventListener("click", () => set(false));
    mq.addEventListener("change", () => set(false));
    document.addEventListener("keydown", (e) => { if (e.key === "Escape" && sidebar.classList.contains("is-open")) { set(false); sbToggle.focus(); } });
  }

  /* ---------- Dropdowns ---------- */
  $$("[data-dropdown]").forEach((dd) => {
    const btn = $("[data-dropdown-toggle]", dd);
    const panel = document.getElementById(btn.getAttribute("aria-controls"));
    const set = (open) => { btn.setAttribute("aria-expanded", String(open)); panel.hidden = !open; };
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      const open = panel.hidden;
      $$("[data-dropdown-toggle][aria-expanded='true']").forEach((b) => { b.setAttribute("aria-expanded", "false"); document.getElementById(b.getAttribute("aria-controls")).hidden = true; });
      set(open);
    });
    document.addEventListener("click", (e) => { if (!dd.contains(e.target)) set(false); });
    dd.addEventListener("keydown", (e) => { if (e.key === "Escape" && !panel.hidden) { e.stopPropagation(); set(false); btn.focus(); } });
  });
  $$("[data-mark-read]").forEach((b) => b.addEventListener("click", () => {
    const panel = b.closest(".dropdown-panel");
    $$(".is-unread", panel).forEach((a) => a.classList.remove("is-unread"));
    const count = b.closest(".dropdown").querySelector(".count");
    if (count) count.remove();
    toast("Semua notifikasi ditandai sudah dibaca.");
  }));

  /* ---------- Tabs (WAI-ARIA) ---------- */
  $$("[data-tabs]").forEach((root) => {
    const tabs = $$('[role="tab"]', root).filter((t) => t.closest("[data-tabs]") === root);
    const select = (tab, focus) => {
      tabs.forEach((t) => {
        const on = t === tab;
        t.setAttribute("aria-selected", String(on));
        t.tabIndex = on ? 0 : -1;
        document.getElementById(t.getAttribute("aria-controls")).hidden = !on;
      });
      if (focus) tab.focus();
      root.dispatchEvent(new CustomEvent("tabchange", { detail: tab }));
    };
    tabs.forEach((tab, i) => {
      tab.addEventListener("click", () => select(tab, false));
      tab.addEventListener("keydown", (e) => {
        const map = { ArrowRight: i + 1, ArrowLeft: i - 1, Home: 0, End: tabs.length - 1 };
        if (!(e.key in map)) return;
        e.preventDefault();
        select(tabs[(map[e.key] + tabs.length) % tabs.length], true);
      });
    });
    const hash = location.hash.slice(1);
    const fromHash = hash && tabs.find((t) => t.getAttribute("aria-controls") === hash);
    if (fromHash) select(fromHash, false);
  });
  $$("[data-open-tab]").forEach((a) => a.addEventListener("click", (e) => {
    const tab = $(`[role="tab"][aria-controls="${a.dataset.openTab}"]`);
    if (tab) { e.preventDefault(); tab.click(); tab.scrollIntoView({ block: "start", behavior: reduce ? "auto" : "smooth" }); }
  }));

  /* ---------- Counters ---------- */
  const counters = $$("[data-count]");
  if (counters.length && !reduce && "IntersectionObserver" in window) {
    const io = new IntersectionObserver((entries) => entries.forEach((en) => {
      if (!en.isIntersecting) return;
      io.unobserve(en.target);
      const el = en.target, target = Number(el.dataset.count), start = performance.now();
      const tick = (now) => {
        const p = Math.min((now - start) / 1800, 1);
        el.textContent = Math.round(target * (p === 1 ? 1 : 1 - Math.pow(2, -10 * p))).toLocaleString("id-ID");
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    }), { threshold: 0.6 });
    counters.forEach((c) => io.observe(c));
  }

  /* ---------- Copy & download ---------- */
  $$("[data-copy]").forEach((b) => b.addEventListener("click", async () => {
    const text = b.dataset.copy;
    try { await navigator.clipboard.writeText(text); } catch (e) {
      const ta = Object.assign(document.createElement("textarea"), { value: text });
      document.body.appendChild(ta); ta.select(); document.execCommand("copy"); ta.remove();
    }
    const label = b.querySelector("span");
    const old = label.textContent;
    label.textContent = "Tersalin";
    setTimeout(() => (label.textContent = old), 1800);
  }));
  $$("[data-download]").forEach((b) => b.addEventListener("click", () => {
    const blob = new Blob([b.dataset.download.replace(/\\n/g, "\n")], { type: "text/plain" });
    const a = Object.assign(document.createElement("a"), { href: URL.createObjectURL(blob), download: b.dataset.filename || "catatan.txt" });
    a.click();
    URL.revokeObjectURL(a.href);
  }));

  /* ---------- Password reveal ---------- */
  $$("[data-reveal]").forEach((b) => b.addEventListener("click", () => {
    const input = document.getElementById(b.dataset.reveal);
    const show = input.type === "password";
    input.type = show ? "text" : "password";
    b.setAttribute("aria-pressed", String(show));
    b.innerHTML = `${icon(show ? "eye-off" : "eye")}<span class="sr-only">${show ? "Sembunyikan" : "Tampilkan"}</span>`;
  }));

  /* ---------- Uppercase code input ---------- */
  $$("[data-uppercase]").forEach((i) => i.addEventListener("input", () => {
    const p = i.selectionStart;
    i.value = i.value.toUpperCase().replace(/\s/g, "");
    i.setSelectionRange(p, p);
  }));
  $$("[data-digits]").forEach((i) => i.addEventListener("input", () => { i.value = i.value.replace(/\D/g, "").slice(0, Number(i.maxLength) || 6); }));

  /* ---------- Character counters ---------- */
  $$("textarea[maxlength][data-counter]").forEach((ta) => {
    const out = document.getElementById(ta.dataset.counter);
    const max = Number(ta.maxLength);
    const upd = () => {
      out.textContent = `${ta.value.length.toLocaleString("id-ID")} / ${max.toLocaleString("id-ID")}`;
      out.classList.toggle("is-near", ta.value.length > max * 0.9);
    };
    ta.addEventListener("input", upd); upd();
  });

  /* ---------- Conditional fields: data-show-when="name=value" ---------- */
  const conds = $$("[data-show-when]");
  const evalConds = () => conds.forEach((el) => {
    const [name, val] = el.dataset.showWhen.split("=");
    const on = $$(`[name="${name}"]`).some((i) => ((i.type === "radio" || i.type === "checkbox") ? i.checked : true) && i.value === val);
    el.hidden = !on;
    $$("input, select, textarea", el).forEach((i) => { if (i.dataset.condRequired !== undefined) i.required = on; });
  });
  if (conds.length) { document.addEventListener("change", evalConds); evalConds(); }

  /* ---------- File drop ---------- */
  const fmtSize = (b) => (b > 1048576 ? (b / 1048576).toFixed(1) + " MB" : Math.max(1, Math.round(b / 1024)) + " KB");
  $$("[data-dropzone]").forEach((dz) => {
    const input = $("input[type=file]", dz);
    const list = document.getElementById(dz.dataset.dropzone);
    const ok = ["image/jpeg", "image/png", "application/pdf"];
    const render = () => {
      list.innerHTML = [...input.files].map((f) => {
        const bad = !ok.includes(f.type) ? "Format tidak didukung" : f.size > 5 * 1048576 ? "Lebih dari 5 MB" : "";
        return `<li class="${bad ? "is-bad" : ""}">${icon(bad ? "alert" : "paperclip")}<span class="name">${esc(f.name)}</span><span class="size">${bad || fmtSize(f.size)}</span></li>`;
      }).join("");
    };
    input.addEventListener("change", render);
    ["dragenter", "dragover"].forEach((ev) => dz.addEventListener(ev, () => dz.classList.add("is-over")));
    ["dragleave", "drop"].forEach((ev) => dz.addEventListener(ev, () => dz.classList.remove("is-over")));
  });

  /* ---------- Validation ---------- */
  function setError(el, msg) {
    const holder = el.type === "radio" || el.type === "checkbox" && el.closest("fieldset") ? el.closest("fieldset") || el.closest(".field") : el.closest(".field") || el.parentElement;
    const id = (el.id || el.name) + "-err";
    let err = document.getElementById(id);
    const group = el.type === "radio" ? $$(`[name="${el.name}"]`, el.form || document) : [el];
    if (!msg) {
      group.forEach((g) => g.removeAttribute("aria-invalid"));
      if (err) err.remove();
      return;
    }
    group.forEach((g) => { g.setAttribute("aria-invalid", "true"); g.setAttribute("aria-describedby", id); });
    if (!err) {
      err = document.createElement("p");
      err.className = "error";
      err.id = id;
      holder.appendChild(err);
    }
    err.innerHTML = `${icon("alert")}<span>${esc(msg)}</span>`;
    err.querySelector(".icon").style.cssText = "width:16px;height:16px;margin-top:2px";
  }
  function checkField(el) {
    if (el.disabled || el.closest("[hidden]")) { setError(el, ""); return true; }
    const v = el.type === "checkbox" ? el.checked : el.value.trim();
    let msg = "";
    if (el.type === "radio") {
      if (el.required && !$$(`[name="${el.name}"]`, el.form || document).some((r) => r.checked)) msg = el.dataset.msg || "Pilih salah satu.";
    } else if (el.required && !v) msg = el.dataset.msg || (el.type === "checkbox" ? "Centang untuk melanjutkan." : "Bagian ini wajib diisi.");
    else if (el.type === "email" && v && !el.checkValidity()) msg = "Format email belum benar. Contoh: nama@email.com";
    else if (el.minLength > 0 && v && el.value.trim().length < el.minLength) msg = `Tulis minimal ${el.minLength} karakter. Saat ini ${el.value.trim().length}.`;
    else if (el.pattern && v && !new RegExp(`^(?:${el.pattern})$`).test(el.value)) msg = el.dataset.msgPattern || "Formatnya belum sesuai.";
    else if (el.dataset.match && v !== document.getElementById(el.dataset.match).value) msg = "Kata sandi tidak sama dengan yang di atas.";
    setError(el, msg);
    return !msg;
  }
  function validate(scope) {
    const seen = new Set();
    let first = null;
    $$("input, select, textarea", scope).forEach((el) => {
      if (el.type === "file" || el.type === "hidden" || el.type === "submit") return;
      if (el.type === "radio") { if (seen.has(el.name)) return; seen.add(el.name); }
      if (!checkField(el) && !first) first = el;
    });
    if (first) first.focus();
    return !first;
  }
  document.addEventListener("change", (e) => { if (e.target.matches("[aria-invalid]")) checkField(e.target); });
  document.addEventListener("input", (e) => { if (e.target.matches("[aria-invalid]:not([type=radio])")) checkField(e.target); });

  /* Demo forms: validate, then redirect or toast */
  $$("form[data-demo]").forEach((form) => form.addEventListener("submit", (e) => {
    e.preventDefault();
    if (!validate(form)) return;
    const btn = $("[type=submit]", form);
    const go = () => {
      if (form.dataset.redirect) { location.href = form.dataset.redirect; return; }
      if (btn) { btn.removeAttribute("aria-busy"); btn.disabled = false; }
      if (form.dataset.successShow) {
        form.hidden = true;
        document.getElementById(form.dataset.successShow).hidden = false;
      }
      if (form.dataset.success) toast(form.dataset.success);
      if (form.dataset.reset !== undefined) form.reset();
    };
    if (btn) { btn.setAttribute("aria-busy", "true"); btn.disabled = true; }
    setTimeout(go, 600);
  }));

  /* ---------- Stepper (lapor) ---------- */
  $$("form[data-stepper]").forEach((form) => {
    const steps = $$("[data-step]", form);
    const items = $$(".stepper li", form);
    const prev = $("[data-prev]", form), next = $("[data-next]", form), submit = $("[data-submit]", form);
    const count = $("[data-step-count]", form);
    let i = 0;
    const fill = () => $$("[data-summary]", form).forEach((el) => {
      const inputs = $$(`[name="${el.dataset.summary}"]`, form);
      const vals = inputs.filter((x) => (x.type === "radio" || x.type === "checkbox") ? x.checked : x.value.trim()).map((x) => {
        const t = x.closest(".choice, .check")?.querySelector(".choice-title, span");
        if (t) return t.textContent.trim();
        if (x.tagName === "SELECT") return x.options[x.selectedIndex].text;
        if (x.type === "date" && x.value) return new Date(x.value).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
        return x.value.length > 160 ? x.value.slice(0, 160) + "…" : x.value;
      });
      el.textContent = vals.join(", ") || "Tidak diisi";
    });
    const show = (n, focus = true) => {
      i = n;
      steps.forEach((s, k) => (s.hidden = k !== n));
      items.forEach((li, k) => {
        li.classList.toggle("is-done", k < n);
        if (k === n) li.setAttribute("aria-current", "step"); else li.removeAttribute("aria-current");
      });
      count.textContent = `Langkah ${n + 1} dari ${steps.length}`;
      prev.hidden = n === 0;
      next.hidden = n === steps.length - 1;
      submit.hidden = n !== steps.length - 1;
      if (n === steps.length - 1) fill();
      if (focus) {
        const h = $(".step-title", steps[n]);
        h.setAttribute("tabindex", "-1");
        h.focus({ preventScroll: true });
        form.scrollIntoView({ block: "start", behavior: reduce ? "auto" : "smooth" });
      }
    };
    next.addEventListener("click", () => { if (validate(steps[i])) show(i + 1); });
    prev.addEventListener("click", () => show(i - 1));
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!validate(steps[i])) return;
      submit.setAttribute("aria-busy", "true");
      submit.disabled = true;
      submit.querySelector("span").textContent = "Mengirim dengan aman…";
      setTimeout(() => (location.href = form.dataset.redirect), 900);
    });
    show(0, false);
  });

  /* ---------- Filtered lists (edukasi, tabel admin) ---------- */
  $$("[data-filter-list]").forEach((root) => {
    const items = $$("[data-item]", root);
    const chips = $$("[data-filter]", root);
    const q = $("[data-search]", root);
    const selects = $$("[data-filter-select]", root);
    const empty = $("[data-empty]", root);
    const count = $("[data-result-count]", root);
    let cat = "semua";
    const apply = () => {
      const t = (q?.value || "").trim().toLowerCase();
      let n = 0;
      items.forEach((it) => {
        let ok = (cat === "semua" || it.dataset.category === cat) && (!t || it.textContent.toLowerCase().includes(t));
        selects.forEach((s) => { if (s.value && it.dataset[s.dataset.filterSelect] !== s.value) ok = false; });
        it.hidden = !ok;
        n += ok;
      });
      if (empty) empty.hidden = n > 0;
      if (count) count.textContent = `${n} ${count.dataset.noun || "hasil"}`;
    };
    chips.forEach((c) => c.addEventListener("click", () => {
      chips.forEach((x) => x.setAttribute("aria-pressed", String(x === c)));
      cat = c.dataset.filter;
      apply();
    }));
    q?.addEventListener("input", apply);
    selects.forEach((s) => s.addEventListener("change", apply));
    $$("[data-reset-filter]", root).forEach((b) => b.addEventListener("click", () => {
      if (q) q.value = "";
      selects.forEach((s) => (s.value = ""));
      chips[0]?.click();
      apply();
    }));
    apply();
  });

  /* ---------- Services directory (dummy data; PHP build serves this as JSON) ---------- */
  const SERVICES = [
    { name: "SAPA 129 (Kementerian PPPA)", type: "hotline", province: "Nasional", city: "Nasional", phone: "129", wa: "08111-129-129", hours: "24 jam", verified: true, tags: ["Pengaduan", "Rujukan"] },
    { name: "Layanan darurat nasional", type: "hotline", province: "Nasional", city: "Nasional", phone: "112", hours: "24 jam", verified: true, tags: ["Darurat"] },
    { name: "UPTD PPA Kabupaten Banyumas", type: "uptd", province: "Jawa Tengah", city: "Banyumas", hours: "Senin–Jumat", verified: false, tags: ["Pendampingan", "Psikologis", "Hukum"] },
    { name: "Unit PPA Satreskrim Polresta Banyumas", type: "polisi", province: "Jawa Tengah", city: "Banyumas", hours: "24 jam", verified: false, tags: ["Laporan polisi"] },
    { name: "RSUD Prof. Dr. Margono Soekarjo", type: "rs", province: "Jawa Tengah", city: "Banyumas", hours: "24 jam (IGD)", verified: false, tags: ["Medis", "Visum"] },
    { name: "Satgas PPKPT Universitas Jenderal Soedirman", type: "kampus", province: "Jawa Tengah", city: "Banyumas", hours: "Hari kerja", verified: false, tags: ["Kampus", "Pendampingan"] },
    { name: "UPTD PPA Provinsi Jawa Tengah", type: "uptd", province: "Jawa Tengah", city: "Semarang", hours: "Senin–Jumat", verified: false, tags: ["Pendampingan", "Rumah aman"] },
    { name: "LBH APIK Semarang", type: "lbh", province: "Jawa Tengah", city: "Semarang", hours: "Hari kerja", verified: false, tags: ["Hukum", "Gratis"] },
    { name: "UPTD PPA Kabupaten Cilacap", type: "uptd", province: "Jawa Tengah", city: "Cilacap", hours: "Senin–Jumat", verified: false, tags: ["Pendampingan"] },
    { name: "Biro layanan psikologi kampus (contoh)", type: "psikolog", province: "Jawa Tengah", city: "Banyumas", hours: "Dengan janji", verified: false, tags: ["Konseling"] },
    { name: "UPTD PPA Provinsi DI Yogyakarta", type: "uptd", province: "DI Yogyakarta", city: "Yogyakarta", hours: "Senin–Jumat", verified: false, tags: ["Pendampingan"] },
  ];
  const TYPE_LABEL = { hotline: "Hotline", uptd: "UPTD PPA", polisi: "Kepolisian (Unit PPA)", rs: "Rumah sakit", lbh: "Lembaga bantuan hukum", psikolog: "Psikolog", kampus: "Satgas kampus" };
  $$("[data-directory]").forEach((root) => {
    const list = $("[data-directory-list]", root);
    const q = $("[data-search]", root), prov = $("[name=provinsi]", root), city = $("[name=kota]", root), type = $("[name=jenis]", root), h24 = $('[name="24jam"]', root);
    const empty = $("[data-empty]", root), count = $("[data-result-count]", root);
    const limit = Number(root.dataset.limit) || 0;
    const mode = root.dataset.directory; // "public" | "refer"
    const render = () => {
      const t = (q?.value || "").trim().toLowerCase();
      let rows = SERVICES.filter((s) =>
        (!prov?.value || s.province === prov.value || s.province === "Nasional") &&
        (!city?.value || s.city === city.value || s.city === "Nasional") &&
        (!type?.value || s.type === type.value) &&
        (!h24?.checked || s.hours.startsWith("24")) &&
        (!t || (s.name + s.tags.join(" ") + s.city).toLowerCase().includes(t)));
      if (limit) rows = rows.slice(0, limit);
      list.innerHTML = rows.map((s) => `
        <li class="service">
          <div class="service-title"><h3>${esc(s.name)}</h3>${s.verified ? '<span class="badge badge-ok">Terverifikasi</span>' : '<span class="badge badge-verify">Perlu verifikasi</span>'}</div>
          <div class="service-meta">
            <span>${icon("building")}${TYPE_LABEL[s.type]}</span>
            <span>${icon("pin")}${s.city === "Nasional" ? "Seluruh Indonesia" : esc(s.city + ", " + s.province)}</span>
            <span>${icon("clock")}${esc(s.hours)}</span>
          </div>
          <div class="service-tags">${s.tags.map((x) => `<span class="badge badge-plain">${esc(x)}</span>`).join("")}</div>
          <div class="service-actions">
            ${s.phone ? `<a class="btn btn-ink btn-xs" href="tel:${s.phone}">${icon("phone")} Telepon <span class="mono">${s.phone}</span></a>` : `<span class="small muted">Kontak sedang diverifikasi tim</span>`}
            ${s.wa ? `<a class="btn btn-ghost btn-xs" href="#" aria-label="WhatsApp ${esc(s.wa)}">${icon("message")} <span class="mono">${s.wa}</span></a>` : ""}
            ${mode === "refer" ? `<button class="btn btn-yellow btn-xs" type="button" data-pick-service="${esc(s.name)}">Pilih untuk rujukan</button>` : ""}
          </div>
        </li>`).join("");
      if (empty) empty.hidden = rows.length > 0;
      if (count) count.textContent = `${rows.length} layanan`;
    };
    [q, prov, city, type, h24].forEach((el) => el?.addEventListener(el.tagName === "INPUT" && el.type !== "checkbox" ? "input" : "change", render));
    prov?.addEventListener("change", () => {
      const cities = [...new Set(SERVICES.filter((s) => !prov.value || s.province === prov.value).map((s) => s.city).filter((c) => c !== "Nasional"))];
      city.innerHTML = '<option value="">Semua kota/kabupaten</option>' + cities.map((c) => `<option>${esc(c)}</option>`).join("");
      render();
    });
    $$("[data-reset-filter]", root).forEach((b) => b.addEventListener("click", () => {
      [q, prov, city, type].forEach((el) => el && (el.value = ""));
      if (h24) h24.checked = false;
      prov?.dispatchEvent(new Event("change"));
    }));
    render();
  });

  /* ---------- Chat (polling simulated) ---------- */
  $$("[data-chat]").forEach((chat) => {
    const log = $(".chat-log", chat), form = $(".composer", chat), ta = $("textarea", form), sync = $("[data-sync]", chat);
    const me = chat.dataset.chat;
    const now = () => new Date().toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" });
    const scroll = () => (log.scrollTop = log.scrollHeight);
    scroll();
    const grow = () => { ta.style.height = "auto"; ta.style.height = Math.min(ta.scrollHeight, 160) + "px"; };
    ta.addEventListener("input", grow);
    ta.addEventListener("keydown", (e) => { if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); form.requestSubmit(); } });
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const text = ta.value.trim();
      if (!text) { ta.focus(); return; }
      log.insertAdjacentHTML("beforeend", `<div class="bubble me">${esc(text).replace(/\n/g, "<br>")}<span class="bubble-meta">${now()} · Terkirim</span></div>`);
      ta.value = ""; grow(); scroll();
      if (chat.dataset.demoReply) {
        setTimeout(() => { log.insertAdjacentHTML("beforeend", '<div class="typing" data-typing aria-label="Sedang mengetik"><i></i><i></i><i></i></div>'); scroll(); }, 900);
        setTimeout(() => {
          $("[data-typing]", log)?.remove();
          log.insertAdjacentHTML("beforeend", `<div class="bubble">${esc(chat.dataset.demoReply)}<span class="bubble-meta">${now()} · ${me === "penyintas" ? "Dewi (pendamping)" : "Penyintas"}</span></div>`);
          scroll();
        }, 2600);
      }
    });
    if (sync) {
      let t = 0;
      setInterval(() => {
        if (document.hidden) { sync.lastChild.textContent = " Dijeda"; return; }
        t = (t + 5) % 60;
        sync.lastChild.textContent = " Diperbarui baru saja";
      }, 5000);
    }
  });

  /* ---------- Slot booking ---------- */
  $$("[data-slots]").forEach((root) => {
    const btn = $("[data-book]", root), out = $("[data-booked]", root);
    root.addEventListener("change", (e) => { if (e.target.name === "slot") btn.disabled = false; });
    btn.addEventListener("click", () => {
      const sel = $("input[name=slot]:checked", root);
      if (!sel) return;
      const label = sel.dataset.label;
      sel.checked = false; sel.disabled = true;
      sel.nextElementSibling.querySelector("small").textContent = "Dipesan olehmu";
      btn.disabled = true;
      if (out) out.textContent = label;
      toast(`Sesi ${label} dipesan. Pendamping akan mengonfirmasi.`);
    });
  });

  /* ---------- Confirm dialogs (destructive actions only) ---------- */
  $$("[data-confirm]").forEach((trigger) => trigger.addEventListener("click", (e) => {
    e.preventDefault();
    const dlg = document.getElementById(trigger.dataset.confirm);
    if (!dlg) return;
    dlg.returnValue = "";
    dlg.showModal();
    dlg.addEventListener("close", function onClose() {
      dlg.removeEventListener("close", onClose);
      if (dlg.returnValue !== "ok") return;
      if (trigger.dataset.done) toast(trigger.dataset.done);
      if (trigger.dataset.removeClosest) trigger.closest(trigger.dataset.removeClosest)?.remove();
      if (trigger.dataset.href) location.href = trigger.dataset.href;
    });
  }));
  $$("dialog form[method=dialog]").forEach((f) => f.addEventListener("submit", (e) => {
    const sub = e.submitter;
    if (sub && sub.value === "ok" && !validate(f)) { e.preventDefault(); }
  }));

  /* ---------- Mark notification rows read ---------- */
  $$("[data-mark-all]").forEach((b) => b.addEventListener("click", () => {
    $$(".is-unread-row").forEach((r) => r.classList.remove("is-unread-row"));
    $$("[data-unread-label]").forEach((l) => l.remove());
    toast("Semua notifikasi ditandai sudah dibaca.");
  }));

  /* ---------- Status change form: show referral field when "dirujuk" ---------- */
  $$("[data-pick-service]").forEach(() => {});
  document.addEventListener("click", (e) => {
    const b = e.target.closest("[data-pick-service]");
    if (!b) return;
    const target = $("[data-picked-service]");
    if (target) { target.value = b.dataset.pickService; toast(`Dipilih: ${b.dataset.pickService}`); }
  });

  /* ---------- Charts (canvas, vanilla) ---------- */
  const CSS = getComputedStyle(document.documentElement);
  const C = { ink: "#1C1B19", ink3: "#6A655C", line: "#E4DFD3", yellow: "#FFC907", deep: "#C99400" };
  const PALETTE = [C.ink, C.yellow, "#8C8579", "#C9C2B3"];
  function niceMax(v) { const p = Math.pow(10, Math.floor(Math.log10(v || 1))); return Math.ceil(v / p / (v / p > 5 ? 2 : 1)) * p * (v / p > 5 ? 2 : 1); }
  function draw(canvas) {
    const cfg = JSON.parse(canvas.dataset.chart);
    const box = canvas.parentElement;
    const w = box.clientWidth, h = box.clientHeight, dpr = window.devicePixelRatio || 1;
    canvas.width = w * dpr; canvas.height = h * dpr;
    const ctx = canvas.getContext("2d");
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    ctx.clearRect(0, 0, w, h);
    const mono = "11px " + (CSS.getPropertyValue("--font-mono") || "monospace");
    const sans = "12px " + (CSS.getPropertyValue("--font-sans") || "sans-serif");
    const series = cfg.series;
    const all = series.flatMap((s) => s.values);
    const max = niceMax(Math.max(...all));

    if (cfg.type === "hbar") {
      ctx.font = sans;
      const lw = Math.min(w * 0.42, Math.max(...cfg.labels.map((l) => ctx.measureText(l).width)) + 16);
      const rowH = Math.min(36, (h - 8) / cfg.labels.length);
      cfg.labels.forEach((l, i) => {
        const y = 4 + i * rowH, v = series[0].values[i], bw = ((w - lw - 48) * v) / max;
        ctx.fillStyle = C.ink; ctx.font = sans; ctx.textBaseline = "middle"; ctx.textAlign = "left";
        ctx.fillText(l, 0, y + rowH / 2);
        ctx.fillStyle = "#F1EDE4"; ctx.fillRect(lw, y + rowH * 0.2, w - lw - 48, rowH * 0.6);
        ctx.fillStyle = i === 0 ? C.ink : C.yellow; ctx.fillRect(lw, y + rowH * 0.2, Math.max(2, bw), rowH * 0.6);
        ctx.fillStyle = C.ink; ctx.font = "bold " + mono; ctx.fillText(String(v), lw + bw + 8, y + rowH / 2);
      });
      return;
    }

    const pad = { l: 36, r: 10, t: 12, b: 28 };
    const pw = w - pad.l - pad.r, ph = h - pad.t - pad.b;
    ctx.font = mono; ctx.fillStyle = C.ink3; ctx.textAlign = "right"; ctx.textBaseline = "middle";
    for (let k = 0; k <= 4; k++) {
      const y = pad.t + ph - (ph * k) / 4;
      ctx.strokeStyle = C.line; ctx.lineWidth = 1;
      ctx.beginPath(); ctx.moveTo(pad.l, Math.round(y) + 0.5); ctx.lineTo(w - pad.r, Math.round(y) + 0.5); ctx.stroke();
      ctx.fillText(String(Math.round((max * k) / 4)), pad.l - 8, y);
    }
    const n = cfg.labels.length, step = pw / n;
    ctx.textAlign = "center"; ctx.textBaseline = "top";
    const every = Math.ceil(n / Math.max(1, Math.floor(pw / 44)));
    cfg.labels.forEach((l, i) => { if (i % every === 0) ctx.fillText(l, pad.l + step * i + step / 2, h - pad.b + 10); });

    if (cfg.type === "bar") {
      const gw = step * 0.64, bw = gw / series.length;
      series.forEach((s, si) => s.values.forEach((v, i) => {
        const bh = (ph * v) / max, x = pad.l + step * i + (step - gw) / 2 + bw * si;
        ctx.fillStyle = PALETTE[si];
        ctx.fillRect(x + 1, pad.t + ph - bh, bw - 2, bh);
      }));
    } else {
      series.forEach((s, si) => {
        const pts = s.values.map((v, i) => [pad.l + step * i + step / 2, pad.t + ph - (ph * v) / max]);
        if (si === 0) {
          ctx.beginPath(); ctx.moveTo(pts[0][0], pad.t + ph);
          pts.forEach((p) => ctx.lineTo(p[0], p[1]));
          ctx.lineTo(pts[pts.length - 1][0], pad.t + ph); ctx.closePath();
          ctx.fillStyle = "rgba(255, 201, 7, 0.28)"; ctx.fill();
        }
        ctx.beginPath(); pts.forEach((p, i) => (i ? ctx.lineTo(p[0], p[1]) : ctx.moveTo(p[0], p[1])));
        ctx.strokeStyle = PALETTE[si]; ctx.lineWidth = 2.5; ctx.lineJoin = "round"; ctx.stroke();
        pts.forEach((p) => { ctx.beginPath(); ctx.arc(p[0], p[1], 3.5, 0, Math.PI * 2); ctx.fillStyle = "#fff"; ctx.fill(); ctx.lineWidth = 2; ctx.stroke(); });
      });
    }
  }
  const charts = $$("canvas[data-chart]");
  if (charts.length) {
    const redraw = () => charts.forEach(draw);
    (document.fonts ? document.fonts.ready : Promise.resolve()).then(redraw);
    if ("ResizeObserver" in window) { const ro = new ResizeObserver(() => redraw()); charts.forEach((c) => ro.observe(c.parentElement)); }
  }
})();

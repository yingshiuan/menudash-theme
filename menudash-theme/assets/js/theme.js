/* MenuDash Theme: the tabs for the recommended dishes, the phone menu and the map on request.
   All only add to what the page already shows. The "open now" badge is MenuDash's
   [menudash_open]. */
(function () {
  "use strict";

  /* ---------- Tabs for the recommended dishes ----------
     Each h3.mdt-picks-sub with the dish grid after it becomes one tab. All rows sit in one
     strip that slides sideways (six dishes to a screen on a computer, two and a bit on a
     phone): a tab slides the strip to its dishes, and swiping past them moves the highlight. */

  function makeTabs() {
    var heads = Array.prototype.slice.call(document.querySelectorAll(".mdt-picks-sub"));
    if (heads.length < 2) return;
    var bar = document.createElement("div");
    bar.className = "mdt-tabs";
    bar.setAttribute("role", "tablist");
    var strip = document.createElement("div");
    strip.className = "mdt-swipe";
    var panels = heads.map(function (h, i) {
      var grid = h.nextElementSibling;
      while (grid && !grid.classList.contains("mdt-picks")) grid = grid.nextElementSibling;
      var btn = document.createElement("button");
      btn.type = "button";
      btn.id = "mdt-tab-" + i;
      btn.setAttribute("role", "tab");
      btn.textContent = h.textContent;
      bar.appendChild(btn);
      grid.setAttribute("role", "tabpanel");
      grid.setAttribute("aria-labelledby", btn.id);
      h.hidden = true;
      return { btn: btn, grid: grid };
    });
    heads[0].parentNode.insertBefore(bar, heads[0]);
    bar.parentNode.insertBefore(strip, panels[0].grid);
    panels.forEach(function (p) { strip.appendChild(p.grid); });

    var current = 0;
    function mark(k) {
      current = k;
      panels.forEach(function (p, i) {
        p.btn.setAttribute("aria-selected", String(i === k));
        p.btn.tabIndex = i === k ? 0 : -1;
      });
    }
    function select(k, focus) {
      mark(k);
      var smooth = !(window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches);
      strip.scrollTo({ left: panels[k].grid.offsetLeft - strip.offsetLeft - parseFloat(getComputedStyle(strip).paddingLeft), behavior: smooth ? "smooth" : "auto" });
      if (focus) panels[k].btn.focus();
    }
    // The tab whose dishes fill the left half of the strip is the lit one.
    var ticking = false;
    strip.addEventListener("scroll", function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        ticking = false;
        var mid = strip.getBoundingClientRect().left + strip.clientWidth / 2, k = 0;
        panels.forEach(function (p, i) { if (p.grid.getBoundingClientRect().left <= mid) k = i; });
        if (k !== current) mark(k);
      });
    }, { passive: true });
    panels.forEach(function (p, i) {
      p.btn.addEventListener("click", function () { select(i, false); });
      p.btn.addEventListener("keydown", function (e) {
        if (e.key === "ArrowRight" || e.key === "ArrowLeft") {
          e.preventDefault();
          select((i + (e.key === "ArrowRight" ? 1 : panels.length - 1)) % panels.length, true);
        }
      });
    });
    addEventListener("resize", function () { select(current, false); });
    select(0, false);
  }

  /* ---------- Phone menu: ☰ becomes ✕ in the same place ----------
     The menu opens below the header (which stays), and WordPress's close button is moved
     onto the spot of the ☰ button: style.css reads these two measurements. */
  function placeMenu() {
    var open = document.querySelector(".mdt-band .wp-block-navigation__responsive-container-open");
    var head = document.querySelector("header");
    if (!open || !head) return;
    var r = open.getBoundingClientRect(), root = document.documentElement.style;
    root.setProperty("--mdt-head", Math.max(0, Math.round(head.getBoundingClientRect().bottom)) + "px");
    root.setProperty("--mdt-x-top", Math.round(r.top) + "px");
    root.setProperty("--mdt-x-left", Math.round(r.left) + "px");
  }

  /* ---------- Map on request ----------
     functions.php sends Google Maps iframes without their address (data-src) and a
     hidden "Show map" button; the button shows here and loads the map on a click.
     The click is remembered in this browser (localStorage), so on later visits the maps
     load by themselves. Nothing is sent to us; the privacy page says so. */
  var MAP_KEY = "mdt-map";

  function mapsOnClick() {
    var remembered = false;
    try { remembered = localStorage.getItem(MAP_KEY) === "yes"; } catch (e) { /* private mode */ }
    Array.prototype.forEach.call(document.querySelectorAll(".mdt-map-slot"), function (slot) {
      var btn = slot.querySelector(".mdt-map-load"), frame = slot.querySelector("iframe[data-src]");
      if (!btn || !frame) return;
      function load() {
        frame.src = frame.getAttribute("data-src");
        slot.classList.add("is-on");
      }
      if (remembered) { load(); return; }
      btn.hidden = false;
      btn.addEventListener("click", function () {
        try { localStorage.setItem(MAP_KEY, "yes"); } catch (e) { /* private mode */ }
        load();
        frame.focus();
      });
    });
  }

  /* ---------- Phone action bar: away while reading, back on the way up ----------
     Scrolling down (reading the menu) slides the bar out of sight; scrolling up, or reaching
     the top or the end of the page, brings it back. When it has moved, a "resize" lets
     MenuDash put its list bar and back-to-top button where the bar is (or isn't). */
  function hideOnScroll(sel) {
    var bar = document.querySelector(sel);
    if (!bar) return;
    var last = scrollY, busy = false;
    function update() {
      busy = false;
      var y = scrollY, moved = y - last;
      if (Math.abs(moved) < 8) return; // Small jitters (a finger resting) change nothing.
      var end = innerHeight + y >= document.documentElement.scrollHeight - 60;
      var hide = moved > 0 && y > 120 && !end;
      if (hide !== bar.classList.contains("is-away")) bar.classList.toggle("is-away", hide);
      last = y;
    }
    addEventListener("scroll", function () { if (!busy) { busy = true; requestAnimationFrame(update); } }, { passive: true });
    bar.addEventListener("transitionend", function (e) { if (e.target === bar && e.propertyName === "transform") dispatchEvent(new Event("resize")); });
    bar.addEventListener("focusin", function () { bar.classList.remove("is-away"); }); // Keyboard users always reach it.
  }

  function start() {
    makeTabs(); placeMenu(); mapsOnClick(); hideOnScroll(".mdt-actionbar");
    // Measured again right before opening: the page may have scrolled.
    document.addEventListener("click", function (e) {
      if (e.target.closest && e.target.closest(".wp-block-navigation__responsive-container-open")) placeMenu();
    }, true);
    addEventListener("resize", placeMenu);
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", start);
  else start();
})();

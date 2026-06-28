# LPK Asa Hikari Mulya — Style Reference
> Two surfaces, one institution: a flat indigo data console for the people who manage the school, and a soft-shadow blue mobile app for the people who just need to clock in and get back to class.

**Theme:** light (both surfaces)

This system is deliberately split into two visual languages because the product has two genuinely different jobs. The **Admin Console** is a desktop-first, data-dense management tool (Super Admin / Admin) — flat cards, 1px borders instead of shadows, indigo `#6D5DFC` as the only accent, Inter as the working typeface. The **Member App** is a mobile-first PWA for Siswa / Karyawan / Sensei — soft-shadow floating cards with no borders, blue `#1A6DFF` as the only accent, Plus Jakarta Sans for a warmer, rounder feel. They never share an accent hue and never share an elevation method, which is what keeps them from reading as "the same dashboard with different colors." Both inherit the same neutral-gray logic and the same icon library, so the product still feels like one institution under the surface.

---

# PART A — ADMIN CONSOLE
*(Super Admin, Admin — desktop-first, sidebar layout)*

## Tokens — Colors

| Name | Value | Token | Role |
|------|-------|-------|------|
| Indigo Primary | `#6D5DFC` | `--color-admin-indigo` | Primary buttons, active sidebar indicator, focus rings, links |
| Indigo Deep | `#4B3DF5` | `--color-admin-indigo-deep` | Gradient end on the premium CTA, hover/pressed states |
| Indigo Tint | `#EFEBFF` | `--color-admin-indigo-tint` | Active sidebar item background, selected segmented-control pill |
| Ink | `#15131F` | `--color-admin-ink` | Headings, stat-card numerals |
| Slate | `#6F6C84` | `--color-admin-slate` | Secondary text, captions, inactive nav icon/label |
| Mist | `#ABA8BD` | `--color-admin-mist` | Placeholder text, disabled icons |
| Surface | `#FFFFFF` | `--color-admin-surface` | Card background, sidebar background |
| Canvas | `#F6F6FB` | `--color-admin-canvas` | App background behind cards |
| Border | `#E6E6F0` | `--color-admin-border` | Card borders, table dividers, input outlines |
| Success | `#16A34A` | `--color-admin-success` | "Active" badge text, positive delta |
| Success Tint | `#DCFCE7` | `--color-admin-success-tint` | "Active" badge background |
| Danger | `#DC2626` | `--color-admin-danger` | "Inactive" badge text, destructive actions |
| Danger Tint | `#FEE2E2` | `--color-admin-danger-tint` | "Inactive" badge background |

## Tokens — Typography

### Inter — working typeface for every Admin Console screen
- **Substitute:** Plus Jakarta Sans, Manrope, General Sans
- **Weights:** 400, 500, 600, 700
- **Sizes:** 12px, 13px, 14px, 16px, 18px, 20px, 24px, 28px, 32px
- **Line height:** 1.0–1.15 for stat numerals, 1.2–1.3 for headings, 1.5–1.6 for body
- **Role:** 700-weight is reserved for stat-card numerals only, so they stay the visual anchor of every card. 600 is used for section headings and active nav labels. 400/500 carries body and captions at a relaxed 1.5–1.6 leading because the screens are dense with tabular data. 12px uppercase labels get +0.04em tracking to read as eyebrows above numbers, never as buttons.

### Type Scale

| Role | Size | Line Height | Letter Spacing | Token |
|------|------|-------------|----------------|-------|
| caption | 12px | 1.4 | 0.02em | `--text-admin-caption` |
| label (eyebrow) | 12px | 1.4 | 0.04em uppercase | `--text-admin-label` |
| body | 14px | 1.6 | 0 | `--text-admin-body` |
| body-lg | 16px | 1.6 | 0 | `--text-admin-body-lg` |
| subheading | 18px | 1.3 | 0 | `--text-admin-subheading` |
| heading | 24px | 1.2 | -0.01em | `--text-admin-heading` |
| heading-lg | 28px | 1.15 | -0.01em | `--text-admin-heading-lg` |
| stat-number | 32px | 1.0 | -0.02em | `--text-admin-stat` |

## Tokens — Spacing & Shapes

**Base unit:** 8px (4px permitted only for icon-to-label micro gaps)
**Density:** comfortable — data-dense but never cramped

### Spacing Scale
| Token | Value | Use |
|-------|-------|-----|
| `--space-admin-1` | 4px | icon/label gap inside badges |
| `--space-admin-2` | 8px | internal stack gaps |
| `--space-admin-3` | 12px | nav item padding (vertical) |
| `--space-admin-4` | 16px | card padding, grid gutters |
| `--space-admin-5` | 20px | card padding (default) |
| `--space-admin-6` | 24px | card-to-card gap |
| `--space-admin-8` | 32px | section gap, page padding |
| `--space-admin-10` | 40px | top bar vertical breathing room |

### Border Radius
| Element | Radius | Token |
|---------|--------|-------|
| Stat card / integration card | 16px | `--radius-admin-lg` |
| Sidebar active item / secondary button | 10px | `--radius-admin-md` |
| Search input / status badge / gradient CTA | 999px | `--radius-admin-full` |
| Avatar | 999px | `--radius-admin-full` |

### Layout
- **Sidebar width:** 240px fixed, collapses to a 72px icon rail below 1024px
- **Top bar height:** 72px
- **Page max-width:** full-bleed inside 32px page padding
- **Section gap:** 32px
- **Card grid gap:** 16–20px

## Components

### Sidebar Nav Item
**Role:** primary navigation between Dashboard / User / Absensi / Report / Setting.
Default: transparent background, icon + label in Slate `#6F6C84`, 14px medium, padding 10px 16px, radius 10px. Active: background Indigo Tint `#EFEBFF`, icon + label Indigo Primary `#6D5DFC`, weight 600.

### Primary Gradient Button
**Role:** the single highest-priority AI/insight or premium action per screen.
Background `linear-gradient(135deg, #6D5DFC, #4B3DF5)`, text white, radius 999px, padding 10px 20px, font 14px weight 600, 16px leading icon. No drop shadow — the gradient itself carries the visual weight.

### Secondary Solid Button
**Role:** standard create/save actions ("+ Create New Form" equivalent — "+ Tambah User", "+ Tambah Kelas").
Background `#6D5DFC` flat, text white, radius 10px, padding 10px 18px, font 14px weight 600.

### Stat Card
**Role:** top-of-dashboard KPI tiles (Total Siswa, Total Sensei, Total Absensi Hari Ini…).
Background Surface, border 1px `#E6E6F0`, radius 16px, padding 20px. Label row: 12px uppercase Slate + 18px outline icon. Delta badge top-right: pill, Success/Danger/neutral tint, 12px weight 600, padding 2px 8px. Number: 28–32px Ink weight 700, margin-top 8px.

### Segmented Control
**Role:** filter toggles (All / Active / Paused — or "Mingguan / Bulanan / Tahunan").
Track background Canvas, radius 10px, padding 4px. Each segment: padding 6px 14px, radius 8px, 13px weight 500 Slate. Active segment: Surface white background, Ink text, no shadow.

### Data List / Integration Card
**Role:** record cards (Kelas, Jadwal, Laporan summaries).
Background Surface, border 1px `#E6E6F0`, radius 16px, padding 20px. Header: 40px icon tile (tinted square, radius 10px) + 15px weight 600 title + 13px Slate subtitle; status badge top-right. Hairline divider 1px `#E6E6F0`, margin 16px 0. Stats row: two columns, 12px uppercase Slate label over 20px weight 700 Ink value.

### Status Badge
Padding 2px 10px, radius 999px, 12px weight 600, paired tint/text per status (Success, Danger, or a neutral Slate-on-Canvas pill for inactive/paused states).

### Search Input
Background Canvas, radius 999px, padding 10px 16px, no border, placeholder in Mist, 16px leading icon in Slate.

## Do's and Don'ts
### Do
- Keep every card on the 16px-radius + 1px-border system; depth comes from the Canvas/Surface contrast, never from a shadow.
- Reserve the indigo gradient for exactly one CTA per screen — the highest-priority action only.
- Keep stat numerals at weight 700 and cap everything else at 600; this is the only place 700 appears.

### Don't
- Don't introduce a second accent hue alongside indigo / success / danger.
- Don't use emoji anywhere — sidebar icons, badges, and empty states all use the Lucide outline set.
- Don't let card radius drift past 16px or padding drop below 16px; the fully-rounded 999px pill belongs to buttons and badges only, never to a card.

## Elevation & Surfaces
Flat surface system. Depth is communicated entirely through a single 1px border (`#E6E6F0`) plus the four-step contrast between Canvas (`#F6F6FB`) and Surface (`#FFFFFF`) — not through shadow. A soft shadow (`0 4px 12px rgba(21,19,31,0.06)`) is reserved exclusively for floating layers that sit above the page: dropdown menus, modals, toasts. Static cards never carry a shadow.

## Imagery & Layout
Icons are 1.5px-stroke outline glyphs (Lucide/Feather family), monochrome Slate by default and recolored only inside a tinted tile to signal category or status. No illustration, no photography in the chrome — the only raster image permitted is the 32–36px circular user avatar. Structurally: fixed sidebar left, fixed top bar, content scrolls in a single column of section blocks (page header → stat-card row → segmented filter → card grid), each block separated by the 32px section gap.

---

# PART B — MEMBER APP (ABSENSI)
*(Siswa, Karyawan, Sensei — mobile-first PWA, bottom-nav layout)*

## Tokens — Colors

| Name | Value | Token | Role |
|------|-------|-------|------|
| Blue Primary | `#1A6DFF` | `--color-member-blue` | Clock In button, active bottom-nav icon, links |
| Blue Deep | `#0F52CC` | `--color-member-blue-deep` | Pressed/hover state on the primary button |
| Blue Tint | `#E8F0FF` | `--color-member-blue-tint` | Clock Out button fill, selected chips |
| Ink | `#14151A` | `--color-member-ink` | Headings, the big clock numerals |
| Slate | `#7C7F89` | `--color-member-slate` | Secondary text, timestamps, address strings |
| Mist | `#B8BAC2` | `--color-member-mist` | Placeholder values (e.g. `--:--:--`) |
| Surface | `#FFFFFF` | `--color-member-surface` | Cards, bottom-nav background |
| Canvas | `#F4F5F8` | `--color-member-canvas` | Page background |
| Border | `#E9EAEE` | `--color-member-border` | Hairline dividers only — cards lean on shadow, not border |
| Status Hadir | `#16A34A` / `#DCFCE7` | `--color-status-hadir` | Present |
| Status Terlambat | `#D97706` / `#FEF3C7` | `--color-status-terlambat` | Late |
| Status Izin | `#2563EB` / `#DBEAFE` | `--color-status-izin` | Permission |
| Status Sakit | `#EA580C` / `#FFEDD5` | `--color-status-sakit` | Sick |
| Status Alpha | `#DC2626` / `#FEE2E2` | `--color-status-alpha` | Absent |

*(Feature-grid icon-tile accents — fill only, never used as chrome/buttons/text):* Coral `#FF6584`, Violet `#8B5CF6`, Sky `#38BDF8`, Mint `#22C55E`, Amber `#F59E0B`.

## Tokens — Typography

### Plus Jakarta Sans — friendly, rounded grotesque for every member-facing screen
- **Substitute:** Sora, General Sans, Inter
- **Weights:** 400, 500, 600, 700, 800
- **Sizes:** 12px, 13px, 14px, 16px, 18px, 24px, 32px, 40px
- **Line height:** 1.0 for the live clock display, 1.2 for headings, 1.4–1.5 for body
- **Role:** 800-weight is locked exclusively to the live time numerals (40px) so the clock is unmistakably the page's focal point. 600/700 covers names and section titles. 400/500 at a relaxed 1.4–1.5 keeps dense address/timestamp strings legible at small sizes.

### Type Scale

| Role | Size | Line Height | Letter Spacing | Token |
|------|------|-------------|----------------|-------|
| caption | 12px | 1.4 | 0.01em | `--text-member-caption` |
| label | 13px | 1.4 | 0.02em uppercase | `--text-member-label` |
| body | 14px | 1.5 | 0 | `--text-member-body` |
| name | 16px | 1.3 | 0 | `--text-member-name` |
| section-title | 18px | 1.3 | 0 | `--text-member-section` |
| button | 15px | 1.0 | 0.01em | `--text-member-button` |
| clock | 40px | 1.0 | -0.02em | `--text-member-clock` |

## Tokens — Spacing & Shapes

**Base unit:** 8px
**Density:** spacious — generous touch targets, mobile breathing room

### Spacing Scale
| Token | Value | Use |
|-------|-------|-----|
| `--space-member-2` | 8px | icon-to-label gaps |
| `--space-member-3` | 12px | inline element gaps |
| `--space-member-4` | 16px | screen padding, card padding (min) |
| `--space-member-5` | 20px | card padding (default) |
| `--space-member-6` | 24px | section gap |
| `--space-member-8` | 32px | hero/profile-card top offset |
| `--space-member-10` | 40px | clock-screen vertical centering |

### Border Radius
| Element | Radius | Token |
|---------|--------|-------|
| Cards / banners | 20px | `--radius-member-xl` |
| Confirmation photo, feature image | 16px | `--radius-member-lg` |
| Buttons, badges, avatars, icon tiles | 999px | `--radius-member-full` |
| Bottom nav bar | 0px (edge-to-edge, flush) | `--radius-member-none` |

### Layout
- **Content max-width:** 480px, centered (mobile-first; never gains a sidebar, even on desktop)
- **Screen padding:** 16–20px
- **Section gap:** 24px
- **Bottom nav height:** 64px + safe-area-inset-bottom

## Components

### Header Bar
Background Surface, height 56px, padding 16px, no border. Left: org name + 14px chevron, weight 600 Ink. Right: 20px outline icons (search, bell) in Slate.

### Profile Summary Card
Background Surface, radius 20px, padding 20px, soft shadow `0 8px 20px rgba(20,21,26,0.06)`. 48px circular avatar, name 16px weight 700 Ink, role 13px Slate, date/time right-aligned 12px Slate.

### Attendance Status Row
Two columns ("Absen Masuk" / "Absen Keluar"). Label 12px uppercase Slate. Value 24px weight 700 Ink when recorded, or Mist `#B8BAC2` for the unset `--:--:--` placeholder.

### Primary Pill Button — Clock In
Background `#1A6DFF` solid, text white 15px weight 600, radius 999px, padding 14px 24px, 18px leading icon, soft shadow `0 6px 16px rgba(26,109,255,0.25)`.

### Secondary Pill Button — Clock Out
Background Blue Tint `#E8F0FF`, text Blue Primary `#1A6DFF`, radius 999px, padding 14px 24px, no border — fill-based, not stroke-based.

### Feature Grid Icon Tile
48px circular badge, flat solid fill from the accent set (Coral/Violet/Sky/Mint/Amber), 22px white icon glyph centered, 12px Slate label below, no shadow on the tile itself.

### Bottom Navigation Bar
Fixed, Surface white, height 64px + safe area, flush/edge-to-edge (no radius). 4–5 items, 22px icon + 11px label. Inactive: Slate, outline icon variant. Active: Blue Primary, solid/filled icon variant, label weight 600 — color shift only, no background pill.

### Camera Capture Screen
Full-bleed live camera feed. Top scrim `rgba(0,0,0,0.4)` fading to transparent for header legibility (white 16px weight 600 title + back chevron). Bottom scrim mirrored. Shutter: 72px white circle with a 4px white ring offset, no icon, positioned `safe-area-bottom + 24px`.

### Confirmation Detail Card
Full-screen on Canvas/Surface. Centered column: 96px square photo, radius 16px, border 1px `#E9EAEE`; name 16px weight 700; ID 13px Slate; label/value pairs centered; address block 13px Slate with a 14px leading location-pin icon; inline text link ("Perbaharui lokasi") 13px weight 600 Blue Primary; primary pill button pinned to the bottom safe area.

### Status Pill
Padding 4px 10px, radius 999px, 12px weight 600, tint/text pairing per attendance status (Hadir/Terlambat/Izin/Sakit/Alpha).

## Do's and Don'ts
### Do
- Keep every primary action a fully-rounded 999px pill — this is what visually separates this surface from the Admin Console's 10px rectangles.
- Let elevation (soft shadow) carry all card depth; this surface has no card borders.
- Reserve weight-800 type exclusively for the live clock numerals.

### Don't
- Never put emoji in the bottom nav or feature grid — every glyph is a real SVG icon (outline for inactive, solid for active).
- Don't borrow the Admin Console's indigo hue anywhere on this surface.
- Don't let the feature-grid accent colors (Coral/Violet/Sky/Mint/Amber) leak onto buttons or status badges — they exist for icon tiles only.

## Elevation & Surfaces
Soft elevation system — the inverse of the Admin Console. Canvas is flat `#F4F5F8` and every card floats on a diffuse shadow (`0 8px 20px rgba(20,21,26,0.06)` default; `0 6px 16px rgba(26,109,255,0.25)` for the primary button) instead of a border. This single difference — border vs. shadow — is what keeps the two surfaces from ever being mistaken for one another, even though they share the same neutral-gray logic.

## Imagery & Layout
Icons mix outline (inactive nav, header utilities) and solid/filled (active nav, feature-grid glyphs) variants of the same icon family at 1.5–2px stroke. Photography is limited to the user's own avatar and the live/just-captured selfie, shown as soft-radius squares or circles — never full-bleed except on the dedicated camera screen. Structurally: header → profile/clock card → two-button row → feature grid → announcement banner → fixed bottom nav, all within the 480px centered column.

---

## Shared Foundations
- **Icon library:** Lucide (covers both the outline-only Admin Console and the outline+solid pairing the Member App needs for nav states).
- **Accessibility:** minimum 44px touch target on every Member App control; 2px focus-visible ring in the surface's own accent (Indigo for Admin, Blue for Member).
- **Motion:** 160ms ease-out on nav active-state transitions and button press states only. No scroll-triggered reveals, no celebratory/confetti motion — both surfaces stay quiet.
- **Responsive:** Admin Console collapses its sidebar to a 72px icon rail below 1024px. Member App is the canonical mobile experience; on desktop it simply stays centered at 480px and never grows a sidebar.
- **Dark mode:** the original brief asked for dark-mode support, but both reference designs are light-only. Recommendation: ship light-only first against this spec, and treat dark mode as a phase-2 token swap (`--color-*-surface`/`--color-*-canvas`/`--color-*-ink` inversions) rather than designing it blind now.

## Agent Prompt Guide
**Quick Color Reference**
- Admin: Indigo `#6D5DFC` · Canvas `#F6F6FB` · Success `#16A34A` · Danger `#DC2626`
- Member: Blue `#1A6DFF` · Canvas `#F4F5F8` · Hadir `#16A34A` · Alpha `#DC2626`

**Example Component Prompts**

1. *"Build a Stat Card row for the Admin Console dashboard: 4 cards, white background, 1px `#E6E6F0` border, 16px radius, 20px padding, no shadow. Each card has a 12px uppercase Slate `#6F6C84` label with an 18px outline icon, a 28px weight-700 Ink `#15131F` number below it, and a small pill badge top-right in Success `#16A34A`/`#DCFCE7` or Danger `#DC2626`/`#FEE2E2`. Use Inter, gap 16px between cards."*

2. *"Build a Sidebar with 8 nav items in Inter 14px. Default state: Slate `#6F6C84` icon + label, transparent background, 10px radius, padding 10px 16px. Active state: Indigo Tint `#EFEBFF` background, Indigo Primary `#6D5DFC` icon + label, weight 600. Pin a user avatar + name + plan label at the bottom of the sidebar."*

3. *"Build the Clock In/Clock Out row for the Member App: two pill buttons, 999px radius, 14px 24px padding, 15px weight-600 text. Clock In: solid `#1A6DFF` background, white text, soft shadow `0 6px 16px rgba(26,109,255,0.25)`. Clock Out: `#E8F0FF` fill, `#1A6DFF` text, no border. Place both above a 5-column feature grid of 48px circular icon tiles in Coral/Violet/Sky/Mint/Amber."*

4. *"Build the attendance confirmation screen for the Member App: white background, centered column, 96px square photo with 16px radius and 1px `#E9EAEE` border, name in 16px weight 700, ID in 13px Slate `#7C7F89`, a large 40px weight-800 clock value, an address line with a location-pin icon, a `#1A6DFF` 13px weight-600 inline text link reading 'Perbaharui lokasi', and a full-width `#1A6DFF` pill button pinned to the bottom safe area."*

## Quick Start

```css
:root {
  /* ===== ADMIN CONSOLE ===== */
  --color-admin-indigo: #6D5DFC;
  --color-admin-indigo-deep: #4B3DF5;
  --color-admin-indigo-tint: #EFEBFF;
  --color-admin-ink: #15131F;
  --color-admin-slate: #6F6C84;
  --color-admin-mist: #ABA8BD;
  --color-admin-surface: #FFFFFF;
  --color-admin-canvas: #F6F6FB;
  --color-admin-border: #E6E6F0;
  --color-admin-success: #16A34A;
  --color-admin-success-tint: #DCFCE7;
  --color-admin-danger: #DC2626;
  --color-admin-danger-tint: #FEE2E2;

  --radius-admin-lg: 16px;
  --radius-admin-md: 10px;
  --radius-admin-full: 999px;

  --space-admin-1: 4px;  --space-admin-2: 8px;  --space-admin-3: 12px;
  --space-admin-4: 16px; --space-admin-5: 20px; --space-admin-6: 24px;
  --space-admin-8: 32px; --space-admin-10: 40px;

  /* ===== MEMBER APP ===== */
  --color-member-blue: #1A6DFF;
  --color-member-blue-deep: #0F52CC;
  --color-member-blue-tint: #E8F0FF;
  --color-member-ink: #14151A;
  --color-member-slate: #7C7F89;
  --color-member-mist: #B8BAC2;
  --color-member-surface: #FFFFFF;
  --color-member-canvas: #F4F5F8;
  --color-member-border: #E9EAEE;

  --color-status-hadir: #16A34A;      --color-status-hadir-tint: #DCFCE7;
  --color-status-terlambat: #D97706;  --color-status-terlambat-tint: #FEF3C7;
  --color-status-izin: #2563EB;       --color-status-izin-tint: #DBEAFE;
  --color-status-sakit: #EA580C;      --color-status-sakit-tint: #FFEDD5;
  --color-status-alpha: #DC2626;      --color-status-alpha-tint: #FEE2E2;

  --color-feature-coral: #FF6584;
  --color-feature-violet: #8B5CF6;
  --color-feature-sky: #38BDF8;
  --color-feature-mint: #22C55E;
  --color-feature-amber: #F59E0B;

  --radius-member-xl: 20px;
  --radius-member-lg: 16px;
  --radius-member-full: 999px;

  --space-member-2: 8px;  --space-member-3: 12px; --space-member-4: 16px;
  --space-member-5: 20px; --space-member-6: 24px; --space-member-8: 32px;
  --space-member-10: 40px;

  --shadow-member-card: 0 8px 20px rgba(20,21,26,0.06);
  --shadow-member-primary: 0 6px 16px rgba(26,109,255,0.25);
  --shadow-admin-float: 0 4px 12px rgba(21,19,31,0.06);
}
```

```css
@theme {
  /* Admin */
  --color-admin-indigo: #6D5DFC;
  --color-admin-indigo-deep: #4B3DF5;
  --color-admin-indigo-tint: #EFEBFF;
  --color-admin-ink: #15131F;
  --color-admin-slate: #6F6C84;
  --color-admin-surface: #FFFFFF;
  --color-admin-canvas: #F6F6FB;
  --color-admin-border: #E6E6F0;
  --color-admin-success: #16A34A;
  --color-admin-danger: #DC2626;

  --radius-admin-lg: 16px;
  --radius-admin-md: 10px;

  --font-admin: "Inter", "Plus Jakarta Sans", sans-serif;

  /* Member */
  --color-member-blue: #1A6DFF;
  --color-member-blue-tint: #E8F0FF;
  --color-member-ink: #14151A;
  --color-member-slate: #7C7F89;
  --color-member-surface: #FFFFFF;
  --color-member-canvas: #F4F5F8;

  --color-status-hadir: #16A34A;
  --color-status-terlambat: #D97706;
  --color-status-izin: #2563EB;
  --color-status-sakit: #EA580C;
  --color-status-alpha: #DC2626;

  --radius-member-xl: 20px;
  --radius-member-full: 999px;

  --font-member: "Plus Jakarta Sans", "Sora", sans-serif;
}
```
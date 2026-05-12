# 法寶管理系統 — Project Knowledge

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | Vue 3 (Composition API, SFC), Vite 5, Tailwind CSS 3, Sass |
| Backend | Laravel 10 (PHP 8.2), MySQL, Sanctum auth |
| Node | 22.20.0 / npm 10.9.3 |
| CSS | Tailwind v3.4 with arbitrary values (`text-[13px]`, `w-[198px]`, etc.) |

## Project Structure

```
D:\xampp\htdocs\fabou_la
├── app/
│   ├── Http/
│   │   ├── Controllers/    # 19 controllers
│   │   └── ...
│   ├── Models/              # 18 Eloquent models
│   └── ...
├── resources/
│   └── js/
│       └── components/      # 37 Vue components
│           ├── AdminDashboard.vue
│           ├── TeachingManager.vue
│           ├── RegistryManager.vue
│           ├── ImperialGraceManager.vue
│           ├── ImperialGraceAddForm.vue
│           ├── KaiwenManager.vue
│           ├── KaiwenBatchAdd.vue
│           ├── GrudgeManager.vue
│           ├── GrudgeBatchImport.vue
│           ├── GrudgeAddForm.vue
│           ├── MilitaryManagerV2.vue
│           ├── MilitaryAddForm.vue
│           ├── MilitaryBatchAdd.vue
│           ├── OtherManager.vue
│           ├── OtherRecordsManager.vue
│           ├── OtherTeachingManager.vue
│           ├── RegistryAddForm.vue
│           ├── LuckyDraw.vue
│           ├── TrashManager.vue
│           ├── SearchComponent.vue
│           ├── CompactDatePicker.vue
│           ├── RemarksViewer.vue
│           ├── PaginationButtons.vue
│           ├── LogoImperialNotebook.vue
│           ├── MobileDashboard.vue
│           ├── MobileNavbar.vue
│           ├── ProfileSettings.vue
│           ├── RandomGroup.vue
│           ├── AdminRootSelector.vue
│           ├── AddActionMenu.vue
│           ├── KaiwenApproval.vue
│           └── admin/
│               ├── DharmaCrud.vue
│               ├── GroupCrud.vue
│               ├── MasterCrud.vue
│               ├── TreasureCrud.vue
│               └── UserCrud.vue
├── routes/
│   ├── web.php              # Main routes (auth-protected)
│   └── api.php              # API routes (sanctum)
├── package.json
├── vite.config.js
├── tailwind.config.js
└── composer.json
```

## Dev Workflow

| Command | Purpose |
|---|---|
| `php artisan serve` | Start PHP dev server (http://127.0.0.1:8000) |
| `npm run dev` | Start Vite HMR dev server |
| `npm run build` | Production build |
| `php artisan migrate` | Run DB migrations |
| `php artisan route:list` | List all routes |

Two terminals needed: `php artisan serve` + `npm run dev`.

## GitHub

- Remote: `https://github.com/shudgai/fabou_la.git`
- Current branch: `optimization`

## Database

- MySQL, database `fabou`, user `root`/no password
- 18 models/tables

## UI Conventions (from recent work)

| Rule | Description |
|---|---|
| Input style | `border-0 border-b-2 border-slate-300 bg-transparent` (underline, no box) |
| Folder button | `w-[198px] h-[198px]`, SVG `w-[163px] h-[163px]`, `viewBox="0 0 64 64"` |
| Folder fill | Solid color (no `url(#gradient)`) — `#ef4444` / `#b91c1c` |
| Folder text (registries) | Yellow `#fbbf24` |
| Folder grid | `flex flex-col items-center gap-[4px]` (vertical, no borders) |
| Button layout (Kaiwen) | Title + two tabs on same row: `flex-row items-center gap-3 ml-3` |
| Batch import title | `<h5>` with `<br>` for multi-line |
| Layout | Use `flex-col` / `items-center` for vertical stacking; `grid-cols-2` only for form footer buttons |
| SVG folder path | `M4 14C4 11.7909...` (standard folder shape, solid fill) |
| Vite build warning | Pre-existing CSS `text-[13px]` warning is minor/ignorable |
| Vue parser | `h-[198px]` cannot be inside `:class` — use static `class` instead |
| Overflow | Avoid `overflow-x-clip` on containers that hold fixed-width folder buttons |

## Component-Specific Notes

### TeachingManager.vue
- Main add/list UI for 父皇仙師開示載錄
- Folder grid: `flex flex-col items-center gap-[4px]`
- Folder icon fill: `#ef4444` (red)
- Has `*允同享皇恩` in remark-list datalist

### RegistryManager.vue
- 法寶登記專區
- Folder grid: same as Teaching
- Folder text: `#fbbf24` (yellow)
- Folder icon fill: `#b91c1c` (dark red)
- `overflow-visible` on outer wrapper

### ImperialGraceManager.vue
- 皇恩 module — reference for folder sizing (198×198 buttons, 163×163 SVG, `overflow-clip` on wrapper)
- `overflow-y-auto` on scroll container

### KaiwenManager.vue
- 開文專區 — weekly + self posts
- Header: title "開文專區" + two tabs side by side (17px, purple active color)
- Form footer: `grid grid-cols-2 gap-4` for cancel/save buttons
- `mt-[3px]` on tab container
- TDZ bug fixed: draft auto-save watch must be placed AFTER `weeklyLines` declaration (line ~712), not before

### GrudgeBatchImport.vue
- Batch import UI for 怨靈載錄
- Header: `<h5>` "怨靈載錄專區<br>多筆載錄"
- Preview toast: `w-full` on flex column to match parent
- Placeholder: `多筆新增如下列:\n日期(yyyy/mm/dd)\n法號總數`

### LuckyDraw.vue
- 抽籤專區 — lottery/draw system
- Step 1 label: "滑動游標選取固定人員"; Step 2 label: "滑動游標選取其他抽籤人員"
- Flow: 抽順序 (step1 → step2 → step3), 回合抽籤 (step1 → step3)
- 回合抽籤 mode: `roundParticipants` auto-populated with all selected names on step 3 entry and new round
- step 3 & 4 lotteryMode headers show LogoImperialNotebook + 抽籤專區 + subtitle with logo header bar
- step 3 subtitle on its own centered row: "回合抽籤 - 點選本輪人員"

### GrudgeAddForm.vue / MilitaryAddForm.vue
- All inputs: `border-0 border-b-2 border-slate-300 bg-transparent` (underline)
- Dharma name dropdown items: 17px to match content text
- All content text (labels, inputs, dropdowns) unified to `text-[17px]`
- All inputs `text-center`

### MilitaryAddForm.vue (2026-05-12 rewrite)
- Step-based form: 日期 → 法號 → 數量 → 備註 → 預覽 (5 steps)
- Cumulative mode: 日期 → 數量 → 備註 → 預覽 (4 steps)
- 法號 and 備註對象 are separate fields (matching GrudgeAddForm pattern)
- CompactDatePicker rendered at root level with `v-if`/`@close`, triggered by calendar icon
- 3 dot action menu at `top-[48px]` in manager
- Dropdowns: clean white bg + shadow, no `rounded-xl border` to avoid boxy look on mobile
- Footer buttons: `absolute bottom-[7dvh]` on mobile (above navbar), `md:relative` on desktop
- 黑曜軍 labels: 閻尊 / 閻閽
- 耀紫軍 labels: 龍勝 / 龍戰

### GrudgeManager.vue
- List items: `py-3 px-3` with `border-slate-300` between items

### OtherManager.vue
- Module for 其他 (miscellaneous) records with special sub-views (開文核定表, 隨機分組, 抽籤)
- Header uses dual-line layout: module title (30px) + active folder name (23px)
- Active folder title span at `OtherManager.vue:115` — `margin-left: 14px` to align with KaiwenApproval content
- KaiwenApproval and RandomGroup rendered as child components with `v-if` on folder name
- Folder buttons follow same 198×198 pattern with SVG icons
- "抽順序" (Draw Sequence) button is explicitly rendered as the second item in the grid, immediately following the first folder (usually 開文核定表).

### KaiwenApproval.vue
- 開文核定表 — two-step flow: select participants → approval table with ✓/× slots
- Step 1: grid of dharma names (4 cols mobile, 5 cols desktop), toggle selection, confirm order
- Step 2: approval table with per-row expandable slot count, V/X toggle per slot
- "點選待定法號" header has `marginTop: 10px` for vertical alignment
- Bottom action bar at `bottom: calc(7dvh + env(safe-area-inset-bottom))` for mobile
- Copy to LINE: exports formatted results to clipboard (✓ / × notation)
- Font size overrides via `:global(body.font-*)` selectors for accessibility

### RandomGroup.vue
- 隨機分組 — 5-step wizard flow: Personnel Selection → Guardians → Seeds → Rules & Size → Results
- Group Size configuration located at the bottom of Step 4 for better ergonomic flow
- Distribute logic uses safe size parameter (`Math.max(1, parseInt(size) || 1)`) to prevent infinite `while` loop freeze if the size input is manually cleared.
- Results view (Step 5) utilizes standard administrative layout and dynamically fills container height correctly utilizing flex.

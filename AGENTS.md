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
| Responsive grids | Use `grid-cols-1 sm:grid-cols-2` for mobile-first 2-col; use `w-full max-w-[Xpx] aspect-square` for fluid buttons |
| Folder image overlay font | `font-family: 'DFKai-SB', '標楷體', serif` (cascade via inline `style` on parent div) |
| Sub-folder scaling | 150% of original: container `w-[284px] h-[284px]`, SVG `w-[266px] h-[266px]`; text/ spacing scaled proportionally |
| Lucky Draw (OtherManager) | Grid `grid-cols-1` with `w-[310px] h-[310px]` fixed buttons; 4 units: 抽順序(empty), 資料夾, 抽順序(in-list), 回合抽籤 |
| Folder overlay text positioning | `justify-start pt-[72px]` (not `justify-center`) for Teaching sub-folders with tall images |
| Home view title spacing | `tracking-tighter` (not `tracking-widest`) for consistency between home and list views |
| Back button | `w-[100px]` → `min-w-[100px]` to prevent text truncation on mobile |
| Batch import preview | Show raw pasted lines as-is (use `rawLines` computed: `split('\n').map(l => l.trim()).filter(l => l !== '')`); never modify `batchInput`/`batchText` textarea content with watchers |
| Batch integrity (WYPIWYS) | For batch-pasted records, preserve the FULL original content in the database; do NOT strip headers or item lines. List view should render raw content with `whitespace-pre-wrap` and hide synthetic headers when `isContentLiteral` is true. |
| Dropdown/Menu state | Use `activeDropdownId` (reactive) to track which item menu/dropdown is open; ensure `activeDropdownId = null` is called on delete/edit/close. |
| CompactDatalist (Mobile) | Replace native `<datalist>` on mobile with boxed buttons below input; only visible on mobile (`md:hidden`); caps at 15 items for performance. |

## Component-Specific Notes

### TeachingManager.vue
- Main add/list UI for 父皇仙師開示載錄
- Folder grid: `flex flex-col items-center gap-[4px]`
- Folder icon fill: `#ef4444` (red)
- Has `*允同享皇恩` in remark-list datalist
- Sub-folder images scaled 150% (btn 260→390px, img 245→368px)
- Home title uses `tracking-tighter`
- Homepage image text: `pt-[72px]` (not `pt-24`) for vertical positioning
- All dropdowns use `bottom-full mb-*` to expand upward (replaces `top-full mt-*`)
- **Full Fidelity (WYPIWYS)**: Literal batch records bypass synthetic header generation and truncation. List view detects these via `isContentLiteral(item)` and renders `item.content` raw with `whitespace-pre-wrap`.
- **Reference Fix**: All deletion/menu logic must use `activeDropdownId` (defined in script) to avoid `ReferenceError` on `openMenuId`.
- **master_name Leak Bug (frontend fix)**: `showAdd()` (line 5082) sets `form.value.master_name = mName` as pre-fill. This leaks into `performActualSave` payload via `...form.value` spread, causing PHP backend's `resolveRelations()` to override master_id. Fix: destructure out `master_name` before spreading (`const { master_name: _mn, ...formClean } = form.value`). Single-item save at `performActualSave` line 4837.
- **master_id Fallback Trap**: `performActualSave` line 4840: `master_id: form.value.master_id || currentFolder.value?.id` — 0 is falsy in JS, so `master_id=0` (daily folder) would fall through to `currentFolder.value?.id`. Always ensure explicit master_id is set.
- **Single-Member Group Suppression**: To prevent individual dharma names (e.g., "閻願") from being automatically "upgraded" to their single-member group name (e.g., "玄義宮") after saving/reloading, `getFullRecipientList` now requires a `hintName`. It only auto-resolves groups with >1 member unless the `hintName` exactly matches the group name.
- **Group Identity Persistence**: Selecting a group in `TeachingAddForm.vue` now populates `form.value.target_remarks` with the group name. This acts as a persistent hint in the database so that after a page refresh, the system knows whether the user intended to record for the "Group" or the "Individual".

### TeachingAddForm.vue
- Step-based form modal for 父皇仙師每日開示 (wizard: 日期 → 仙師 → 對象 → 內容 → 降寶 → 預覽)
- Modal container: `overflow-hidden` removed to prevent dropdown clipping
- **Upward-expanding teleported dropdowns**: Master, Practitioner, Treasure dropdowns use `<teleport to="body">` with `position: fixed` + `transform: translateY(-100%)` to expand upward without being clipped by scroll container
- Each teleported dropdown has a corresponding `open*Dropdown()` function using `getBoundingClientRect()` for positioning
- Master and Practitioner inputs: dropdown only opens on arrow button click or on input typing (not on focus alone)
- Master input: no pre-fill on mount (removed `onMounted` auto-fill logic)
- Homepage image text: `pt-[72px]` (not `pt-24`) for vertical positioning
- All dropdowns use `bottom-full mb-*` to expand upward (replaces `top-full mt-*`)
- **Template setTimeout 替代**: Vue 模版中不可用 `setTimeout`/`window.setTimeout`（編譯為 `_ctx.setTimeout` 而報錯）。一律用 `delayClose(ref, ms)` helper function 取代。
- **resolveMasterId 回退**: 當 `props.masters` 搜尋不到時，使用硬編碼 map: `{ '老祖仙師': 1, '元始仙師': 2, ... '閻王仙師': 8 }` 作為ID回退。`handleNext()` 在第2步時也會呼叫 resolveMasterId()。

### RegistryManager.vue
- 法寶登記專區
- Folder grid: same as Teaching
- Folder text: `#fbbf24` (yellow)
- Folder icon fill: `#b91c1c` (dark red)
- Sub-folder images scaled 150% (btn 189→284px, img 177→266px)
- **Edit Interface Refinement**: Dharma name selection moved below main Remarks field; uses a 3-column table (法號, 日期, 備註) for both View and Edit modes.
- **Table Headers**: "法號" header includes an "Add" (新增) button in both modes; Edit mode toggles a grid selector; View mode triggers Edit mode with selector open.
- **Others Category**: Table columns reordered to [法號, 日期, 親友, 備註] to prioritize dharma name visibility.

### ImperialGraceManager.vue
- 皇恩 module — reference for folder sizing (198×198 buttons, 163×163 SVG, `overflow-clip` on wrapper)
- `overflow-y-auto` on scroll container
- Sub-folder images scaled 150% (btn 260→390px, img 245→368px)

### KaiwenManager.vue
- 開文專區 — weekly + self posts
- Header: title "開文專區" + two tabs side by side (17px, purple active color)
- Form footer: `grid grid-cols-2 gap-4` for cancel/save buttons
- `mt-[3px]` on tab container
- TDZ bug fixed: draft auto-save watch must be placed AFTER `weeklyLines` declaration (line ~712), not before
- Acrostic grid container: `overflow-x-hidden` → `overflow-x-auto` to prevent cutting content

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
- Modal pattern: parent must NOT use `v-if` — use `:show` prop with inner `v-if="show"` (teleport compat)

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
- Active folder title span: `margin-left: 14px` to align with KaiwenApproval content
- KaiwenApproval and RandomGroup rendered as child components with `v-if` on folder name
- Folder buttons follow same 198×198 pattern with SVG icons
- Lucky Draw section uses `grid-cols-1` with 4 fixed `w-[310px] h-[310px]` folder buttons
- "抽順序" always appears after first folder (idx===0), plus standalone "回合抽籤"
- **Close button fix**: Don't use `v-if` on parent for modals with `<teleport to="body">` — use `:show` prop + inner `v-if="show"` instead (see LuckyDraw). KaiwenApproval and RandomGroup must have `@close="activeFolderId = null"` handler.
- `overflow-visible` removed from root wrappers to prevent layout bleed

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

## Performance & Optimization

| Technique | Implementation |
|---|---|
| Image Priority | `fetchpriority="high"`, `loading="eager"` on critical module icons |
| Preloading | Critical assets (`imperial_grace_book_v5.png`, etc.) preloaded in `app.blade.php` |
| Animation Speed | Snappy 0.25s transitions (`animate-slide-up`, `animate-fade-in`) |
| Modal Standard | All modals wrapped in `<teleport to="body">`, `fixed inset-0 z-[3500] flex items-end md:items-center justify-center` |
| Close Button | `absolute right-* top-* z-[50] p-2 text-slate-300 hover:text-slate-600` with X SVG inside `<button @click="$emit('close')">` |
| Z-Index | Standardized levels: Modals (`z-[3500]`), Toasts (`z-[6000]`), Nav (`z-[100]`), Close buttons inside modals (`z-[50]/z-[500]/z-[9999]`) |
| Bottom Button Bar | `fixed md:absolute left-0 right-0 ... bottom-[calc(7dvh+env(safe-area-inset-bottom))] md:bottom-0` with `z-[200]` |
| Mobile Safe Area | Replace `<mobile-navbar>` in modals with `<div class="h-[env(safe-area-inset-bottom)] md:h-0"></div>` |
| Dropdown mobile style | All custom dropdown items use `md:rounded-*` so mobile renders flat rows (no per-item boxes); desktop keeps `rounded-*` |
| CompactDatalist Pattern | Use for all inputs requiring suggestions on mobile to prevent keyboard overlay issues; handles filtering and boxed layout. |
| Scroll iOS smoothness | All `.custom-scrollbar` must include `-webkit-overflow-scrolling: touch` for inertial scrolling on iPhone |
| localStorage iOS safe | Use `safeLocalStorage` from `iosCompat.js` instead of raw `localStorage` (Private Browsing throws SecurityError) |

## PHP Backend: `resolveRelations` 陷阱

`TeachingService.php` 的 `resolveRelations()` 會檢查 `$data['master_name']` 並用其**覆蓋** `master_id`：
```php
if (isset($data['master_name']) && !empty($data['master_name'])) {
    $master = Master::where('name', $data['master_name'])->first();
    if ($master) $data['master_id'] = $master->id;  // 覆蓋原本的 master_id!
}
```
前端傳 payload 時若 `form.value` 中殘留 `master_name`（如 `showAdd()` 的 pre-fill），會導致 `master_id` 被錯誤覆蓋。**前後端都要注意**：前端 payload 須 destructure 移除 `master_name`；後端若有明確 `master_id` 應優先使用。

## Draft Auto-Save Pattern (localStorage)

All draft auto-save features follow this pattern (see KaiwenManager.vue line ~714, OtherRecordsManager.vue, LuckyDraw.vue, OtherManager.vue):

| Step | Implementation |
|---|---|
| Save | `watch(() => ({...form fields...}), (val) => { if (addMode/visible) safeLocalStorage.setKey(JSON.stringify(val)) }, { deep: true })` |
| Restore | On add/open, check `safeLocalStorage.getItem(key)`, parse, `window.confirm('偵測到您有未儲存的草稿，是否要載入？')`, restore fields |
| Clear | On save success and on close/cancel: `safeLocalStorage.removeItem(key)` |

| Component | Draft Key | Data Saved |
|---|---|---|
| KaiwenManager | `kaiwen_draft` | form, weeklyLines, isManualWeekly, type |
| OtherRecordsManager | `other_records_draft` | title, content, date |
| LuckyDraw | `lucky_draw_draft` | currentStep, pendingNames, fixedParticipants, selectedNames, roundParticipants, drawCount, manualName, lotteryMode |
| OtherManager | `other_manager_record_draft` | title, content, record_date |

## Session Notes (2026-05-15)

### DB Wipe & Re-seed
- `php artisan db:wipe --force && php artisan migrate --force` clears all tables and test data
- After wipe, run: `php artisan db:seed --class=MasterSeeder` (or `php artisan db:seed` for all)
- Admin user 元續: email `shudgai999@gmail.com`, password `abc1234`, role `admin`

### i18n: No English in UI
- All user-visible English text replaced with Traditional Chinese
- Login auth error message: `lang/zh_TW/auth.php` with `'failed' => '帳號或密碼錯誤，請重新輸入。'`
- Login flow always starts at step 1 (removed auto-advance to password when saved credentials exist)
- Forgot password link added to step 1 and step 3 of login form
- Date placeholders use `年/月/日` format
- "Step X of Y" → "步驟 X / Y"
- PHP API responses: `'已刪除'` / `'已更新'` / `'錯誤'`

### KaiwenManager Acrostic Grid (Desktop)
- Cells: `md:w-12 md:h-12`, gap: `md:gap-0` (mobile: `gap-1`)
- Textarea rows: `:rows="props.isDesktop ? 2 : 6"`
- Tab buttons: `md:flex-row` side-by-side on desktop

## Session Notes (2026-05-15)

### Registry AddForm UI: label 作法 → 作法/求寶方式
- `RegistryAddForm.vue:147`, `RegistryAddForm.vue:277`, `RegistryManager.vue:286`: `作法 (選填)` → `作法/求寶方式`

### Registry Batch Add Fix (重大 bug 修復)

#### Root cause: `batchParsedRows` 解析器回傳 0 筆
- `batchParsedRows` computed 要求所有法號必須存在於 `dharmaNames` 中 (`knownNames.length > 0 && names.length === knownNames.length`)，若 `dharmaNames` 尚未載入（非同步）則全部拒絕
- `triggerBatchSave` 忽略表單的 `batchData.rows`，對原始文字進行不相容的二次區塊解析

#### 修復清單 (`RegistryAddForm.vue:572-730`)

| 問題 | 修復 |
|---|---|
| `dharmaNames` 未載入時法號驗證全拒 | Step 6: `dharmaNames.value.length === 0` 時改用 heuristic（排除含冒號的文字），不再強依賴 DB |
| tab 分隔格式不支援 (`日期\t允求:法寶\t法號`) | 新增 Step 5: `\t` 分割後直接建立記錄，不驗證 dharmaNames（欄位位置明確） |
| `允求:法寶 法號1,法號2` 前綴單行格式 | Step 4: prefixKeywords 處理器在 `prefixKeywords.includes(key)` 時，直接解析 val 中的法寶 + 法號 |
| 屬性殘留（前筆用意洩漏到後筆） | Step 7 新法寶名稱時重置 `pendingTreasure = { name: '', purpose: '', ... }` |
| `triggerBatchSave` 二次解析 | 優先使用 `batchData.rows`（表單已解析資料），老解析器保留為 fallback |

#### 支援的批次格式
1. **Tab 分隔**: `日期\t前綴:法寶名稱\t法號1,法號2` （最推薦）
2. **前綴單行**: `允求:法寶名稱 法號1,法號2`
3. **多行屬性**: `日期` → `法寶名稱` → `用意:xxx` → `法號1,法號2`
4. **民國年**: `113.10.6` 自動轉西元 `2024-10-06`

## Image Optimization (2026-05-15)

### Format & Sizing Strategy
| Before | After | Saving |
|--------|-------|--------|
| 1024×1024 PNG, 574~840 KB each | 256×256 WebP, **5~11 KB** each | **~99%** |
| `ImageController.php` output PNG (1024×1024) | output **WebP** (q=80), cache `.webp` | ~70% smaller |

### Active Images (in `public/image/`)
- `imperial_grace_book_v5.png` (709 KB) + `.webp` (10 KB) — ImperialGraceManager, TeachingManager
- `registry_book_yellow_v2.png` (574 KB) + `.webp` (5 KB) — RegistryManager
- `registry_book_yellow_v6.png` (682 KB) + `.webp` (6 KB) — ImageController teaching template
- `imperial_grace_book_yellow.png` (840 KB) + `.webp` (11 KB) — currently unused

### Implementation
- **Vue components** use `<picture>` with WebP source + PNG fallback:
  ```html
  <picture><source srcset="/image/xxx.webp" type="image/webp">
  <img src="/image/xxx.png" ...></picture>
  ```
- **ImageController** (`ImageController.php`): `imagepng()` → `imagewebp($img, $cachePath, 80)`, cache extension `.webp`, `Content-Type: image/webp`
- **app.blade.php preloads** point to `.webp` files (not `.png`)
- **PNG originals kept** as fallback for older browsers and for GD source images in ImageController (which uses `imagecreatefromjpeg`)
- **6 orphaned images** (~3.7 MB) and empty `favicon.ico` deleted; unused `imperial_grace_book_yellow.png` preload removed

### Resize Conversion (PHP GD)
```powershell
# Resize and convert any PNG/JPEG to 256×256 WebP:
$srcImg = imagecreatefromjpeg($path);  # or imagecreatefrompng
$dstImg = imagescale($srcImg, 256, 256);
imagewebp($dstImg, $outputPath, 80);
```

### Important Notes
- Files have JPEG headers (`FF D8 FF E0`) despite `.png` extension — `imagecreatefromjpeg()` is correct
- Do NOT delete the original `.png` files — ImageController uses them as background sources for dynamic text overlay
- WebP has universal browser support as of 2026



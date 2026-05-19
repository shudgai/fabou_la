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
| Imperial Grace List | Two-row vertical stack: [Date + Status] / [Name (Large/Bold)] |
| Batch Date Parsing | Supports ROC (114.10.6) and AD. Auto-detects `Date Name` (single line) or `Date\nName` (multi-line). **No default date applied if missing.** |
| Data Security | Field-level AES-256 encryption applied to all sensitive 'content', 'remarks', and 'purpose' fields across models. |
| Batch import preview | Show raw pasted lines as-is (use `rawLines` computed: `split('\n').map(l => l.trim()).filter(l => l !== '')`); never modify `batchInput`/`batchText` textarea content with watchers |
| Batch integrity (WYPIWYS) | For batch-pasted records, preserve the FULL original content in the database; do NOT strip headers or item lines. List view should render raw content with `whitespace-pre-wrap` and hide synthetic headers when `isContentLiteral` is true. |
| Dropdown/Menu state | Use `activeDropdownId` (reactive) to track which item menu/dropdown is open; ensure `activeDropdownId = null` is called on delete/edit/close. |
| CompactDatalist (Mobile) | Replace native `<datalist>` on mobile with boxed buttons below input; only visible on mobile (`md:hidden`); caps at 15 items for performance. |

## Component-Specific Notes

### TeachingManager.vue
- Main add/list UI for 父皇仙師開示載錄
- Wizard steps refactored to 7 steps (Added Step 6: Footer Remarks).
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
- Step-based form modal for 父皇仙師每日開示 (wizard: 日期 → 仙師 → 對象 → 內容 → 降寶 → 結尾備註 → 預覽)
- Modal container: `overflow-hidden` removed to prevent dropdown clipping
- **Footer Remarks (Step 6)**: 
  - Title size matches body text (`17px`).
  - Single underline design: All items and input sit on a common `border-b-2 border-slate-300` base.
  - Vertical Stacking: Selected items stack vertically and are centered horizontally.
  - Sorting logic: "完畢" is always forced to the bottom of the list.
  - Interactive Entry: Input field remains visible after adding items for iterative custom entry.
  - Quick Add: Chip-style buttons (`*允同享皇恩`, `完畢`) located below the entry line.
- **Upward-expanding teleported dropdowns**: Master, Practitioner, Treasure dropdowns use `<teleport to="body">` with `position: fixed` + `transform: translateY(-100%)` to expand upward without being clipped by scroll container
- Each teleported dropdown has a corresponding `open*Dropdown()` function using `getBoundingClientRect()` for positioning
- Master and Practitioner inputs: dropdown only opens on arrow button click or on input typing (not on focus alone)
- Master input: no pre-fill on mount (removed `onMounted` auto-fill logic)
- Homepage image text: `pt-[72px]` (not `pt-24`) for vertical positioning
- All dropdowns use `bottom-full mb-*` to expand upward (replaces `top-full mt-*`)
- **Template setTimeout 替代**: Vue 模版中不可用 `setTimeout`/`window.setTimeout`（編譯為 `_ctx.setTimeout` 而報錯）。一律用 `delayClose(ref, ms)` helper function 取代。
- **resolveMasterId 回退**: 當 `props.masters` 搜尋不到時，使用硬編碼 map: `{ '老祖仙師': 1, '元始仙師': 2, ... '閻王仙師': 8 }` 作為ID回退。`handleNext()` 在第2步時也會呼叫 resolveMasterId()。

### RegistryManager.vue
- 特殊法寶登記區
- Folder grid: same as Teaching
- Folder text: `#fbbf24` (yellow)
- Folder icon fill: `#b91c1c` (dark red)
- Sub-folder images scaled 150% (btn 189→284px, img 177→266px)
- **Folder Labels**: "特殊法寶<br>登記區" (two lines, break at '寶', scaled 90%).
- **Edit Interface Refinement**: Dharma name selection moved below main Remarks field; uses a 3-column table (法號, 日期, 備註) for both View and Edit modes.
- **Table Headers**: "法號" header includes an "Add" (新增) button in both modes; Edit mode toggles a grid selector; View mode triggers Edit mode with selector open.
- **Others Category**: Table columns reordered to [法號, 日期, 親友, 備註] to prioritize dharma name visibility.

### RegistryAddForm.vue
- Step-based form modal for 法寶登記 (Wizard: 日期 → 仙師 → 法寶名稱 → 用意 → 功效 → 作法 → 法寶內容 → 備註)
- **Registry Batch Import Standard (2026-05-16)**:
  - **Combined Line**: `[Date] [Name] 承接者:[Recipients]` (e.g. `109.11.1 閻燁提升為玄之將 承接者:赤覺`)
  - **Recipients**: Comma or space separated (e.g. `鳳尊，鳳煌，龍勝`).
  - **Multi-line Attributes**: `用意:`, `功效:`, `作法:`, `法寶內容:`, `備註:`. 
    - Multiple lines of the same attribute are appended with newlines (`\n`).
    - Attributes following a recipient line apply to the *preceding* treasure and all its recipients.
  - **Prefix Stripping**: Automatically strips `允求:`, `賜降:`, `得知:`, `求得:` from treasure names for data purity.
  - **Date Parsing**: Supports ROC (`109.11.1`) and standalone year headers (`110年`).
  - **Heuristic**: Space-separated lines starting with a date are treated as combined lines if they contain a recipient prefix.
- **Registry AddForm UI: label**: `作法 (選填)` → `作法/求寶方式`.

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

### LuckyDraw.vue (2026-05-17)
- **TDZ 修復**: `loadUsers` 函式定義移至 watchers 之前，避免 `immediate: true` watcher 初始化時 TDZ 錯誤
- **Defensive check**: watch callback 中加入 `typeof loadUsers === 'function'` 防禦性檢查
- **Mobile scroll fix**: 所有步驟的 flex 容器加入 `min-h-0` 確保行動裝置正確計算高度並啟用滾動
- **回合抽籤**: Step 3/4 內容過長時可正常向下滾動

### LuckyDraw.vue 抽籤結果介面 (2026-05-18 Finalized)
- **最多 3 欄**: `chunkedResults` computed 依人數動態分欄 — ≤10人→1欄, ≤20人→2欄, 21+人→3欄
  ```js
  const cols = n <= 10 ? 1 : n <= 20 ? 2 : 3;
  const chunkSize = Math.ceil(n / cols);
  ```
- **全寬佈局**: 欄位容器 `w-[100vw] flex flex-row flex-nowrap justify-between items-start px-3` — 使用 100vw + justify-between 填滿手機螢幕
- **直式排列**: 每欄 `flex flex-col justify-start items-center gap-y-4 md:gap-y-6`
- **上方 padding**: 結果滾動容器 `px-0 pt-[15px] pb-64`（15px 與標題欄的間距）
- **序號咖啡色 + 法號紅色**: 兩個獨立 `<span>`：
  - 序號 `color: '#92400e'`（深琥珀棕）
  - 法號 `color: '#dc2626'`（深紅）+ textShadow
- **動態字型 (`getDynamicFontSize`)**: 1人→120px, 2人→90px, 3-4人→64px, 5-6人→54px, 7-8人→46px, 9-12人→40px, 13-16人→34px, 17+人→`clamp(22px, 5.5vw, 38px)`
- **動畫**: `animate-slide-in`，每筆延遲 `idx * 0.03s`
- **兩個結果畫面均套用**: Step 4 (回合抽籤) 及 Draw Order 最終結果畫面結構完全一致
- **底部按鈕色彩配置**: 改為淺色系底框搭配深色文字（淺綠 `bg-green-200 text-green-900`、淺黃 `bg-yellow-200 text-yellow-900`、淺藍 `bg-blue-200 text-blue-900`）提升辨識度。

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
1. **聯合單行 (推薦)**: `日期 法寶名稱 承接者:法號1,法號2`
2. **Tab 分隔**: `日期\t法寶名稱\t法號1,法號2`
3. **前綴單行**: `允求:法寶名稱 法號1,法號2`
4. **多行屬性 (狀態式解析)**: `日期` → `法寶名稱` → `用意:xxx` → `承接者:法號1,法號2` → `備註:xxx`
5. **屬性累加**: 若出現多行相同的屬性（如多個 `備註:`），解析器會自動換行累加，不再覆蓋。
6. **民國年**: `113.10.6` 自動轉西元 `2024-10-06`。

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

## UI Tweaks (2026-05-15)

### Bold Removal
| Component | Element | Before | After |
|-----------|---------|--------|-------|
| `MilitaryManagerV2.vue` | `.military-label`, `.military-value`, `.military-value-name`, `.military-date-value` | `font-weight: 900/500` | `font-weight: 400` |
| `MilitaryManagerV2.vue` | Date header spans | `font-black` class | removed |
| `GrudgeManager.vue` | `.grudge-label` | `font-weight: 900` | `font-weight: 400` |
| `GrudgeManager.vue` | Date header spans | `font-black` class | removed |

### Grid Alignment (GrudgeManager.vue)
- All `法號:` fields changed from `flex-col` (label above name) to `flex-row items-center space-x-1.5` (label + name side by side)
- Grid layout: `grid-cols-2` — Row 1: 日期+法號, Row 2: 數量+結果 (法號 aligns vertically with 結果)

### Unused Imports Removed
| File | Import Removed |
|------|---------------|
| `RandomGroup.vue` | `CompactDatalist` |
| `TeachingManager.vue` | `CompactDatalist` |

### Scratch Directory
- `/scratch` added to `.gitignore` — 116 temporary/test files untracked from repo

## Session Notes (2026-05-15)

### LuckyDraw 回合抽籤 — 法號框清空
- `handleNextRound`: `roundParticipants` 改為 `[]`，新回合時法號框不再自動全選，讓使用者自行點選本輪人員
- `confirmSelection` (lotteryMode): `roundParticipants` 改為 `[]`，選完固定人員進入 step 3 時法號框保持清空

## Session Notes (2026-05-16)

### TeachingAddForm.vue 遺失變數 Bug

#### 問題
`TeachingAddForm.vue` 在 commit `1376e26` (feat: add new record button) 的改版中，將自訂下拉選單（teleported dropdown）全部替換為 `EditableInputChips` 元件。改版過程中**誤刪**了 8 個關鍵變數定義，導致：
- `currentStep` 為 `undefined` → 畫面只顯示 `STEP  / `（無步驟數字）
- `currentStep === 1` 永遠為 `false` → 所有步驟內容都隱藏
- `form`, `masterNameInput`, `dharmaSearchQuery` 等同樣遺失 → 表單無法運作

#### 影響變數清單
| 變數 | 型態 | 功能 |
|------|------|------|
| `currentStep` | `ref(1)` | 當前步驟 (1-6) |
| `totalSteps` | `6` | 總步驟數 |
| `stepTitles` | 陣列 | 6 個步驟的標題文字 |
| `currentStepTitle` | `computed` | 當前步驟標題 |
| `form` | `ref({...})` | 表單資料物件 (date, master_id, dharma_name_ids, content, items, ...) |
| `masterNameInput` | `ref('')` | 仙師輸入綁定 |
| `dharmaSearchQuery` | `ref('')` | 對象搜尋綁定 |
| `showItemsSelector` | `ref(false)` | 降寶明細選擇器開關 |

#### 修復
在 `TeachingAddForm.vue:369` 的 `// --- 2. Computed ---` 之前補回所有變數定義。

#### 教訓 (前後端 + git)
1. **Vue 3 Composition API**：所有 template 中使用的變數都必須在 `<script setup>` 中顯式宣告，Vue 不會報錯遺失變數（僅默默地呈現 undefined）。
2. **Git diff 檢查**：大範圍 template 重構時，務必用 `git diff` 確認沒有意外刪除 script 區塊的變數定義，特別是與 template 綁定的 `ref`/`computed`。
3. **Build 驗證**：此 bug 不會造成 build error（Vue template 編譯時不會檢查變數是否已宣告），必須實際瀏覽測試才能發現。
4. **未來重構守則**：修改 template 時若涉及刪除/改寫區塊，應同時檢查 `v-model`、`{{ }}`、`v-if` 中使用的變數是否有對應宣告。

## Session Notes (2026-05-16)

### TeachingAddForm.vue 逐筆新增 — 全部 textarea 支援換行

#### 問題
逐筆新增表單的 input/textarea 無法用 Enter 換行，或 textarea 太小（rows 不足）、`resize-none` 無法拖曳放大。

#### 修改清單

| 位置 | 修改 |
|------|------|
| `EditableInputChips.vue:28` | 移除 `@keydown.enter.stop`，讓 Enter 可以正常換行；`rows="2"` → `rows="3"` |
| `TeachingAddForm.vue:169` (Step 4 內容) | `rows="8"` → `rows="10"`，移除 `resize-none`，加 `min-h-[200px]` |
| `TeachingAddForm.vue:312` (降寶備註 modal) | `rows="2"` → `rows="4"`，移除 `resize-none`，加 `min-h-[100px]` |
| `TeachingAddForm.vue:64` (批次主貼入區) | 移除 `resize-none`，加 `min-h-[300px]` |
| `TeachingAddForm.vue:81` (批次單筆記錄) | 移除 `resize-none`，`rows="3"` → `rows="4"`，`min-h-[60px]` → `min-h-[100px]` |

#### 教訓
`EditableInputChips` 是共用元件，修改 `@keydown.enter.stop` 會影響所有使用處。若需保留某些場景的 Enter 阻擋行為，應改為透過 prop 控制。

## Session Notes (2026-05-16)

### TeachingManager.vue 逐筆修改功能

#### 功能說明
- 在每日開示列表中，點擊三點選單的「修改」可展開逐筆編輯介面
- 編輯介面包含：日期、仙師、對象、開示內容、賜降法寶、結尾備註
- 使用 EditableInputChips 元件編輯仙師和對象

#### 修復記錄
1. **Overlay 滑動修復**：將 overlay 容器從 `overflow-hidden` 改為 `overflow-y-auto`，使內容可滾動
2. **對象儲存 bug**：修正 `saveInlineEdit` 中的資料結構不正確問題
   - 錯誤：傳送 `target_name` (string) 到後端
   - 正確：解析 `target_name` 為 `dharma_name_ids` (array of IDs) 和 `target_remarks` (string)
3. **UI 樣式優化**：移除編輯介面的框線，改為下底線樣式輸入框 (`border-0 border-b-2 border-slate-300 bg-transparent`)

#### 相關程式碼位置
- `TeachingManager.vue:341-423` - 編輯介面模板
- `TeachingManager.vue:1084-1117` - saveInlineEdit 函數
- `TeachingManager.vue:1056-1073` - startInlineEdit 函數（初始化編輯資料）
- `TeachingManager.vue:1052-1081` - cancelInlineEdit 函數（重設表單）

## Session Notes (2026-05-16)

### Data Isolation Fix — 使用者資料隔離

#### 問題
`php artisan test` 中的 `DataIsolationTest` 有 4 個測試失敗：
- `registry isolation` — 使用者 B 看到 15 筆（應為 0）
- `imperial grace isolation` — 使用者 B 看到 13 筆（應為 0）
- `grudge isolation` — 使用者 B 看到 3 筆（應為 0）
- `teaching linkage is preserved` — 403 Forbidden

#### 根因分析
1. **Registry/ImperialGrace/Grudge**：程式碼**已經有** `where('user_id', $user->id)` 隔離邏輯，**是測試斷言寫錯**。測試用 `assertCount(0, $response->json())` 檢查 paginated response 的 key 數量（~15 個），不是實際資料筆數。
2. **Teaching**：`applyVisibilityFilter` 只檢查 `user_id`，沒有包含被法號關聯的開示。`TeachingController` 的 `can_see_daily_teachings` 權限檢查也過於嚴格。

#### 修復清單

| 檔案 | 修改 |
|------|------|
| `tests/Feature/DataIsolationTest.php:57` | `assertCount(0, $response->json())` → `assertCount(0, $response->json()['data'])` |
| `tests/Feature/DataIsolationTest.php:76` | `assertCount(0, $response->json()['registries'])` → `assertCount(0, $response->json()['registries']['data'])` |
| `tests/Feature/DataIsolationTest.php:116` | `assertCount(0, $response->json())` → `assertCount(0, $response->json()['paginator']['data'])` |
| `tests/Feature/DataIsolationTest.php:142` | `assertCount(1, $response->json()['data'])` → `assertCount(1, $response->json()['records']['data'])` |
| `app/Services/TeachingService.php:327` | `applyVisibilityFilter` 加入 `orWhereHas('dharmaNames')` 讓被法號標記的使用者也能看到開示 |
| `app/Http/Controllers/TeachingController.php:28-40` | 權限檢查加入：如果使用者的 `dharma_name_id` 有關聯的每日開示，允許存取 |

#### 修復後結果
```bash
php artisan test
# Tests: 7 passed, 12 assertions
```

## Pre-Launch Checklist

### 上線前必做檢查

| 指令 | 說明 |
|------|------|
| `npm run build` | 前端編譯成功，無 error（僅 CSS 警告可忽略） |
| `php artisan test` | 所有測試通過 |
| `php artisan route:list` | 確認路由正確（155 條） |
| `php artisan migrate:status` | 確認遷移已執行 |
| `php artisan config:cache` | 正式環境需快取設定 |
| `php artisan route:cache` | 正式環境需快取路由 |

### 正式環境設定 (.env)
```env
APP_ENV=production
APP_DEBUG=false
```

### GitHub
- Remote: `https://github.com/shudgai/fabou_la.git`
- Current branch: `optimization`
- 上線後建議 commit 本次修復：
  ```
  git add app/Http/Controllers/TeachingController.php app/Services/TeachingService.php tests/Feature/DataIsolationTest.php
  git commit -m "fix: 修復使用者資料隔離與開示權限問題"
  ```

## Session Notes (2026-05-18)

### LuckyDraw 回合抽籤優化與修復

#### 修復與調整細節

| 項目 | 修改內容 | 目的 |
|---|---|---|
| **改橫向** | 移除了多筆結果展示容器中法號的直排樣式 (`writingMode: 'vertical-rl'`) | 讓多個被抽中的法號以現代、直觀的橫排文字形式排列，具備流暢換行 |
| **不要加數字** | 移除了法號名單上方原本帶有背景的小 index 數字標籤 (`idx + 1`) | 呈現最純粹、無干擾的法號結果列表 |
| **開始本輪抽籤沒反應** | 在進入步驟三與新回合時，將 `roundParticipants.value` 的初始空值 `[]` 修改為自動拷貝當前所有選定人員 `[...selectedNames.value]` | 解決因初始為空而點擊按鈕無回應的問題，預設全選，使用者仍可點擊取消，大幅提升易用性 |
| **不要出現螢光（字體跳動/加框）** | 移除了單筆與多筆結果展示的黃色螢光底色背景卡片、框線與陰影，並拿掉了 `animate-bounce` 彈跳動畫 | 讓法號以超大氣 `76px` 標楷體靜態呈現，文字不抖動，大方沉穩 |
| **要出現抽籤介面** | 保留並精緻化了點擊抽籤時的飛竹搖籤動畫畫面（即動畫螢幕） | 維持系統最頂級、動態且極致豪華的儀式感 |
| **結果文字水平垂直置中** | 將滾動容器與結果排版全部重構為 `flex flex-col justify-center items-center min-h-[350px]` 居中佈局 | 確保抽籤結果無論是單個或多個法號，在任何螢幕尺寸下皆能 100% 完美水平與垂直絕對居中 |
| **Phase 2 臨時人員新增 (Add Picker)** | 新增雙用「新增人員」輸入列：手打文字按 `+` 則加入新名；空白狀態按 `+` 則展開「不在名單中」的法號選人器面板。 | 解決手機端打字不便的問題，點擊兩次即可快速補漏未選取的人員，且 `+` 按鈕永不失效。 |
| **Phase 2 狀態持久化** | 在已確認名單中點擊法號，改為切換「本輪是否參與 (`roundParticipants`)」，不再修改 `pendingNames`；新回合直接跳回 Step 3 並預選所有人員。 | 徹底解決了回合抽籤時因點擊導致名單遺失，以及新回合需重新點選的不便，實現極高效率連續作業流程。 |
| **草稿語境提示** | 抽籤草稿載入提示，在回合抽籤模式下改為「已有在場人員名單（X 人），是否要載入？」 | 更精準地提醒使用者該草稿的作用，避免誤覆蓋辛苦選好的人員名單。 |

## Session Notes (2026-05-19)

### Registry Dharma Column Width & Remarks Center Alignments
- **Registry Dharma Column Grid Optimization**: Adjusted both view and edit mode column widths to guarantee perfect visual display constraints:
  - **Edit Mode Table**: Changed column widths to `w-[38%]` (法號), `w-[27%]` (日期), `w-[31%]` (備註), `w-[4%]` (刪除) to exactly fit 5 Chinese characters for Dharma names while shifting date/remarks inputs left/forward.
  - **View Mode Tables**: Changed column widths to `w-[38%]` (法號), `w-[28%]` (日期), `w-[34%]` (備註) across both major category and other categories tables.
- **Remarks Center Alignment**: Changed remarks `div` container class in View Mode tables from `justify-start` to `justify-center` and target table cells `td` class from `text-left` to `text-center` so that remarks content (and empty indicator `--`) are perfectly centered relative to the headers.
- **Database Auto Backup & Restore System**:
  - **Root Cause of Data Loss**: Custom sub-folders in Imperial Grace and Registries are calculated based on database records of specific Masters. Running `php artisan db:seed` or refreshing databases would wipe all tables and replace them with standard seeder defaults, causing custom sub-folders and records to disappear.
  - **Solution (backup_db.php)**: Created a fast backup script in the workspace root. Run `php backup_db.php` to export 18 key database tables to individual formatted JSON files inside `database/seeders/data/` (keeps live data perfectly preserved).
  - **Restore Mechanism (DatabaseSeeder.php)**: Integrated automatic backup file detection into the core `DatabaseSeeder`. It automatically checks for `users.json` and `treasures.json` backups; if found, it disables foreign key checks, truncates database tables, and restores custom records with 100% fidelity, avoiding any data loss during database refreshes.



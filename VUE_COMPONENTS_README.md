# 法寶系統 (Fabou_la) Vue 元件說明文件

這份文件概述了 `resources/js/components/` 目錄下的主要 Vue 元件及其負責的功能。本系統採用 Vue 3 (Composition API) 撰寫，並遵循 Mobile-First (行動端優先) 的 UI 設計規範。

---

## 1. 核心架構與導覽 (Core Layout & Navigation)

*   **`AdminDashboard.vue`**
    系統的主控台容器。負責左側選單的渲染與路由切換（使用 `#hash` 導航，如 `#grace`, `#treasure`），並動態載入對應的 Manager 元件。
*   **`MobileNavbar.vue`**
    底部固定導覽列（Mobile 首選 UI）。包含五個主要按鈕：返回、首頁、功能按鈕 (`+`號)、搜尋、設定/字體大小調整。它會將使用者的點擊事件 `$emit` 回傳給父元件處理。
*   **`AddActionMenu.vue`**
    點擊底部導覽列 `+` 號後彈出的統一「動作選單」。負責顯示「逐筆新增」、「多筆新增」、「匯出 Excel」、「手動排序」等操作選項。

---

## 2. 主要管理專區 (Main Manager Modules)

這些檔案是系統最核心的功能模組，分別對應側邊欄的八大專區：

*   **`ImperialGraceManager.vue` (重大皇恩專區)**
    負責管理重大皇恩紀錄。依據仙師（Master）分類資料夾，擁有自己專屬的列表展示與搜尋邏輯。
*   **`TeachingManager.vue` (父皇仙師開示專區)**
    負責管理仙師的開示、指點紀錄。
*   **`GrudgeManager.vue` (怨靈記錄專區)**
    負責管理怨靈相關的渡化或登記紀錄。
*   **`MilitaryManager.vue` (軍隊記錄專區)**
    負責管理「虎甲軍」等軍隊紀錄。具有獨特的「按日期分組（Date Grouping）」以及「自動展開/收合」列表 UI。
*   **`RegistryManager.vue` (法寶登記專區)**
    *對應網址 `#treasure`*。主要管理法寶的請領、登記紀錄（Dharma Name Registries）。區分為「重大皇恩登記簿」與「其他皇恩登記簿」。
*   **`KaiwenManager.vue` (開文專區)**
    處理開文、核定表相關的資料與狀態展示。包含對應的子元件 `KaiwenApproval.vue`。
*   **`OtherManager.vue` (其他專區)**
    管理無法歸類於上述專區的雜項，包含特殊的「隨機分組」功能模組。
*   **`TrashManager.vue` (資源回收筒)**
    顯示被標記為刪除（Soft Delete）的紀錄，提供還原或永久刪除的操作。

---

## 3. 表單與輸入模組 (Forms & Input)

當使用者在 Manager 中點擊「新增」時，會動態呼叫以下表單元件來處理資料輸入（皆支援單筆與批次輸入）：

*   **`ImperialGraceAddForm.vue`**：皇恩紀錄專用表單。
*   **`RegistryAddForm.vue`**：法寶登記專用表單，負責處理法寶名稱、用意、與多個法號的關聯輸入。
*   **`MilitaryAddForm.vue`** & **`MilitaryBatchAdd.vue`**：軍隊紀錄專用表單。
*   **`GrudgeAddForm.vue`** & **`GrudgeBatchImport.vue`**：怨靈記錄專用表單。

---

## 4. 共用與特殊互動元件 (Shared & Special Components)

*   **`CompactDatePicker.vue`**
    客製化的緊湊型日期選擇器，用於在狹小的行動端介面上快速修改時間，而不需依賴瀏覽器原生的醜陋日期元件。
*   **`RemarksViewer.vue`**
    「備註」檢視器與編輯器。當列表中的備註太長或需要換行時，點擊會彈出此模態框（Modal）供閱讀及修改。
*   **`RandomGroup.vue`**
    隨機分組產生器（主要被 `OtherManager.vue` 呼叫）。可以將人員亂數分組，並即時切換正反選狀態與重置。
*   **`LuckyDraw.vue`**
    抽籤或幸運轉盤/抽獎相關的互動元件。

---

## 5. 後台基本資料管理 (Admin / CRUD Components)

通常存放在 `resources/js/components/admin/` 目錄下，由擁有 `is_admin` 權限的帳號才能看見：

*   **`DharmaCrud.vue`**：管理所有的「人員 / 法號」基本資料庫。
*   **`MasterCrud.vue`**：管理所有的「仙師」清單（建立資料夾的依據）。
*   **`TreasureCrud.vue`**：管理法寶的「主檔名稱」辭典。
*   **`GroupCrud.vue`**：管理「群組與職務」的架構設定。
*   **`UserCrud.vue`**：管理能登入系統的後台帳號與權限。

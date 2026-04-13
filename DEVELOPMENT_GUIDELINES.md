# Fabou Archival System - 開發準則 (Development Guidelines)

本文件定義了本專案的開發規範與架構原則，所有開發人員應嚴格遵守以維持代碼品質與一致性。

---

## 1. 前端開發規範 (Frontend Standards)

### 1.1 響應式設計：手機優先 (Mobile-First)
- **原則**：所有組件與頁面必須先針對行動裝置（手機）進行設計與開發，再透過 Tailwind CSS 的斷點 (`md:`, `lg:`) 擴展至平版與桌面端。
- **介面佈局**：在小螢幕上應使用垂直卡片流或抽屜式導航；在大螢幕上則轉換為表格或側邊欄佈局。

### 1.2 設計風格：白色主題 (White/Light Theme)
- **色調**：使用簡潔的白色與淺灰色作為主背景。
- **美學**：追求現代、清爽的介面感覺，避免沉重的深色調。使用高品質的間距 (Spacing) 與字體排版來提升質感。

### 1.3 技術棧：Vue 3 Composition API
- **語法規範**：必須使用 `<script setup>` 語法糖。
- **組件化**：將複雜的邏輯拆分為可重用的 Composables (例如 `useAuth.js`, `useRecords.js`)。
- **狀態管理**：優先使用 Composition API 的 `ref` / `reactive` 處理組件內狀態；跨組件狀態則使用 Pinia。

---

## 2. 後端與架構規範 (Backend & Architecture)

### 2.1 路由管理 (Routing)
- **主要檔案**：大部分路由應定義在 `routes/web.php` 中。
- **路由規範**：嚴格遵守 **RESTful API** 命名約定。
  - `GET /masters` - 取得列表 (index)
  - `POST /masters` - 新增資源 (store)
  - `GET /masters/{id}` - 取得單一資源 (show)
  - `PUT/PATCH /masters/{id}` - 更新資源 (update)
  - `DELETE /masters/{id}` - 刪除資源 (destroy)

### 2.2 代碼架構：精簡控制器 (Thin Controllers)
- **控制器角色**：Controller 僅負責處理請求驗證 (Request Validation)、調用服務層、以及回傳回應 (Response)。
- **邏輯分層**：禁止在 Controller 中編寫複雜的業務邏輯或資料庫操作。
- **Service 層**：所有的核心業務邏輯、演算法、與第三方整合應封裝在 `app/Services` 目錄下的 Service 類別中。

### 2.3 資料庫操作 (Eloquent ORM)
- **模型優先 (Model-Only)**：所有資料庫操作應盡量透過 **Eloquent Model** 完成，盡量避免使用 `DB` Facade 或原生 SQL 語句。
- **功能特性**：充分利用模型的 `Scopes` (篩選器)、`Accessors` (存取器)、`Mutators` (修改器) 與 `Relations` (關聯)。
- **遷移檔**：必須包含完整的外鍵約束 (Foreign Keys) 與必要的索引 (Indexes)。

---

## 3. 代碼風格 (Coding Style)
- **變數命名**：後端 PHP 使用 `snake_case`，前端 JS/Vue 使用 `camelCase`。
- **註釋**：複雜的業務邏輯必須附帶中文註釋說明。
- **Git Commit**：使用有意義的標題 (例如 `feat:`, `fix:`, `docs:`)。

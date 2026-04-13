# 法寶錄載系統 - 資料庫模型與遷移設計方案 (Laravel 版)

本文件根據舊版 Firestore 架構，設計與之對應的 Laravel Eloquent 模型關係 (Relationships) 以及資料庫遷移 (Migrations) 虛擬碼。

---

## 1. 資料庫模型關係 (Eloquent Relationships)

### User (使用者)
- `belongsToMany(Role::class)`: 一個使用者可以擁有多個職責標籤。
- `hasOne(DharmaName::class)`: 一個使用者對應一個法號（核心識別）。
- `hasMany(ImperialGrace::class)`: 使用者可錄載重大皇恩紀錄。
- `hasMany(Teaching::class)`: 使用者可錄載開示紀錄。
- `hasMany(OtherRecord::class)`: 使用者可錄載軍事或其他紀錄。

### DharmaName (法號表)
- `belongsTo(User::class)`: 隸屬於特定使用者。
- `hasMany(OtherRecord::class)`: 法號可被引用於各種錄載紀錄中。

### Role (職責/權限)
- `belongsToMany(User::class)`: 多個使用者可以擁有相同的職責標職。
- *權限邏輯*: 在代碼層面控制 `allowed_master_ids` 與 `allowed_folder_ids`。

### Master (仙師/核心目錄)
- `hasMany(ImperialGrace::class)`: 一位仙師擁有多筆重大皇恩紀錄。
- `hasMany(Teaching::class)`: 一位仙師擁有多筆開示紀錄。
- `hasMany(OtherRecord::class)`: 紀錄中可能引用仙師聖號。

### OtherFolder (其他/軍種資料夾)
- `hasMany(OtherRecord::class)`: 一個資料夾下擁有多筆詳細紀錄 (如黑曜軍、虎甲軍等)。
- *註記*: 此模型專門用於分類軍事單位及非仙師專區的資料錄載。

### ImperialGrace (重大皇恩)
- `belongsTo(Master::class)`: 隸屬於特定仙師。

### Teaching (開示)
- `belongsTo(Master::class)`: 隸屬於特定仙師。

### OtherRecord (其他紀錄/軍事紀錄)
- `belongsTo(OtherFolder::class)`: 隸屬於特定資料夾。
- `belongsTo(Master::class)`: 可選，引用關聯的仙師聖號。

---

## 2. 遷移虛擬碼 (Migrations Pseudocode)

### Table: `masters` (仙師表)
```php
Schema::create('masters', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // 例如：閻王仙師
    $table->string('category'); // imperial | others | teaching
    $table->string('status')->default('active'); // active | inactive
    $table->integer('treasure_count')->default(0); // 運行總數統計
    $table->timestamps();
});
```

### Table: `imperial_graces` (重大皇恩表)
```php
Schema::create('imperial_graces', function (Blueprint $table) {
    $table->id();
    $table->foreignId('master_id')->constrained(); // 關聯仙師
    $table->string('treasure_name'); // 法號/名稱
    $table->text('description')->nullable(); // 詳細內容
    $table->date('record_date'); // 紀錄日期
    $table->date('know_date')->nullable(); // 得知日期
    $table->date('report_date')->nullable(); // 回報日期
    $table->string('display_status'); // 已登記 / 已求得 / 未求得
    $table->string('status')->default('active'); // active | trash
    $table->integer('order')->default(0); // 排序權重
    $table->json('excel_rows')->nullable(); // 核心表格資料 (如 40 人名單)
    $table->foreignId('user_id')->constrained(); // 操作員 ID
    $table->timestamps();
});
```

### Table: `teachings` (開示表)
```php
Schema::create('teachings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('master_id')->constrained();
    $table->string('treasure_name'); // 開示主題
    $table->text('description')->nullable(); // 詳細開示內容
    $table->date('record_date');
    $table->string('status')->default('active');
    $table->json('excel_rows')->nullable(); // 額外表格結構
    $table->foreignId('user_id')->constrained(); // 記錄/操作員 ID
    $table->timestamps();
});
```

### Table: `other_folders` (軍種/其他資料夾表 - 專用)
```php
Schema::create('other_folders', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // 例如：黑曜軍、虎甲軍、虎賁軍、耀紫軍
    $table->string('folder_code')->unique(); // 識別碼，如 'MIL_ARMOR'
    $table->timestamps();
});
```

### Table: `other_records` (其他詳細紀錄表)
```php
Schema::create('other_records', function (Blueprint $table) {
    $table->id();
    $table->foreignId('other_folder_id')->constrained(); // 所屬資料夾
    $table->foreignId('master_id')->nullable()->constrained(); // 關聯仙師(可選)
    $table->string('title'); // 標題
    $table->text('content')->nullable(); // 內容
    $table->date('record_date');
    $table->json('excel_rows')->nullable(); // 批次錄入名單
    $table->foreignId('user_id')->constrained(); // 記錄/操作員 ID
    $table->timestamps();
});
```

### Table: `roles` & `role_user` (職責權限表)
```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Admin, Military_Armor, etc.
    $table->string('role_code')->unique(); // R_ADMIN, R_MIL_ARMOR
    $table->json('allowed_master_ids')->nullable(); // 權限範圍鎖定
    $table->timestamps();
});

Schema::create('role_user', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained();
    $table->foreignId('role_id')->constrained();
});

### Table: `dharma_names` (法號表)
```php
Schema::create('dharma_names', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained(); // 關聯使用者ID
    $table->string('name')->unique(); // 法號 (如：元續)
    $table->string('status')->default('active'); // 狀態
    $table->text('remarks')->nullable(); // 備註
    $table->timestamps();
});
```
```

---

## 3. 核心實施策略

1. **JSON 欄位應用**: `excel_rows` 將使用 Laravel 的 JSON 轉型功能 (`AsArrayObject` 或 `AsCollection`)，以便快速處理舊版中的陣列資料結構。
2. **軟刪除邏輯**: 雖然可以使用 Laravel 內建的 `SoftDeletes` 特性，但為了對齊舊版 `status='trash'` 的邏輯，建議在查詢時加上 `where('status', 'active')` 全域作用域 (Global Scope)。
3. **效能優化**: 針對 `record_date` 與 `master_id` 建立複合索引，確保在大量紀錄下仍能快速檢索。

# 系統資料庫與模組對應說明

本文件旨在釐清「歸檔專區 (Registries)」與「重大皇恩 (Imperial Grace)」兩者之區隔：

### 1. 歸檔專區 (Registries)
- **資料表**: `registries` / `user_registries`
- **Model**: `Registry` / `UserRegistry`
- **Controller**: `RegistryController`
- **Vue 元件**: `RegistryManager.vue` / `RegistryAddForm.vue`
- **用途**: 此為 **總登記表 (Master List)**。專供特定專員登記「大家的所有法寶 (重大皇恩)」，作為一個獨立的彙整總表。與個別使用者的個人紀錄互不隸屬，是為了架構解耦而設立的獨立模組。

### 2. 重大皇恩 (Imperial Grace)
- **資料表**: `imperial_graces`
- **Model**: `ImperialGrace`
- **用途**: 此為 **個人/特定性質** 的皇恩紀錄，與上述的總登記表在資料結構上是完全分開的兩個獨立個件。

### 注意事項：
- 雖然兩者在業務語意上都與「法寶登記」相關，但在系統實作上是**互不隸屬 (Mutually Exclusive)** 的兩個獨立模組。
- 在開發與維護時，請務必區分 API 路徑：
  - `/registries/...` -> 歸檔專區 (總表)
  - (未來規畫路徑) -> 重大皇恩個人紀錄

---
*備註：此筆記建立於 2026-04-16，用於釐清系統去耦後的架構。*

# 開示紀錄與團體架構重構討論 (Refactor Discussion)

## 1. 現狀分析 (Current State)
目前 `teachings` 資料表使用單一的外鍵 (Foreign Key) 來關聯團體與法號：
*   `group_id`: 指向單一團體。
*   `dharma_name_id`: 指向單一法號。

**面臨的問題**：
一筆開示紀錄往往是針對「多個人」或「多個特定對象」發布的，傳統的 1-to-1 關聯無法滿足需求。

---

## 2. 最終架構：人本主義模式 (Individual-Centric Architecture)

我們將重心放在「法號 (人)」身上。不論在前端如何選擇，資料庫儲存的都是「這筆開示發給了哪些人」。

### A. 資料表瘦身 (Table Cleanup)
*   **修改 `teachings`**：刪除 `group_id` 與 `dharma_name_id`。
*   **邏輯**：開示內容不再標記「屬於哪個群組」，只儲存內容、日期與發布者。

### B. 建立中間表 (Pivot Tables)
1.  **`teaching_dharma_name`** (`teaching_id`, `dharma_name_id`)
    *   **核心用途**：紀錄這筆開示**發布給了哪些法號**。這是系統唯一的開示歸屬依據。
2.  **`group_dharma_name`** (`group_id`, `dharma_name_id`)
    *   **輔助用途**：定義群組成員。例如「老祖組」包含 A、B、C。
    *   **前端應用**：在新增紀錄時，當您選擇某個群組，前端會讀取此表並自動勾選 A、B、C。

---

## 3. 方案優勢 (Advantages)

1.  **邏輯極簡**：後端只需要關注「開示人 -> 接收人」的關聯。
2.  **彈性最大**：群組只是用來「快速勾選」的工具。如果您勾選了某群組後，想臨時取消其中一個人的勾選，資料庫依然能精準紀錄。
3.  **未來場景**：如果某人某天換了組，歷史開示的權限依然會留在該法號身上，不會因為群組變動而消失。

---

## 4. 具體異動計畫 (Implementation Steps)

### 第一步：遷移 (Migration)
1.  修改 `teachings`：
    *   `$table->dropForeign(['group_id', 'dharma_name_id']);` (依據現狀刪除外鍵)
    *   `$table->dropColumn(['group_id', 'dharma_name_id']);`
2.  建立兩個中間表 Migration：`teaching_dharma_name` 與 `group_dharma_name`。

### 第二步：模型處理 (Model Logic)
1.  `Teaching` 模型定義 `dharmaNames()` 為 `belongsToMany`。
2.  `Group` 模型定義 `dharmaNames()` 為 `belongsToMany`。
3.  `DharmaName` 模型可選定義 `teachings()` 與 `groups()`。

### 第三步：前端更新 (`TeachingManager.vue`)
1.  **選人介面優化**：
    *   上方顯示群組清單。
    *   點擊群組時，下方對應的法號成員自動設為「選中狀態 (Checked)」。
    *   使用者也可以個別勾選/取消特定的法號。
2.  **資料傳輸**：儲存時只傳送 `dharma_name_ids` 陣列給後端。

---

## 5. 使用場景示範 (User Story)
*   **動作**：使用者在後台新增開示，點擊「老祖組」。
*   **前端表現**：自動勾選了老祖組的 5 位成員。使用者手動取消其中 1 位。
*   **儲存結果**：資料庫 `teaching_dharma_name` 存入 4 筆記錄。
*   **查詢結果**：不論未來群組如何變動，這 4 位成員永遠能看到這筆開示。

---
**請確認是否有需要調整的部分，我們達成共識後再進行程式碼實作。**

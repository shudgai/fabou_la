# Fabou Archival System - Comprehensive Development Plan

This document consolidates the architecture, requirements, and implementation details from the legacy `fabou` project to guide the development of the new Laravel-integrated version (`fabou_la`).

## 1. Project Overview
The Fabou Archival System is a high-performance data integration platform designed for collecting, organizing, and viewing spiritual and military records. It features a mobile-first, high-density interface with real-time synchronization.

## 2. Technology Stack
- **Frontend**: Vue 3 (Composition API with `<script setup>`)
- **Styling**: Tailwind CSS (RWD, Mobile-First)
- **Animations**: GSAP (GreenSock Animation Platform)
- **State/Routing**: Vue Router, Axios
- **Backend (Legacy)**: Firebase (Firestore, Auth)
- **Backend (Target)**: Laravel (PHP 8.x) + MySQL/PostgreSQL (to be mapped from Firestore schema)

## 3. Database Architecture (Firestore Mapping)

### 3.1 Core Collections
| Collection | Description | Key Fields |
| :--- | :--- | :--- |
| `masters` | Folder/Master entities | `name`, `category`, `status`, `treasureCount` |
| `imperial_graces` | Central spiritual records | `masterId`, `treasureName`, `recordDate`, `excelRows` |
| `teachings` | Spiritual teachings | `masterId`, `treasureName`, `description`, `recordDate` |
| `others_records` | Specialized unit records | `folderId`, `masterId`, `title`, `content`, `excelRows` |

### 3.2 Data Patterns
- **Denormalization**: `masterName` is stored within record documents to avoid joins.
- **Status Management**: Uses `status` field (`active`, `trash`) for soft-deletion and archival.
- **Flexible Content**: `excelRows` stores complex tabular data as JSON arrays.
- **Tag Parsing**: `description` fields often use `【Tag Name】` for dynamic UI rendering.

## 4. Role-Based Access Control (RBAC)

| Role ID | Title | Access Scope |
| :--- | :--- | :--- |
| `R_ADMIN` | Admin | Full global access to all data and user management. |
| `R_TEMPLE_OFFICER` | Temple Key Personnel | Core teachings, Military units (Obsidian, Armor, Ben, Purple). |
| `R_MIL_BLACK` | Obsidian Unit | Access to Obsidian-specific folders/records. |
| `R_MIL_ARMOR` | Tiger Armor Unit | Access to Tiger Armor-specific folders/records. |
| `R_RECORDER` | Recorder | Data entry only, isolated from general viewing. |
| `R_MEMBER` | General Member | Personal isolated folder access. |

## 5. Core Features & Functional Requirements

### 5.1 Interface & Navigation
- **One-Master-One-Interface**: Unique management page for each spiritual master.
- **High-Density Grid**: Optimized list views that switch to vertical cards on mobile.
- **Sticky Headers/Actions**: Critical controls (Add, Export, Filter) remain accessible.

### 5.2 Data Entry & Management
- **Multi-Source Import**: Support for batch-pasting from Google Keep/Notes.
- **Dharma Registration Template**: Pre-populated 40-name registration tables.
- **Real-Time Sync**: UI updates immediately upon database changes.
- **Duplicate Prevention**: Hard-blocking duplicates (Date + Name + Content) except for specific units.

### 5.3 Specialized Modules
- **Military Units**: Discrete data paths for Obsidian (黑曜), Tiger Armor (虎甲), Tiger Ben (虎賁), and Purple Glory (耀紫).
- **Recycle Bin**: `TrashView` for restoring or permanently deleting `trash` status records.

## 6. UI/UX Design System
- **Aesthetic**: Premium, modern look using subtle gradients and micro-animations.
- **Responsiveness**: Mobile-first design; tables collapse into cards on small screens.
- **Interactions**: GSAP-powered page transitions and entry animations.

## 7. Migration Roadmap (`fabou` -> `fabou_la`)

1. **Environment Setup**: (Completed) Laravel + Vite + Vue 3 + Tailwind CSS.
2. **Schema Mapping**: Convert Firestore document structures into Laravel Migrations.
3. **Component Porting**:
   - Refactor `RecordManager.vue` (Legacy 300KB component) into smaller, reusable Blade/Vue components.
   - Migrate `OtherDetail.vue` and `MasterDetail.vue` logic.
4. **Auth Integration**: Replace Firebase Auth with Laravel Fortify/UI (Current `php artisan ui vue --auth`).
5. **Data Transfer**: Scripted migration of legacy Firestore data to the new SQL database.

---
*Created: 2026-04-13*

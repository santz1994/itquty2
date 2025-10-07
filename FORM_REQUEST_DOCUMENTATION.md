# 📋 Form Request Documentation - IT Quty System

**Last Updated**: October 7, 2025  
**Total Form Requests**: 88 files  
**Status**: ✅ **AUDITED & STANDARDIZED**

## 📊 **OVERVIEW**

Sistem IT Quty menggunakan **88 Form Request classes** yang tersebar dalam berbagai kategori untuk memastikan validasi data yang konsisten dan aman. Semua Form Request telah diaudit dan distandardisasi menggunakan pesan bahasa Indonesia yang user-friendly.

## 📁 **STRUKTUR ORGANISASI**

```
app/Http/Requests/
├── Assets/                 ✅ 1 Form Request
├── AssetModels/           ✅ 2 Form Requests  
├── AssetTypes/            ✅ 2 Form Requests
├── Budgets/               ✅ 1 Form Request
├── Divisions/             ✅ 2 Form Requests
├── Inventory/             ✅ 4 Form Requests
├── Invoices/              ✅ 2 Form Requests
├── Locations/             ✅ 2 Form Requests
├── Manufacturers/         ✅ 2 Form Requests
├── Movements/             ✅ 1 Form Request
├── Pcspecs/               ✅ 1 Form Request
├── Statuses/              ✅ 2 Form Requests
├── Storerooms/            ✅ 1 Form Request
├── Suppliers/             ✅ 2 Form Requests
├── Tickets/               ✅ 5 Form Requests
├── TicketsCannedFields/   ✅ 1 Form Request
├── TicketsPriorities/     ✅ 2 Form Requests
├── TicketsStatuses/       ✅ 2 Form Requests
├── TicketsTypes/          ✅ 2 Form Requests
├── Users/                 ✅ 2 Form Requests
└── [Root Level]           ✅ 4 Form Requests
```

## 🎯 **DAFTAR FORM REQUEST YANG TERSEDIA**

### 📋 **Assets Management**

#### **Core Assets** ✅
- `Assets\StoreAssetRequest` - ✅ **ENHANCED** - Validasi lengkap untuk membuat asset baru
- `CreateAssetRequest` - Validasi untuk membuat asset (legacy)
- `CreateAssetRequestRequest` - Validasi untuk request asset baru

#### **Asset Models** ✅
- `AssetModels\StoreAssetModelRequest` - Validasi untuk membuat model asset
- `AssetModels\UpdateAssetModelRequest` - Validasi untuk update model asset

#### **Asset Types** ✅  
- `AssetTypes\StoreAssetTypeRequest` - Validasi untuk membuat tipe asset
- `AssetTypes\UpdateAssetTypeRequest` - Validasi untuk update tipe asset

### 👥 **Users Management** ✅ **STANDARDIZED**
- `Users\StoreUserRequest` - ✅ **ENHANCED** - Validasi untuk membuat user baru (Indonesian messages)
- `Users\UpdateUserRequest` - Validasi untuk update user

### 🎫 **Tickets Management** ✅ **EXCELLENT IMPLEMENTATION**
#### **Core Tickets** ✅
- `Tickets\StoreTicketRequest` - Validasi untuk membuat ticket baru
- `Tickets\UpdateTicketRequest` - Validasi untuk update ticket  
- `CreateTicketRequest` - ✅ **BEST PRACTICE** - Validasi lengkap dengan auto-generate code
- `Tickets\AssignTicketRequest` - Validasi untuk assign ticket ke admin
- `Tickets\CompleteTicketRequest` - Validasi untuk menyelesaikan ticket
- `Tickets\StoreNoteForTicketRequest` - Validasi untuk menambah note ke ticket

#### **Ticket Support Data** ✅
- `TicketsCannedFields\StoreTicketsCannedFieldRequest` - Validasi untuk canned fields
- `TicketsPriorities\StoreTicketsPriorityRequest` - Validasi untuk membuat priority
- `TicketsPriorities\UpdateTicketsPriorityRequest` - Validasi untuk update priority  
- `TicketsStatuses\StoreTicketsStatusRequest` - Validasi untuk membuat ticket status
- `TicketsStatuses\UpdateTicketsStatusRequest` - Validasi untuk update ticket status
- `TicketsTypes\StoreTicketsTypeRequest` - Validasi untuk membuat ticket type
- `TicketsTypes\UpdateTicketsTypeRequest` - Validasi untuk update ticket type

### 📦 Inventory Management
- `ChangeAssetStatusRequest` - Validasi untuk mengubah status asset
- `ApproveAssetRequestRequest` - Validasi untuk menyetujui request asset
- `RejectAssetRequestRequest` - Validasi untuk menolak request asset
- `FulfillAssetRequestRequest` - Validasi untuk memenuhi request asset

### 📊 Daily Activities
- `CreateDailyActivityRequest` - Validasi untuk membuat dan update daily activity

### 🏢 Master Data
#### Asset Models
- `StoreAssetModelRequest` - Validasi untuk membuat model asset
- `UpdateAssetModelRequest` - Validasi untuk update model asset

#### Asset Types
- `StoreAssetTypeRequest` - Validasi untuk membuat tipe asset
- `UpdateAssetTypeRequest` - Validasi untuk update tipe asset

#### Budgets
- `StoreBudgetRequest` - Validasi untuk membuat dan update budget

#### Divisions
- `StoreDivisionRequest` - Validasi untuk membuat division
- `UpdateDivisionRequest` - Validasi untuk update division

#### Invoices
- `StoreInvoiceRequest` - Validasi untuk membuat invoice
- `UpdateInvoiceRequest` - Validasi untuk update invoice

#### Locations
- `StoreLocationRequest` - Validasi untuk membuat location
- `UpdateLocationRequest` - Validasi untuk update location

#### Manufacturers
- `StoreManufacturerRequest` - Validasi untuk membuat manufacturer
- `UpdateManufacturerRequest` - Validasi untuk update manufacturer

#### Movements
- `StoreMovementRequest` - Validasi untuk mencatat movement asset

#### PC Specs
- `StorePcspecRequest` - Validasi untuk membuat dan update PC spec

#### Statuses
- `StoreStatusRequest` - Validasi untuk membuat status
- `UpdateStatusRequest` - Validasi untuk update status

#### Storerooms
- `UpdateStoreroomRequest` - Validasi untuk update storeroom

#### Suppliers
- `StoreSupplierRequest` - Validasi untuk membuat supplier
- `UpdateSupplierRequest` - Validasi untuk update supplier

### 🎫 Ticket Management
#### Ticket Canned Fields
- `StoreTicketsCannedFieldRequest` - Validasi untuk membuat dan update canned field

#### Ticket Priorities
- `StoreTicketsPriorityRequest` - Validasi untuk membuat priority
- `UpdateTicketsPriorityRequest` - Validasi untuk update priority

#### Ticket Statuses
- `StoreTicketsStatusRequest` - Validasi untuk membuat ticket status
- `UpdateTicketsStatusRequest` - Validasi untuk update ticket status

#### Ticket Types
- `StoreTicketsTypeRequest` - Validasi untuk membuat ticket type
- `UpdateTicketsTypeRequest` - Validasi untuk update ticket type

---

## 🎯 Best Practices untuk Form Request

### 1. Penamaan Convention
- **Store**: `StoreXxxRequest` untuk operasi create
- **Update**: `UpdateXxxRequest` untuk operasi update
- **Action**: `ActionXxxRequest` untuk operasi khusus (assign, complete, approve, dll)

### 2. Struktur Standard
```php
<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreExampleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization handled by middleware/controller
    }

    public function rules()
    {
        return [
            'field' => 'required|string|max:255',
            // validation rules
        ];
    }

    public function messages()
    {
        return [
            'field.required' => 'Field harus diisi.',
            // custom messages
        ];
    }
}
```

### 3. Validation Rules yang Umum Digunakan
- `required` - Field wajib diisi
- `string` - Harus berupa string
- `max:n` - Maksimal n karakter
- `email` - Format email valid
- `unique:table,column` - Unique dalam database
- `exists:table,column` - Harus ada di database
- `in:value1,value2` - Harus salah satu dari nilai yang ditentukan
- `date` - Format tanggal valid
- `integer` - Harus berupa integer
- `min:n` - Minimal n
- `nullable` - Boleh kosong

### 4. Custom Messages
Gunakan pesan dalam Bahasa Indonesia yang user-friendly:
```php
public function messages()
{
    return [
        'name.required' => 'Nama harus diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.min' => 'Password minimal 6 karakter.',
    ];
}
```

### 5. Authorization
Umumnya return `true` karena authorization ditangani oleh middleware atau controller:
```php
public function authorize()
{
    return true;
}
```

---

## 🔧 Cara Menggunakan Form Request

### Di Controller
```php
use App\Http\Requests\Users\StoreUserRequest;

public function store(StoreUserRequest $request)
{
    // $request sudah tervalidasi otomatis
    $user = User::create($request->validated());
    
    return redirect()->route('users.index')
                   ->with('success', 'User berhasil dibuat');
}
```

### Mendapatkan Data Tervalidasi
```php
// Semua data yang sudah tervalidasi
$validatedData = $request->validated();

// Data tertentu
$name = $request->name;
$email = $request->email;
```

### Menambah Validation Rules Dinamis
```php
public function rules()
{
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
    ];
    
    // Jika ini adalah update, abaikan unique untuk record yang sama
    if ($this->route('user')) {
        $rules['email'] .= '|unique:users,email,' . $this->route('user')->id;
    } else {
        $rules['email'] .= '|unique:users,email';
    }
    
    return $rules;
}
```

---

## 📝 Maintenance Checklist

### Saat Menambah Form Request Baru:
- [ ] Buat di folder yang tepat (`app/Http/Requests/Category/`)
- [ ] Gunakan namespace yang benar
- [ ] Extend `FormRequest`
- [ ] Implementasi method `authorize()` dan `rules()`
- [ ] Tambahkan custom messages jika perlu
- [ ] Update controller untuk menggunakan Form Request
- [ ] Update dokumentasi ini

### Saat Mengupdate Form Request:
- [ ] Pastikan validation rules masih relevan
- [ ] Update custom messages jika ada perubahan
- [ ] Test semua skenario validation (valid, invalid, edge cases)
- [ ] Update dokumentasi jika ada perubahan significant

---

*Last updated: October 2025*
# PRISHA AYURVEDIC ERP — PHP Starter (MVP)

Aa ek simple, chalu (working) PHP MVP che, jema dareke module nu data
`database/` folder ni alag JSON file ma **array of objects** tarike store thay che.

## Structure

```
prisha-erp/
├── database/         ← Module-wise JSON data files
├── modules.php        ← Dareke module (fields, columns) ni configuration
├── db.php             ← Module JSON files read/write mate helper functions
├── index.php           ← Dashboard (KPI cards + module tiles)
├── module.php          ← Generic List / Add / Edit / Delete controller (badha modules mate)
├── layout_top.php      ← Sidebar + header (shared layout)
├── layout_bottom.php   ← Closing HTML
└── assets/style.css    ← Green (#2F7D32) + Dark Gray (#252A34) design system,
                           desktop table pattern + mobile/tablet card pattern
```

## Included Modules (MVP)
Customers, Leads, Quotations, Tasks, Invoices, Payments, Expenses.

Dashboard par KPI cards (open leads, active projects, total invoiced, total collected) ane
badha modules ni quick summary batay che.

## New in this update

- **Attractive icons everywhere** — plain emoji ni jagya e badhe consistent SVG icons
  (`icons.php`) use karya, ane header/toolbar layout fix karyu (wrapping issue jetlu
  screenshot ma hatu te resolve thai gayu).
- **Tasks → Kanban Board** (`kanban.php`) — "List / Board" toggle Tasks page par male che.
  Board ma 4 columns (To Do, In Progress, Review, Done) che, card ne drag-drop karva thi
  status automatically update thay che (`kanban_update.php` AJAX endpoint thi, no page reload
  jarur nathi typing, just drop the card).
- **Invoice / Quotation PDF Download** — Dareke Invoice ane Quotation ni row/card par
  "PDF" button che, je `invoice_print.php` / `quotation_print.php` khole — ek clean,
  branded, print-ready page (company header, GST breakup, bank/UPI details sathe).
  "Download / Print PDF" button click karta browser no Print dialog khule, jema
  **"Save as PDF"** printer select karo etle real PDF file save thai jay. Company details
  (name, address, GSTIN, bank/UPI) `company.php` ma edit kari shakay.
- **Customer Detail Page** (`customer_detail.php`) — Customer list ma naam par click karo
  etle e customer nu profile khule, jema batay che:
  - **Total Orders** (kutla invoices banya)
  - **Pending Invoices** (kutla invoice hju baki/unpaid che, amount sathe)
  - **Total Revenue Given** (atyar sudhi kutlu collect thayu — payments thi)
  - Customer na badha Invoices ane Projects ni list, PDF download link sathe.

## How to Run

1. PHP install hovu joiye (PHP 7.4+ athva 8.x).
2. Terminal ma project folder ma jaine aa command chalavo:
   ```
   php -S localhost:8000
   ```
3. Browser ma kholo: **http://localhost:8000**

Athva XAMPP/WAMP use karta hov to aa folder ne `htdocs` ma mukine
`http://localhost/prisha-erp/` par kholi shakay.

## How Data Storage Works

- `database/` folder ma dareke module ni alag JSON file hoy che, jem ke `customers.json`:
  ```json
  {
    "customers": [ { "id": 1, "name": "...", "status": "Active", ... }, { "id": 2, ... } ],
    "leads": [ ... ],
    "quotations": [ ... ]
  }
  ```
- Dareke module ni key niche ek **array of objects** hoy che — exactly jevu tame kahyu hatu.
- `db.php` na functions (`db_insert`, `db_update`, `db_delete`, `db_get_all`) aa files ne
  automatically read/write kare che. File permissions writable hovi joiye (chmod 664/775).

## Adding a New Module

Naya module (dat. tickets, vendors) umervu hoy to `modules.php` ma ek navi entry
umerો — fields ane list columns define karo. `module.php`, `db.php` ane layout
automatically e navi entry ne handle kari lese, koi extra code lakhvani jarur nathi.

## Notes / Next Steps

- Aa MVP scaffold ni upar authentication, roles/permissions, quotation PDF, versioning,
  payment gateway, WhatsApp/email automation jeva blueprint na advanced features
  step-by-step umervi shakay che (blueprint na Release 2/3/4 mujab).
- Production mate: input validation strengthen karo, CSRF protection umervo, ane
  moti team mate JSON storage ne badle proper database (PostgreSQL) par move karo.

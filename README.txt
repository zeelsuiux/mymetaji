===========================================================
MASTER PANEL + SHOPKEEPER PANEL - SETUP GUIDE (Gujlish)
===========================================================

FOLDER STRUCTURE
----------------
/master/            -> Tamaru (owner nu) Master Panel. Ahiya thi customer
                        (shopkeeper) create thay, subscription track thay.
/panel_template/     -> Master ni "blank copy" - dareke navi customer banta
                        aama thi ek navi copy /shops/ ma automatic ban jase.
                        Aa folder ne directly kadi vaparvani jarur nathi.
/shops/              -> Dareke shopkeeper ni potani panel copy ahiya
                        automatic create thay che (e.g. /shops/ramesh-shop/).

UPLOAD KEVI RITE KARVU
-----------------------
1. Aa akhi "final" folder ni andar ni badhi files/folders (master, shops,
   panel_template, .htaccess) tamara hosting na public_html (ke jyare pan
   domain point thatu hoy te folder) ma as-it-is upload karo.
2. PHP 7.4 ke 8.x hovu joiye (moti majority shared hosting par pahele thi j
   hoy che).
3. `database` folder ma PHP ne likhvani permission joiye - jarur padhe to
   `panel_template/database`, `master/database` ane `shops/*/database`
   folders ne 755 (ke 775) permission aapo.

MASTER PANEL - PEHLI VAKHAT LOGIN
-----------------------------------
1. Browser ma kholo: https://yourdomain.com/master/
2. Default login: username = admin , password = admin123
3. TARANT "Settings" ma jaine aa password badli nakho - aa khuj જરૂરી che,
   karan ke master panel પાસે badha customer na data ni access che.

NAVO CUSTOMER (SHOPKEEPER) BANAVVO
-----------------------------------
1. Master Panel > "Add Customer" par click karo.
2. Customer details bharo: Name, Number, Email, Address, Username, Password.
3. Nichej "First Subscription Purchase" ma Plan Amount, Payment Type
   (Cash/Online) ane Purchase Date nakho.
   -> Expiry Date AUTOMATIC 1 year pachi set thai jase.
4. "Save Customer" dabao etle:
   - Ek navi shopkeeper panel /shops/<naam-slug>/ automatic ban jase
   - Tena Admin login (username/password) e j hase jem tame nakhyu
   - License (expiry) pan set thai jase

Shopkeeper ni panel URL hase: https://yourdomain.com/shops/<slug>/
Aa link Master Panel ma dareke customer ni row/detail page par "Open Panel"
button thi pan malse.

RENEWAL (Subscription Purchase Ferithi)
------------------------------------------
1. Master Panel > Customers > temna naam par click karo (Customer Detail
   page khulse).
2. "Add Renewal / New Purchase" form ma navi Amount, Payment Type, Purchase
   Date nakho -> Save Purchase.
3. Tena shop ni expiry date tarat j update thai jase ane lock (jo hoy to)
   automatic hati jase.

PASSWORD BHULI GAYA / RESET KARVU HOY TO
-------------------------------------------
Customer Detail page par "Reset Shopkeeper Login Password" section thi
navu password set kari shakay (blank rakho to automatic random password
generate thai jase, screen par batashe - te shopkeeper ne aapo).

EXPIRY BEHAVIOR (SHOPKEEPER PANEL MA)
----------------------------------------
- Expiry na 15 divas pehla thi, shopkeeper na dashboard (ane badhi pages)
  par ek orange message batashe: "Your subscription will expire in X
  day(s)..."
- Expiry thai gaya pachi, ek red message batashe, ane shopkeeper:
    - Login kari sakse, badhu data JOI sakse (view/list/print)
    - PARANTU navu data Add kari nahi sake
    - Koi pan record Edit kari nahi sake
    - Koi pan record Delete kari nahi sake
    - Export (CSV/PDF bulk export) kari nahi sake
- Renewal (navi subscription purchase) Master Panel mathi thay etle
  aa lock tarat j hati jay, koi technical kaam karvani jarur nathi.

DELETE CUSTOMER
----------------
Customer Detail page ni niche "Danger Zone" ma thi customer ane teni
AAKHI shop panel data (badhu) delete kari shakay - aa PERMANENT che, backup
rakhi ne j karvu.

SECURITY NOTES (JAROORI)
--------------------------
1. Master Panel no default password (admin123) TARANT badli nakho.
2. `database` folders ma .htaccess already mukel che je direct browser thi
   JSON files kholta roke che - aa file delete na karso.
3. Sari rite hoy to /master/ folder ne separate/hidden URL ke IP-restriction
   thi vadhu protect kari shakay (optional, hosting control panel thi).
4. Dareke shopkeeper ni data tenna potana /shops/<slug>/database/ folder ma
   ALAG-ALAG store thay che - ek shop nu data bija shop ne dekhatu nathi.

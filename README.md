# 奶龍選美大賽

一個以「奶龍選美大賽」為主題的活動報名網站。專案包含響應式活動頁面、線上報名、MySQL 資料庫儲存、PDF 報名表產生、QR Code、Email 確認信、PWA、Discord Bot 查詢、自訂 403 / 404 頁面，以及 Cloudflare Tunnel 自訂網域部署。

> 專案版面以 TemplateMo 的 **New Event (TM-486)** 為基礎進行修改與擴充；第三方模板、套件、字型及資源仍依各自原始授權條款使用。本專案自行撰寫與修改的程式碼採 MIT License，詳見 `LICENSE`。

---

## 功能

- 響應式單頁活動網站
- 活動介紹、比賽組別、活動流程與報名須知
- Desktop / Mobile 響應式版面
- Owl Carousel 比賽組別輪播
- 表單前端驗證
- PHP 後端報名處理
- MySQL / MariaDB 報名資料儲存
- TCPDF 自動產生報名 PDF
- QR Code 直接連結個人報名 PDF
- PHPMailer 寄送報名確認信
- Session 保護報名成功頁
- 隨機 PDF 檔名，降低可猜測性
- PWA（Web App Manifest + Service Worker）
- Discord Bot 查詢報名資料
- 自訂 403 Forbidden 頁面
- 自訂 404 Not Found 頁面
- `.env` 管理資料庫、SMTP 與 Discord Bot 機密設定
- Cloudflare Tunnel 自訂網域
- Apache IP / UserDir 路徑與自訂網域皆可正常使用

---

## 線上網址

### 自訂網域

```text
https://hw2.chuen666666.com/
```

### 學校 Apache 網址

```text
http://140.138.155.243/~s1151538/HW2/
```

兩種網址皆可存取專案。

---

## 部署架構

目前正式部署架構如下：

```text
Browser
   │
   ├─ https://hw2.chuen666666.com/
   │
   ▼
Cloudflare
   │
   ├─ DNS / HTTPS
   ├─ Cloudflare Tunnel
   └─ URL Rewrite
   │
   ▼
http://127.0.0.1:80
   │
   ▼
Apache
   │
   ▼
/~s1151538/HW2/
   │
   ▼
/home/s1151538/public_html/HW2/
```

Cloudflare Tunnel 對外提供：

```text
hw2.chuen666666.com
```

Tunnel origin：

```text
http://127.0.0.1:80
```

由於 Apache 實際使用 UserDir 路徑：

```text
/~s1151538/HW2/
```

因此 Cloudflare 端另使用 URL Rewrite，將自訂網域的根路徑對應到 Apache 的實際專案路徑。

概念上：

```text
https://hw2.chuen666666.com/
```

對應：

```text
http://127.0.0.1/~s1151538/HW2/
```

而：

```text
https://hw2.chuen666666.com/css/style.css
```

則對應：

```text
http://127.0.0.1/~s1151538/HW2/css/style.css
```

目前 Rewrite Rule 的設計會避免對已經以：

```text
/~s1151538/HW2/
```

開頭的路徑再次加上相同 prefix，以避免重複 rewrite。

因此以下兩種方式都可正常使用：

```text
https://hw2.chuen666666.com/
```

```text
http://140.138.155.243/~s1151538/HW2/
```

---

## 專案結構

```text
HW2/
├── index.html
├── submit.php
├── success.php
├── 403.html
├── 404.html
├── .htaccess
├── .env                  # 不應提交至 Git
├── example.env
├── .gitignore
├── composer.json
├── composer.lock
├── deploy.sh
├── manifest.json
├── service-worker.js
│
├── css/
│   ├── bootstrap.min.css
│   ├── animate.css
│   ├── font-awesome.min.css
│   ├── owl.carousel.css
│   ├── owl.theme.css
│   └── style.css
│
├── js/
│   ├── jquery.js
│   ├── bootstrap.min.js
│   ├── jquery.parallax.js
│   ├── owl.carousel.min.js
│   ├── smoothscroll.js
│   ├── wow.min.js
│   ├── custom.js
│   ├── register.js
│   └── pwa.js
│
├── images/
├── pdf/
│
├── bot/
│   ├── bot.py
│   ├── requirements.txt
│   └── .venv/            # 不應提交至 Git
│
└── vendor/               # Composer 產生，不應提交至 Git
```

另外，`submit.php` 目前使用位於網站上一層的 TCPDF：

```text
public_html/
├── TCPDF/
└── HW2/
```

實際路徑概念為：

```text
/home/s1151538/public_html/TCPDF/
/home/s1151538/public_html/HW2/
```

因此 `submit.php` 目前使用：

```php
require_once __DIR__ . '/../TCPDF/tcpdf_import.php';
require_once __DIR__ . '/../TCPDF/tcpdf_barcodes_2d.php';
```

如果部署到不同目錄結構，需要同步修改 TCPDF 的載入路徑。

---

## 使用的模板與前端套件

| 元件 | 專案中的檔案 | 用途 |
|---|---|---|
| TemplateMo New Event (TM-486) | `index.html`, `css/style.css` 及原始前端結構 | 網頁版型基礎，已大量修改為奶龍選美大賽 |
| Bootstrap | `css/bootstrap.min.css`, `js/bootstrap.min.js` | Grid、Navbar、按鈕、Collapse 等 UI 元件 |
| jQuery | `js/jquery.js` | Bootstrap 與前端互動腳本依賴 |
| Owl Carousel | `css/owl.carousel.css`, `css/owl.theme.css`, `js/owl.carousel.min.js` | 比賽組別輪播 |
| WOW.js | `js/wow.min.js` | 捲動觸發動畫 |
| Animate.css | `css/animate.css` | 動畫樣式 |
| Font Awesome | `css/font-awesome.min.css` | 圖示 |
| jQuery Parallax | `js/jquery.parallax.js` | 視差相關效果 |
| Smooth Scroll | `js/smoothscroll.js` | 頁面內平滑捲動 |
| Google Fonts - Poppins | HTML `<link>` 載入 | 網站主要英文字型 |
| 自訂 JavaScript | `js/custom.js` | 客製化互動與 Owl Carousel 設定 |
| 表單驗證 | `js/register.js` | 報名欄位驗證 |
| PWA 註冊 | `js/pwa.js` | 註冊 Service Worker |

### TemplateMo

本專案以前端模板：

```text
TemplateMo New Event
TM-486 New Event
```

為基礎，再自行修改：

- 導覽列
- Hero / Intro 區塊
- 活動介紹
- 比賽組別
- 活動流程
- 報名須知
- 報名表單
- Responsive layout
- 顏色與圖片
- JavaScript 行為
- PWA
- PHP / DB / PDF / Email / Discord Bot 等後端功能

網站 Footer 亦保留 TemplateMo 設計來源說明。

---

## Owl Carousel 響應式設定

比賽組別使用 Owl Carousel。

目前主要設定位於：

```text
js/custom.js
```

概念如下：

```javascript
$('#owl-speakers').owlCarousel({
    autoPlay: 6000,
    items: 4,
    itemsDesktop: [1199, 2],
    itemsDesktopSmall: [979, 2],
    itemsTablet: [768, 1],
    itemsMobile: [479, 1]
})
```

目的是避免較窄桌面視窗只顯示單一超大項目而導致圖片過度放大。

---

## PWA

PWA 主要由瀏覽器原生 API 實作，沒有依賴額外 PWA Framework。

主要檔案：

```text
manifest.json
service-worker.js
js/pwa.js
images/icon-192.png
images/icon-512.png
```

Service Worker 僅攔截本站同源 GET request，避免外部分析或第三方 request 在離線狀態下造成不必要的 fetch error。

---

## PHP / 後端套件

PHP 套件由 Composer 管理。

### PHPMailer

由 `submit.php` 使用，用於：

- SMTP 寄送報名確認信
- HTML Email
- QR Code PNG 附件
- UTF-8 郵件內容

### vlucas/phpdotenv

用於讀取專案根目錄：

```text
.env
```

主要由：

```text
submit.php
success.php
```

使用。

`.env` 內容格式：

```env
DB_HOST=
DB_NAME=
DB_USER=
DB_PASSWORD=
DB_REGISTRATION_TABLE=

SMTP_USERNAME=
SMTP_PASSWORD=

DISCORD_TOKEN=
```

正式 `.env` 不應提交到 Git。

### TCPDF

TCPDF 未放在本專案的 `vendor/` 目錄，而是使用外部共用目錄：

```text
../TCPDF/
```

用途包括：

- 建立報名 PDF
- 建立 QR Code
- 將 QR Code 插入 PDF
- 產生可由 QR Code 直接開啟的報名 PDF URL

---

## 資料庫

本專案使用：

```text
MySQL / MariaDB
```

PHP 端透過：

```text
PDO
PDO MySQL
```

連線。

Discord Bot 則透過：

```text
mysql-connector-python
```

查詢相同資料庫。

### 正式環境設定

目前部署使用：

```env
DB_HOST=140.138.155.243
DB_NAME=CS380
DB_REGISTRATION_TABLE=s1151538_registrations
```

以下資訊屬機密，不應寫入 README：

```text
DB_USER
DB_PASSWORD
```

應只放在 `.env` 中。

### 資料表欄位

程式使用的主要欄位：

```text
student_id
name
email
phone
grade
category
pdf_filename
```

其中 `student_id` 應為 `PRIMARY KEY` 或 `UNIQUE`，因為 `submit.php` 使用：

```sql
ON DUPLICATE KEY UPDATE
```

讓相同學號再次送出時更新原資料。

範例 schema：

```sql
CREATE TABLE registrations (
    student_id   VARCHAR(32)  NOT NULL,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(255) NOT NULL,
    phone        VARCHAR(32)  NOT NULL,
    grade        VARCHAR(16)  NOT NULL,
    category     VARCHAR(255) NOT NULL,
    pdf_filename VARCHAR(255) NOT NULL,
    PRIMARY KEY (student_id)
) CHARACTER SET utf8mb4;
```

實際正式環境使用的 table name：

```text
s1151538_registrations
```

若部署到其他環境，可在 `.env` 修改：

```env
DB_REGISTRATION_TABLE=registrations
```

---

## 報名流程

```text
index.html
   │
   │ POST
   ▼
submit.php
   │
   ├─ 接收報名資料
   ├─ 寫入 MySQL
   ├─ 產生隨機 PDF filename
   ├─ 建立 QR Code
   ├─ 建立 TCPDF 報名表
   ├─ 儲存 PDF
   ├─ 使用 PHPMailer 寄送確認信
   ├─ 將 QR Code 作為 Email 附件
   └─ 建立 Session
   │
   ▼
success.php
   │
   ├─ 由 Session 判斷本次報名者
   ├─ 查詢資料庫
   └─ 提供該次報名 PDF 連結
```

---

## PDF 與 QR Code

報名完成後，系統會：

1. 產生隨機 PDF 檔名
2. 將 PDF 儲存在 `pdf/`
3. QR Code 內容指向該 PDF URL
4. 將 QR Code 放入 PDF
5. 將 QR Code 作為 Email 附件寄給報名者

PDF 不再使用：

```text
registration_<student_id>.pdf
```

這類可直接猜測的檔名，而改用隨機值降低 enumeration 風險。

---

## Session 與成功頁

`success.php` 不再單純接受：

```text
?student_id=...
```

來決定要顯示哪一筆報名資料。

報名完成後，由 `submit.php` 建立 Session，再導向成功頁，以避免只修改 URL 中的 student ID 就查看其他報名資料。

---

## SMTP

Email 使用 PHPMailer SMTP。

目前 Microsoft 365 / Office 365 設定：

```text
Host: smtp.office365.com
Port: 587
Encryption: STARTTLS
```

帳號與密碼由：

```env
SMTP_USERNAME=
SMTP_PASSWORD=
```

提供。

---

## Discord Bot

Discord Bot 位於：

```text
bot/bot.py
```

Python dependencies：

```text
bot/requirements.txt
```

目前包含：

```text
discord.py
python-dotenv
mysql-connector-python
```

| 套件 | 用途 |
|---|---|
| `discord.py` | Discord Bot API |
| `python-dotenv` | 讀取根目錄 `.env` |
| `mysql-connector-python` | 查詢報名 MySQL 資料 |

Bot 可在：

- Discord Server
- Discord DM

使用，並可依學號或姓名查詢報名資料。

Discord Token：

```env
DISCORD_TOKEN=
```

不得提交至 Git。

---

## 環境需求

建議環境：

- Linux / Unix-like server
- Apache HTTP Server
- Apache UserDir
- `.htaccess`
- `ErrorDocument`
- PHP **8.1+**
- PHP extensions：
  - PDO
  - PDO MySQL
  - OpenSSL
  - mbstring（建議）
- Composer 2
- MySQL 或 MariaDB
- Python 3
- `python3-venv`
- pip
- TCPDF
- 可使用的 SMTP 帳號
- Discord Bot Token（若啟用 Bot）
- HTTPS（PWA 正式環境）
- Cloudflare Tunnel（若使用自訂網域）

目前正式伺服器使用 PHP 8.1 系列。

---

## 安裝

### 1. Clone repository

```bash
git clone https://github.com/Chuen666666/hw2.git
cd hw2
```

### 2. 建立 `.env`

```bash
cp example.env .env
```

編輯：

```bash
nano .env
```

範例：

```env
DB_HOST=127.0.0.1
DB_NAME=your_database
DB_USER=your_username
DB_PASSWORD=your_password
DB_REGISTRATION_TABLE=registrations

SMTP_USERNAME=your_account@example.com
SMTP_PASSWORD=your_password

DISCORD_TOKEN=your_discord_bot_token
```

不要將 Database password、SMTP password 或 Discord Token 寫入 README、`example.env` 或 Git commit。

### 3. 安裝 PHP dependencies

正式環境建議：

```bash
composer install --no-dev --no-interaction
```

開發環境若 PHP 版本符合所有 dev dependency：

```bash
composer install
```

目前正式伺服器為 PHP 8.1；若開發用 Composer 套件要求較新的 PHP，production deployment 建議使用 `--no-dev`。

### 4. 安裝 TCPDF

TCPDF 預期放在：

```text
public_html/TCPDF/
```

專案則在：

```text
public_html/HW2/
```

確認：

```text
TCPDF/tcpdf_import.php
TCPDF/tcpdf_barcodes_2d.php
```

存在。

### 5. 建立 PDF 目錄

```bash
mkdir -p pdf
```

Web Server 需要寫入權限。

課程 / 開發環境可使用：

```bash
chmod 777 pdf
```

正式 Production 環境建議改用較精確的 User / Group 權限。

### 6. 建立資料表

例如：

```sql
CREATE TABLE registrations (
    student_id   VARCHAR(32)  NOT NULL,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(255) NOT NULL,
    phone        VARCHAR(32)  NOT NULL,
    grade        VARCHAR(16)  NOT NULL,
    category     VARCHAR(255) NOT NULL,
    pdf_filename VARCHAR(255) NOT NULL,
    PRIMARY KEY (student_id)
) CHARACTER SET utf8mb4;
```

再設定：

```env
DB_REGISTRATION_TABLE=registrations
```

### 7. 安裝 Discord Bot

```bash
python3 -m venv bot/.venv
bot/.venv/bin/pip install -r bot/requirements.txt
bot/.venv/bin/python bot/bot.py
```

需要背景執行時，可使用 `nohup`、systemd 或 Supervisor。

---

## deploy.sh

專案提供：

```text
deploy.sh
```

用於簡化：

- 建立 `.env`
- Composer install
- 建立 Python virtual environment
- 安裝 `bot/requirements.txt`
- 設定 `pdf/` 寫入權限

執行：

```bash
chmod +x deploy.sh
./deploy.sh
```

PHP 8.1 Production 環境的 Composer 建議使用：

```bash
composer install --no-dev --no-interaction
```

---

## Apache / `.htaccess`

目前 `.htaccess` 用於：

1. 禁止直接讀取 `.env`
2. 自訂 403 Forbidden 頁面
3. 自訂 404 Not Found 頁面

```apache
<Files ".env">
    Require all denied
</Files>

ErrorDocument 403 /~s1151538/HW2/403.html
ErrorDocument 404 /~s1151538/HW2/404.html
```

因此：

```text
https://hw2.chuen666666.com/.env
```

會回傳：

```text
403 Forbidden
```

不存在的頁面則回傳：

```text
404 Not Found
```

自訂頁面只改變顯示內容，不會把 HTTP status code 變成 200。

---

## 403 / 404 雙網址支援

專案同時支援：

```text
https://hw2.chuen666666.com/
```

與：

```text
http://140.138.155.243/~s1151538/HW2/
```

403 / 404 頁面的 CSS、icon 與背景圖片使用：

```text
/~s1151538/HW2/css/...
/~s1151538/HW2/images/...
```

確保直接使用 IP / UserDir 時資源仍能正常載入。

「回到活動首頁」按鈕透過 JavaScript 判斷目前 pathname：

- 自訂網域 → `/`
- IP / UserDir → `/~s1151538/HW2/`

因此兩種網址都不會誤導向 Apache2 Default Page。

---

## PWA 使用條件

請確認：

- 網站使用 HTTPS
- `manifest.json` 可正常取得
- `service-worker.js` 路徑正確
- icon 存在
- 瀏覽器允許 Service Worker
- Service Worker scope 符合實際部署路徑

Cloudflare 自訂網域提供 HTTPS，因此適合直接 Demo PWA。

---

## Cloudflare Tunnel

Cloudflare Tunnel 不是本專案執行的必要條件。

若只使用 Apache，可以直接存取：

```text
http://140.138.155.243/~s1151538/HW2/
```

目前自訂網域部署則使用 Cloudflare Tunnel，將：

```text
hw2.chuen666666.com
```

導向：

```text
http://127.0.0.1:80
```

再透過 Cloudflare URL Rewrite 將自訂網域根路徑映射到：

```text
/~s1151538/HW2/
```

主要效果：

- 提供自訂網域
- 提供 HTTPS
- 方便 PWA Demo
- 保留 Apache UserDir 架構

---

## 安全注意事項

- `.env` 必須加入 `.gitignore`
- `.env` 應由 Apache 禁止直接存取
- `vendor/` 不應提交
- `bot/.venv/` 不應提交
- Database password 不可提交
- SMTP password 不可提交
- Discord Token 不可提交
- 使用 PDO prepared statement 傳入使用者資料
- DB table name 僅允許安全字元
- `success.php` 使用 Session，避免 student ID URL enumeration
- PDF filename 使用不可預測的 random value
- 公開 PDF URL 一旦被取得仍可直接開啟，此為 QR Code 直接開啟 PDF 功能的設計需求

---

## HTML / CSS 驗證

專案已進行：

- W3C HTML Validator
- W3C CSS Validator

自行維護的 HTML / CSS 已處理 validator 問題。

部分第三方舊版 CSS，例如：

```text
bootstrap.min.css
```

可能包含舊瀏覽器相容性語法、vendor-specific property 或 legacy CSS；不建議為了 validator 結果直接修改第三方套件本體。

`style.css` 中不再需要的舊式 `-webkit-` / `-ms-` browser prefix 已進行清理。

---

## 測試項目

目前已測試：

- Desktop layout
- Mobile layout
- Navbar
- Hero layout
- Owl Carousel
- Form validation
- Database write
- Duplicate student ID update
- PDF generation
- Random PDF filename
- QR Code
- PDF direct link
- Confirmation Email
- QR Code Email attachment
- Session-based success page
- PWA installation
- Service Worker
- Offline behavior
- Discord Bot
- Discord DM / Server usage
- 自訂 403
- 自訂 404
- 403 / 404 HTTP status
- 自訂網域
- Apache IP / UserDir 網址
- W3C HTML Validator
- W3C CSS Validator

---

## Git / 不應提交的內容

建議 `.gitignore` 至少包含：

```gitignore
.env
vendor/
bot/.venv/
```

如果 `pdf/` 內是執行期間產生的個人報名 PDF，也建議依需求決定是否排除產出檔。

---

## 第三方授權

本專案包含或引用多個第三方元件，包括：

- TemplateMo New Event
- Bootstrap
- jQuery
- Owl Carousel
- WOW.js
- Animate.css
- Font Awesome
- jQuery Parallax
- Smooth Scroll
- TCPDF
- PHPMailer
- vlucas/phpdotenv
- discord.py
- mysql-connector-python
- python-dotenv
- Google Fonts

上述元件依各自原作者與專案的授權條款使用。

本 repository 的 MIT License 僅涵蓋本專案自行撰寫、修改且具有再授權權利的程式碼，不會取代第三方元件原有授權。

---

## License

本專案自行撰寫與修改的程式碼採：

```text
MIT License
```

詳見：

```text
LICENSE
```

Copyright (c) 2026 Chuen666666

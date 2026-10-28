# 奶龍選美大賽

「奶龍選美大賽」活動報名網站，包含響應式活動頁面、線上報名、MySQL 資料庫、PDF 報名表、QR Code、Email 確認信、PWA、Discord Bot，以及自訂 403 / 404 頁面。

專案前端以 TemplateMo 的 **New Event (TM-486)** 為基礎修改與擴充。第三方模板、套件、字型及其他外部元件仍依各自原始授權條款使用；本專案自行撰寫與修改且具有再授權權利的程式碼採 MIT License，詳見 `LICENSE`。

## 線上網址

自訂網域：

```text
https://hw2.chuen666666.com/
```

學校 Apache 網址：

```text
http://140.138.155.243/~s1151538/HW2/
```

## 功能

- Responsive 單頁活動網站
- 活動介紹、比賽組別、活動流程與報名須知
- Owl Carousel 比賽組別輪播
- 前端報名表單驗證
- PHP 後端報名處理
- MySQL 報名資料儲存
- TCPDF 產生報名 PDF
- QR Code 直接連結報名 PDF
- PHPMailer 寄送確認信與 QR Code 附件
- Session 驗證報名成功頁
- 隨機 PDF 檔名
- PWA（Web App Manifest + Service Worker）
- Discord Bot 報名查詢
- 自訂 403 Forbidden 頁面
- 自訂 404 Not Found 頁面
- `.env` 管理資料庫、SMTP 與 Discord Bot 設定
- Cloudflare Tunnel + HTTPS 自訂網域
- 同時支援自訂網域與 Apache UserDir 網址

## 部署架構

```text
Browser
   │
   ▼
https://hw2.chuen666666.com/
   │
   ▼
Cloudflare
   ├── HTTPS
   ├── Cloudflare Tunnel
   └── URL Rewrite
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

Cloudflare Tunnel 的 origin 為：

```text
http://127.0.0.1:80
```

Cloudflare URL Rewrite 將自訂網域根路徑映射到 Apache 實際使用的：

```text
/~s1151538/HW2/
```

例如：

```text
https://hw2.chuen666666.com/css/style.css
```

對應 Apache 端：

```text
/~s1151538/HW2/css/style.css
```

Rewrite 會排除已經以 `/~s1151538/HW2/` 開頭的路徑，避免重複加入專案 prefix。

Cloudflare Tunnel 並非本專案執行的必要條件；若不使用自訂網域，可直接透過學校 Apache 網址存取。

## 專案結構

```text
HW2/
├── index.html
├── submit.php
├── success.php
├── 403.html
├── 404.html
├── .htaccess
├── .env
├── example.env
├── .gitignore
├── composer.json
├── composer.lock
├── deploy.sh
├── manifest.json
├── service-worker.js
├── css/
│   ├── bootstrap.min.css
│   ├── animate.css
│   ├── font-awesome.min.css
│   ├── owl.carousel.css
│   ├── owl.theme.css
│   └── style.css
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
├── images/
├── pdf/
├── bot/
│   ├── bot.py
│   ├── requirements.txt
│   └── .venv/
└── vendor/
```

TCPDF 位於專案上一層：

```text
public_html/
├── TCPDF/
└── HW2/
```

對應伺服器路徑：

```text
/home/s1151538/public_html/TCPDF/
/home/s1151538/public_html/HW2/
```

## 前端模板與套件

| 元件 | 對應檔案 | 用途 |
| --- | --- | --- |
| TemplateMo New Event (TM-486) | `index.html`, `css/style.css` 及相關前端結構 | 網站版型基礎 |
| Bootstrap | `css/bootstrap.min.css`, `js/bootstrap.min.js` | Grid、Navbar、Button、Collapse 等 UI |
| jQuery | `js/jquery.js` | 前端互動及 Bootstrap 依賴 |
| Owl Carousel | `css/owl.carousel.css`, `css/owl.theme.css`, `js/owl.carousel.min.js` | 比賽組別輪播 |
| WOW.js | `js/wow.min.js` | 捲動觸發動畫 |
| Animate.css | `css/animate.css` | 動畫樣式 |
| Font Awesome | `css/font-awesome.min.css` | Icon |
| jQuery Parallax | `js/jquery.parallax.js` | Parallax 效果 |
| Smooth Scroll | `js/smoothscroll.js` | 頁面內平滑捲動 |
| Google Fonts - Poppins | HTML `<link>` | 字型 |
| 自訂前端程式 | `js/custom.js` | 網站互動及 Carousel 設定 |
| 報名驗證 | `js/register.js` | 表單驗證 |
| PWA 註冊 | `js/pwa.js` | Service Worker 註冊 |

目前 Owl Carousel 的 responsive 設定：

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

## PHP / Composer 套件

PHP 套件由 Composer 管理。

### PHPMailer

用於 `submit.php`：

- SMTP 寄送確認信
- HTML Email
- QR Code PNG 附件

### vlucas/phpdotenv

用於從專案根目錄的 `.env` 載入：

- Database 設定
- SMTP 設定
- Discord Bot Token

### TCPDF

TCPDF 由外部目錄載入：

```php
require_once __DIR__ . '/../TCPDF/tcpdf_import.php';
require_once __DIR__ . '/../TCPDF/tcpdf_barcodes_2d.php';
```

用途：

- 產生報名 PDF
- 產生 QR Code
- 將 QR Code 放入 PDF

## Discord Bot 套件

`bot/requirements.txt`：

```text
discord.py
python-dotenv
mysql-connector-python
```

用途：

| 套件 | 用途 |
| --- | --- |
| `discord.py` | Discord Bot |
| `python-dotenv` | 讀取 `.env` |
| `mysql-connector-python` | 查詢 MySQL |

Bot 可在 Discord Server 與 DM 中使用，並可依學號或姓名查詢報名資料。

## 資料庫

本專案使用 MySQL。

PHP 端使用：

```text
PDO
PDO MySQL
```

Discord Bot 使用：

```text
mysql-connector-python
```

正式部署的非機密設定：

```env
DB_HOST=140.138.155.243
DB_NAME=CS380
DB_REGISTRATION_TABLE=s1151538_registrations
```

`DB_USER` 與 `DB_PASSWORD` 僅存放於 `.env`。

主要欄位：

```text
student_id
name
email
phone
grade
category
pdf_filename
```

`student_id` 需具備 `PRIMARY KEY` 或 `UNIQUE` constraint，以支援 `ON DUPLICATE KEY UPDATE`。

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

部署時可透過：

```env
DB_REGISTRATION_TABLE=registrations
```

指定實際資料表名稱。

## 報名流程

```text
index.html
   │
   │ POST
   ▼
submit.php
   ├── 接收報名資料
   ├── 寫入 MySQL
   ├── 產生隨機 PDF 檔名
   ├── 建立 QR Code
   ├── 建立 TCPDF 報名表
   ├── 儲存 PDF
   ├── 使用 PHPMailer 寄送確認信
   ├── 附加 QR Code
   └── 建立 Session
   │
   ▼
success.php
   ├── 驗證 Session
   ├── 查詢報名資料
   └── 顯示報名結果及 PDF 連結
```

PDF 儲存在：

```text
pdf/
```

PDF 使用隨機檔名，QR Code 內容指向對應的 PDF URL。

## PWA

PWA 使用瀏覽器原生 Web App API。

主要檔案：

```text
manifest.json
service-worker.js
js/pwa.js
images/icon-192.png
images/icon-512.png
```

Service Worker 處理本站同源 GET request。

PWA 正式使用時需 HTTPS；目前自訂網域由 Cloudflare 提供 HTTPS。

## SMTP

Email 使用 PHPMailer SMTP。

目前使用 Microsoft 365 / Office 365：

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

## `.env`

`example.env` 格式：

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

正式 `.env` 不可提交至 Git。

## HTML 輸出安全

報名資料會輸出至成功頁、TCPDF 報名表及 HTML 確認信。這些 HTML 輸出點使用：

```php
htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
```

處理動態值，以避免使用者輸入被瀏覽器、TCPDF 或郵件用戶端解析為 HTML。資料庫與 SMTP 收件地址仍使用未經 HTML 編碼的原始值，僅在產生 HTML 時依輸出情境進行編碼。

可使用 Composer 檢查鎖定套件的已知安全公告：

```bash
composer audit --locked --no-interaction
```

## Apache / `.htaccess`

`.htaccess` 用於：

- 阻止直接存取 `.env`
- 自訂 403 Forbidden 頁面
- 自訂 404 Not Found 頁面

```apache
<Files ".env">
    Require all denied
</Files>

ErrorDocument 403 /~s1151538/HW2/403.html
ErrorDocument 404 /~s1151538/HW2/404.html
```

403 / 404 頁面保留正確 HTTP status code。

### 403 / 404 雙網址支援

錯誤頁面的 CSS、icon 與背景資源使用 Apache UserDir 路徑：

```text
/~s1151538/HW2/css/...
/~s1151538/HW2/images/...
```

首頁按鈕依目前網址選擇正確首頁：

```text
https://hw2.chuen666666.com/
```

或：

```text
http://140.138.155.243/~s1151538/HW2/
```

## 環境需求

- Linux / Unix-like Server
- Apache HTTP Server
- Apache UserDir
- `.htaccess` / `ErrorDocument`
- PHP 8.1+
- PHP PDO
- PHP PDO MySQL
- PHP OpenSSL
- Composer 2
- MySQL
- Python 3
- `python3-venv`
- pip
- TCPDF
- SMTP 帳號
- Discord Bot Token（使用 Bot 時）
- HTTPS（使用 PWA 時）
- Cloudflare Tunnel（僅自訂網域部署需要）

目前正式伺服器使用 PHP 8.1 系列。

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

設定：

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

### 3. 安裝 PHP dependencies

Production：

```bash
composer install --no-dev --no-interaction
```

### 4. 安裝 TCPDF

目錄需符合：

```text
public_html/TCPDF/
public_html/HW2/
```

並確認：

```text
TCPDF/tcpdf_import.php
TCPDF/tcpdf_barcodes_2d.php
```

存在。

### 5. 建立 PDF 輸出目錄

```bash
mkdir -p pdf
```

Web Server 必須具有該目錄的寫入權限。

### 6. 建立資料庫

建立符合前述 schema 的資料表，再於 `.env` 設定資料表名稱。

### 7. 安裝 Discord Bot

```bash
python3 -m venv bot/.venv
bot/.venv/bin/pip install -r bot/requirements.txt
bot/.venv/bin/python bot/bot.py
```

伺服器背景執行可使用 `nohup`、systemd 或 Supervisor。

## deploy.sh

專案包含 `deploy.sh`，用於協助：

- 建立環境設定檔
- 安裝 PHP dependencies
- 建立 Python virtual environment
- 安裝 Discord Bot dependencies
- 設定執行所需目錄權限

使用：

```bash
chmod +x deploy.sh
./deploy.sh
```

## Git

以下內容不應提交：

```gitignore
.env
vendor/
bot/.venv/
```

若 `pdf/` 中存放實際報名者產生的 PDF，亦不應將這些執行期間資料提交至 repository。

## 驗證與測試

目前專案已測試：

- Desktop / Mobile Responsive Layout
- Navbar / Hero
- Owl Carousel
- 報名表單
- Database Write / Update
- PDF
- QR Code
- Email
- Session Success Page
- PWA / Service Worker
- Discord Bot
- 403 / 404
- 自訂網域
- Apache IP / UserDir 網址
- W3C HTML Validator
- W3C CSS Validator

自行維護的 HTML / CSS 已完成檢查。第三方套件可能保留 legacy browser compatibility syntax，不直接修改第三方 minified library。

## 第三方授權

本專案包含或引用：

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
- python-dotenv
- mysql-connector-python
- Google Fonts

上述元件依各自原始授權條款使用。

## License

本專案自行撰寫與修改且具有再授權權利的程式碼採 MIT License。

詳見：

```text
LICENSE
```

Copyright (c) 2026 Chuen666666

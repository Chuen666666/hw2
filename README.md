# 奶龍選美大賽

一個以「奶龍選美大賽」為主題的活動報名網站。專案包含響應式活動頁面、線上報名、MySQL 資料庫儲存、PDF 報名表產生、QR Code、Email 確認信、PWA、Discord Bot 查詢，以及自訂 404 頁面。

> 專案版面以 TemplateMo 的 **New Event (TM-486)** 為基礎進行修改與擴充；第三方模板、套件、字型及資源仍依各自原始授權條款使用。本專案自行撰寫與修改的程式碼採 MIT License，詳見 `LICENSE`。

## 功能

- 響應式單頁活動網站
- 活動介紹、比賽組別、流程與報名須知
- 表單前端驗證
- PHP 後端報名處理
- MySQL / MariaDB 報名資料儲存
- TCPDF 自動產生報名 PDF
- QR Code 直接連結個人報名 PDF
- PHPMailer 寄送報名確認信
- Session 保護報名成功頁
- 隨機 PDF 檔名
- PWA（Web App Manifest + Service Worker）
- Discord Bot 查詢報名資料
- 自訂 404 頁面
- `.env` 管理資料庫、SMTP 與 Discord Bot 機密設定

## 專案結構

```text
HW2/
├── index.html
├── submit.php
├── success.php
├── 404.html
├── .htaccess
├── .env                  # 不應提交至 Git
├── example.env
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
│   └── .venv/            # 不應提交至 Git
└── vendor/               # Composer 產生，不應提交至 Git
```

另外，`submit.php` 目前使用位於網站上一層的 TCPDF：

```text
public_html/
├── TCPDF/
└── HW2/
```

因此預設路徑為：

```php
../TCPDF/tcpdf_import.php
../TCPDF/tcpdf_barcodes_2d.php
```

若部署到不同目錄結構，需同步修改 `submit.php` 中的 TCPDF 路徑。

## 使用的模板與前端套件

| 元件 | 專案中的檔案 | 用途 |
| --- | --- | --- |
| TemplateMo New Event (TM-486) | `index.html`, `css/style.css` 及原始前端結構 | 網頁版型基礎，已修改為奶龍選美大賽 |
| Bootstrap | `css/bootstrap.min.css`, `js/bootstrap.min.js` | Grid、Navbar、按鈕、Collapse 等 UI 元件 |
| jQuery | `js/jquery.js` | Bootstrap 與前端互動腳本依賴 |
| Owl Carousel | `css/owl.carousel.css`, `css/owl.theme.css`, `js/owl.carousel.min.js` | 比賽組別輪播 |
| WOW.js | `js/wow.min.js` | 捲動觸發動畫 |
| Animate.css | `css/animate.css` | 動畫樣式 |
| Font Awesome | `css/font-awesome.min.css` | 圖示 |
| jQuery Parallax | `js/jquery.parallax.js` | 視差效果 |
| Smooth Scroll | `js/smoothscroll.js` | 頁面內平滑捲動 |
| Google Fonts - Poppins | HTML `<link>` 載入 | 主要英文字型 |
| 自訂 JavaScript | `js/custom.js` | 客製化前端行為 |
| 表單驗證 | `js/register.js` | 報名欄位驗證 |
| PWA 註冊 | `js/pwa.js` | 註冊 Service Worker |

### PWA

PWA 未使用額外 JavaScript 套件，主要由瀏覽器原生 API 實作：

- `manifest.json`
- `service-worker.js`
- `js/pwa.js`
- `images/icon-192.png`
- `images/icon-512.png`

## PHP / 後端套件

PHP 套件由 Composer 管理。

### PHPMailer

由 `submit.php` 使用，用於：

- SMTP 寄信
- HTML Email
- QR Code PNG 附件

### vlucas/phpdotenv

由 `submit.php`、`success.php` 使用，用於讀取 `.env`：

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

請勿將正式 `.env` 提交至公開 Git repository。

### TCPDF

TCPDF 未放在本專案 `vendor/` 中，而是以外部目錄方式載入：

```php
require_once __DIR__ . '/../TCPDF/tcpdf_import.php';
require_once __DIR__ . '/../TCPDF/tcpdf_barcodes_2d.php';
```

用途：

- 產生報名 PDF
- 產生 QR Code (`TCPDF2DBarcode`)
- QR Code 直接連結個人報名 PDF

## Discord Bot 套件

`bot/requirements.txt`：

```text
discord.py
python-dotenv
mysql-connector-python
```

| 套件 | 用途 |
| --- | --- |
| `discord.py` | Discord Bot |
| `python-dotenv` | 讀取專案根目錄 `.env` |
| `mysql-connector-python` | 查詢報名資料庫 |

Bot 支援以學號或姓名查詢報名資料。

## 環境需求

建議環境：

- Linux / Unix-like server
- Apache HTTP Server
  - 需允許 `.htaccess`
  - 需支援 `ErrorDocument`
- PHP **8.1+**
- PHP extensions：
  - PDO
  - PDO MySQL
  - mbstring（建議）
  - OpenSSL
- Composer 2
- MySQL 或 MariaDB
- Python 3
- `python3-venv`
- pip
- TCPDF
- 可用的 SMTP 帳號
- Discord Bot Token（若啟用 Discord Bot）
- HTTPS（PWA / Service Worker 正式環境建議且多數瀏覽器要求）

目前正式環境已在 PHP 8.1 系列運作。

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

編輯 `.env`：

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

不要把真實密碼或 Token 寫進 `README.md`、`example.env` 或 Git commit。

### 3. 安裝 PHP dependencies

正式環境建議：

```bash
composer install --no-dev --no-interaction
```

開發環境若 PHP 版本符合所有 dev dependencies，可使用：

```bash
composer install
```

> 專案曾在 PHP 8.1 的正式伺服器部署；若 `composer install` 因開發用套件要求較新 PHP 而失敗，正式部署應使用 `--no-dev`。

### 4. 安裝 TCPDF

TCPDF 必須位於 `HW2` 上一層：

```text
public_html/TCPDF/
public_html/HW2/
```

並確認存在：

```text
TCPDF/tcpdf_import.php
TCPDF/tcpdf_barcodes_2d.php
```

若放在其他位置，請修改 `submit.php` 的 `require_once` 路徑。

### 5. 建立 PDF 輸出目錄

```bash
mkdir -p pdf
```

讓 Web Server 對 `pdf/` 有寫入權限。

課程 / 開發環境可使用：

```bash
chmod 777 pdf
```

正式站台建議依 Web Server 使用者與群組設定較精確的權限，不建議長期使用 `777`。

### 6. 建立資料庫資料表

程式至少需要以下欄位：

```text
student_id
name
email
phone
grade
category
pdf_filename
```

其中 `student_id` 應為 UNIQUE 或 PRIMARY KEY，因為報名程式使用 `ON DUPLICATE KEY UPDATE`。

範例：

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

若使用不同表名：

```env
DB_REGISTRATION_TABLE=your_table_name
```

### 7. 安裝 Discord Bot

```bash
python3 -m venv bot/.venv
bot/.venv/bin/pip install -r bot/requirements.txt
bot/.venv/bin/python bot/bot.py
```

若需背景執行，可依伺服器環境使用 `nohup`、systemd 或 Supervisor。

## `deploy.sh`

專案包含 `deploy.sh`，用於簡化：

- 建立 `.env`
- 安裝 Composer dependencies
- 建立 Python virtual environment
- 安裝 Bot requirements
- 設定 `pdf/` 可寫入

執行：

```bash
chmod +x deploy.sh
./deploy.sh
```

正式 PHP 8.1 環境建議確認腳本內 Composer 指令為：

```bash
composer install --no-dev --no-interaction
```

## SMTP

目前程式以 PHPMailer 的 SMTP 模式寄送 Email。

若沿用 Microsoft 365 / Office 365：

```text
Host: smtp.office365.com
Port: 587
Encryption: STARTTLS
```

帳號及密碼由 `.env` 提供。

## Apache / `.htaccess`

目前 `.htaccess` 用於：

1. 阻止直接讀取 `.env`
2. 自訂 404 頁面

概念如下：

```apache
<Files ".env">
    Require all denied
</Files>

ErrorDocument 404 /~USER/HW2/404.html
```

部署到不同帳號或網址路徑時，請修改 `ErrorDocument` 路徑。

## PWA

請確認：

- 網站透過 HTTPS 存取
- `manifest.json` 可正常讀取
- `service-worker.js` 可由正確路徑取得
- icon 檔案存在
- 瀏覽器未阻擋 Service Worker
- Service Worker scope 符合實際部署路徑

## 自訂網域

專案可直接使用 Apache 網址，也可透過反向代理 / Cloudflare Tunnel 提供自訂網域。

目前部署案例：

```text
https://hw2.chuen666666.com/
```

若更換網域，請檢查 `submit.php` 中建立 PDF / QR Code URL 的 base URL 是否需同步修改。

## 安全注意事項

- `.env` 必須加入 `.gitignore`
- `vendor/`、`bot/.venv/` 不應提交
- Discord Token、SMTP 密碼、資料庫密碼不可提交
- 報名成功頁應透過 Session 判斷目前報名者
- PDF 檔名應使用不可預測的隨機值，而不是直接使用學號
- SQL table name 僅應允許安全字元
- 使用 prepared statement 傳入使用者資料
- 公開 PDF URL 一旦被知道仍可直接存取；目前設計是為了讓 QR Code 可直接開啟 PDF

## 驗證

專案已針對主要頁面進行：

- W3C HTML Validator
- W3C CSS Validator
- Desktop / Mobile responsive layout
- PWA / Service Worker
- 自訂 404 HTTP status
- PDF / QR Code
- Email
- Database
- Discord Bot

部分第三方舊版 CSS（例如 Bootstrap）可能包含為舊瀏覽器相容性保留的 legacy CSS；不建議為了 validator 結果直接修改第三方套件本體。

## 第三方授權

本專案包含或引用：

- TemplateMo New Event
- Bootstrap
- jQuery
- Owl Carousel
- WOW.js
- Animate.css
- Font Awesome
- TCPDF
- PHPMailer
- vlucas/phpdotenv
- discord.py
- mysql-connector-python
- python-dotenv
- Google Fonts

這些第三方元件各自依原作者的授權條款發布，不因本專案採 MIT License 而改變。

## License

本專案自行撰寫與修改的程式碼採 [MIT License](LICENSE)。

Copyright (c) 2026 Chuen666666

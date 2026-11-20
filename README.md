# Lost and Found System

A simple web-based CRUD application built with **PHP, MySQL, HTML, CSS, and Bootstrap 5**.
Anyone can report and browse lost or found items. An admin logs in to manage the records.

## Pages
| Page | Who can open it | Purpose |
|------|-----------------|---------|
| `index.php` | Everyone | Starting page: welcome, counts, latest reports |
| `items.php` | Everyone | Browse, search, and filter all items (read only) |
| `add.php` | Everyone | Report a lost or found item (with optional photo) |
| `admin/login.php` | Admin | Admin login |
| `admin/dashboard.php` | Admin only | Manage all items: Edit, Claimed, Delete |
| `admin/edit.php` | Admin only | Update a record |
| `admin/claim.php` | Admin only | Mark an item as claimed |
| `admin/delete.php` | Admin only | Delete a record and its photo |
| `admin/change_password.php` | Admin only | Change the admin password |

## Admin account
- URL: `http://localhost/lost_and_found/admin/login.php`
- Username: `admin`
- Password: `admin123` (change it after the first login)

## Database
- `items`: `id` (PK), `item_name`, `category`, `description`, `location`, `date_reported`,
  `status` (Lost / Found / Claimed), `contact_name`, `contact_number`, `found_by`, `photo`, `created_at`
- `admins`: `id` (PK), `username` (unique), `password` (hashed), `full_name`, `created_at`

## Run locally (XAMPP)
1. Start **Apache** and **MySQL** in XAMPP.
2. Copy this folder into `htdocs/lost_and_found`.
3. Open `http://localhost/phpmyadmin` and import `database.sql`.
   (If you already imported the previous version, run `update_existing_db.sql` instead.)
4. Visit `http://localhost/lost_and_found/`.

## Deploy on InfinityFree
1. Create a hosting account, then go to **Control Panel -> MySQL Databases** and create a database.
2. Copy the hostname, username, password, and database name into `db_connect.php`.
3. Upload all files and folders (including `admin/` and `uploads/`) to `htdocs/` using File Manager.
4. Open the InfinityFree **phpMyAdmin** and import `database.sql`
   (delete the `CREATE DATABASE` and `USE` lines first if the import is refused).
5. Test your live link, the admin login, and a photo upload.

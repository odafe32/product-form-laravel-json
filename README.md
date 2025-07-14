# Laravel Skills Test Submission

## 📇 Submitted by:

**Joseph Sule Godfrey**

## 📋 Features

✅ Form with the following fields:

* Product Name
* Quantity in Stock
* Price per Item

✅ Functionality:

* Submitted data is saved to a valid JSON file (`storage/app/data.json`).
* Displayed below the form in a table, ordered by datetime submitted.
* Table includes: Product Name, Quantity, Price, Datetime Submitted, Total Value.
* Total Value is calculated as: `Quantity * Price`.
* Last row shows the sum of all Total Values.

✅ Styling & Interaction:

* Uses Twitter Bootstrap for styling.
* Form submits asynchronously via AJAX — page does not reload.
* (Bonus) Edit functionality is implemented for each row.

✅ Works out-of-the-box — no modification required after extraction.

---

## 🚀 Setup Instructions

### 📄 Requirements

-   PHP >= 8.x
-   Composer
-   Node.js & npm
-   Laravel 10.x or later
-   MySQL or SQLite (optional — not required for this test)

---

### 🖥️ Installation Steps

1.  **Clone the repository**

```bash
git clone https:///github.com/odafe32/product-form-laravel-json.git
cd product-form-laravel-json
```

2.  **Install PHP dependencies**

```bash
composer install
```

3.  **Environment configuration**

```bash
cp .env.example .env
php artisan key:generate
```

4.  **Run the application**

```bash
php artisan serve
```

Then visit:

🔗 [http://localhost:8000](http://localhost:8000/)

---

## 📝 Notes

-   Data is saved in `storage/app/data.json` as a valid JSON file.
-   No database setup is required for this test — it works with file storage.
-   All functionality is handled via Laravel routes, controllers, and AJAX requests.

If you encounter any issues running the project or reviewing the code, feel free to contact me at any time.

---

## 📨 Submission Details

This repository link was submitted as part of the Laravel skills test for Coalition Technologies:

🔗 [https://github.com/odafe32/product-form-laravel-json.git](https://github.com/odafe32/product-form-laravel-json.git)

The repository is **public** and timestamped as per the test instructions.

---

## 💻 Technologies Used

-   Laravel Framework
-   PHP
-   Bootstrap 5
-   jQuery AJAX
-   JSON file storage

---

## 👨‍💻 Contact

**Joseph Sule Godfrey**

📧 Email: _[godfreyj.sule1@gmail.com
](mailto:godfreyj.sule1@gmail.com)_

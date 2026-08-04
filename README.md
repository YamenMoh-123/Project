# TITLE

TITLE is a PHP and MySQL based online book catalogue website.

This website allows users to view books, leave ratings and comments, and favorite books.

Admins can manage reviews, books, and users

---

# Features

## User Features

- Users can create accounts and login
- Anyone can view available books
- Anyone can search books by title and author
- Anyone can view detailed book information
- Users can add books to favourites
- Users can write book reviews
- Users can edit personal reviews

## Administrator Features

- Admin dashboard for managing the website
- Add, Edit, Delete books
- Manage user accounts, disable, give admin privellage, delete
- Manage / delete user reviews
- Change website themes and layout

---

# Technologies Used

- HTML5
- CSS
- JavaScript
- PHP
- MySQL

---

# Project Structure

Project/
├── admin/
| ├── add_book.php
│ ├── dashboard.php
| ├── delete_book.php
| ├── edit_book.php
| ├── manage_books.php
│ ├── manage_reviews.php
│ ├── manage_users.php
│ ├── themes.php
│
├── assets/
│ ├── css/
|   ├── style.css
|   ├── themes.css
│ ├── images/
|   ├── "All book cover images stored here
│ └── js/
|   ├── app.js
│
├── books/
│ ├── book.php
│ ├── books.php
| ├── delete_review.php
| ├── submit_review.php
| ├── toggle_favorite.php
│
├── help/
│ ├── index.html
│ ├── user_basics.html
│ ├── user_advanced.html
│ ├── admin_basics.html
│ ├── admin_advanced.html
│
├── includes/
| ├── image_upload.php
│ ├── header.php
│ ├── footer.php
│ ├── auth.php
│
├── favourites.php
├── index.php
├── login.php
├── logout.php
├── profile.php
├── register.php
│
├── index.php
├── README.md

---

a /config folder exists outside the root directory
a .env file exists outside the root as well

# Database

TITLE uses a MySQL database containing:

- Users
- Books
- Reviews
- Favorites
- Website settings

The database create table commands are located in /database

`schema.sql` creates the required tables.

---

# User Roles

## Regular User

Users can:
- Browse books
- Search the catalogue
- Save favourites
- Write reviews

## Administrator

Administrators can:
- Manage books
- Manage users
- Manage reviews
- Modify website appearance
- Monitor system status

---

# Images
Book covers are stored in:
assets/images/books/

Images are uploaded through the administrator book management interface.

---

# Themes
TITLE supports multiple website themes.

Available themes include:
- Classic
- Dark
- Modern

---

# Documentation

User and admin documentation is available in the wiki link from the nav bar

Documentation includes:
- User features
- Advanced user features
- Admin features
- Advanced admin features

---

---

# TITLE Installation Guide

Installation instructions for TITLE

---

# 1. Download Project Files

Clone the repository:
git clone https://github.com/YamenMoh-123/Project.git


Place the project inside the web server directory.

Example:
directory/Project

---

# 2. Create Database

In MySql create a database:

```sql
CREATE DATABASE TITLE;
```

create the database structure from database/schema.sql by copying the create table commands

# 3. Configure Database Connection

Open config/db.php
Update the database settings:
$host = "localhost";
$dbname = "TITLE";
$username = "your_username";
$password = "your_password";
Save the file.

# 4. Start Server

run from public_html/Project ```php -S localhost:8000```
Open http://localhost:<port server runs on>

# 5. Default Accounts

Through MySql create an admin account by inserting into the users table with isAdmin (true / 1)

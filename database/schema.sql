/* users table uses an int as the primary key
contains name, email, password (that is inserted as a hashed value)
admin and disabled flags and created at timestamp. users are unique on email
 */
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    is_disabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* books contain title, author, page count, description, image, genre, format, language
year published, create at timestamp and is unique on title and author */
CREATE TABLE books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    page_count INT,
    description TEXT,
    image VARCHAR(255),
    genre VARCHAR(100),
    format VARCHAR(50) NOT NULL DEFAULT 'Paperback',
    language VARCHAR(50) NOT NULL DEFAULT 'English',
    published_year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(title, author)
);

/* reviews are unique on book_id and user_id contain rating and review text, created at timestamp
delete an entry if either book id or user id is deleted in their respective tables
(cant have a review on no book, or a review with no creator) */
CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating INT NOT NULL,
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (book_id)
        REFERENCES books(id)
        ON DELETE CASCADE,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE(book_id, user_id)
);

/* favorites are unique on user id and book id this is used as a toggle for users favorites 
rows are deleted if a user is deleted or a book is deleted 
(cant have a favorite on no book, or a favorite for no user */
CREATE TABLE favorites (
    user_id INT UNSIGNED NOT NULL,
    book_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY(user_id, book_id),

    FOREIGN KEY(user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY(book_id)
        REFERENCES books(id)
        ON DELETE CASCADE
);

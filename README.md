# Library-DBMS

Library management system built with PHP, MySQL/MariaDB, and HTML for student and librarian workflows.

## Features

- Role-based login for `student` and `librarian`
- Student dashboard:
  - Search book catalog (title, author, genre, ISBN)
  - View popular books by major
  - View outstanding fines
  - View loan history
  - Create pending loan requests
  - Place holds on books
- Librarian dashboard:
  - View pending loan pickup queue
  - Process pending loans to active
  - View active loans
  - Process returns
  - View unpaid fines
  - Mark fines as paid
  - Manage book inventory/copies
  - Add new catalog titles

## Loan Workflow

1. Student creates a loan request -> loan is created with `pending` status.
2. Librarian processes the request at pickup -> loan status moves to `active`.
3. Librarian processes return -> loan status becomes `returned`, copy status is updated, and fine logic is applied for late returns.

## Project Structure

- `frontend/` - login and dashboard pages
- `backend/` - auth, admin/user actions, helpers, database config
- `database/library.sql` - full schema, sample data, procedures, functions, and triggers

## Setup (XAMPP)

1. Place project in `htdocs`:
   - `/Applications/XAMPP/xamppfiles/htdocs/Library-DBMS`
2. Import `database/library.sql` into MySQL/MariaDB (database name: `library`).
3. Create DB config file:
   - `backend/config/config.php`
4. Start Apache + MySQL in XAMPP.
5. Open:
   - `http://localhost/Library-DBMS/frontend/login.php`

## Notes

- URL path is case-sensitive in this setup. Use `Library-DBMS` exactly.
- Default DB credentials in local XAMPP are often:
  - host: `localhost`
  - user: `root`
  - password: empty

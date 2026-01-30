# GEMINI.md

## Project Overview

This project is a PHP-based web application called "FilmHub". It serves as a personal movie catalog where users can browse movies, view details, add comments, rate them, and manage their own profiles with favorite lists and avatars.

The application follows a classic Model-View-Controller (MVC) architectural pattern:

*   **Model:** Located in the `model/` directory, these files handle all database interactions. There are separate models for movies, users, genres, comments, ratings, and favorites. They connect to a MySQL database (as defined in `filmhub.sql`).
*   **View:** Located in the `view/` directory, these are the PHP template files responsible for rendering the HTML content that the user sees.
*   **Controller:** The main `controller/controller.php` file contains the core application logic. It receives requests, interacts with the models to fetch or update data, and then passes that data to the views for rendering.
*   **Routing:** The `route/routing.php` file acts as a simple front-controller, parsing the URL and dispatching the request to the appropriate method in the main `Controller` class. An `admin/` section with its own MVC structure exists for administrative tasks.

### Main Technologies

*   **Backend:** PHP
*   **Database:** MySQL (MariaDB)
*   **Frontend:** HTML, CSS, and minimal JavaScript.

## Building and Running

This project is a standard PHP application and does not have a formal build process. It is designed to be run on a web server with PHP and MySQL capabilities, such as a local XAMPP or WAMP stack.

### Setup Instructions

1.  **Web Server:**
    *   Place the entire project directory (`filmhub`) into the document root of your web server (e.g., `htdocs` for XAMPP).
    *   Ensure your web server (like Apache) is running.

2.  **Database:**
    *   Create a new database named `filmhub` in your MySQL server (e.g., via phpMyAdmin).
    *   Import the `filmhub.sql` file into the newly created database. This will create all the necessary tables and populate them with initial data.

3.  **Configuration:**
    *   The database connection is configured in `inc/Database.php`. The default credentials are:
        *   **Host:** `127.0.0.1`
        *   **Database:** `filmhub`
        *   **User:** `root`
        *   **Password:** `''` (empty)
    *   If your MySQL setup uses a different username or password, you must update this file accordingly.

4.  **Accessing the Application:**
    *   Open your web browser and navigate to `http://localhost/filmhub/`.

### Test Users

You can log in with the following default credentials found in `filmhub.sql`:

*   **Admin:**
    *   **Login:** `admin`
    *   **Email:** `admin@filmhub.local`
    *   **Password:** `admin123`
*   **User:**
    *   **Login:** `vlad`
    *   **Email:** `vlad@filmhub.local`
    *   **Password:** `vlad123`

*(Note: The passwords in the database are hashed, the plain text versions are assumed from the context of test data.)*

## Development Conventions

*   The codebase is structured in an MVC-like pattern. When adding new features, follow this convention by separating database logic (Model), presentation (View), and application logic (Controller).
*   URL routing is handled manually in `route/routing.php`. New public pages or API endpoints should be added there.
*   The `admin/` directory contains a separate, parallel MVC structure for administrative functions. Changes here should follow the patterns established in `admin/controllerAdmin`, `admin/modelAdmin`, and `admin/viewAdmin`.
*   Helper functions and classes are located in the `inc/` directory.
*   Static assets like CSS, JavaScript, and images are in the `public/` and `img/` directories.

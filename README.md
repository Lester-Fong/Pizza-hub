# Pizza Hub

Welcome to the Pizza Hub Dashboard, a modern web application built to manage and visualize imported pizza sales data. This project combines a Laravel backend with a Vue.js frontend, featuring a responsive dashboard, CSV data import, and Authentication.

## Overview

This application provides a comprehensive dashboard for tracking pizza sales, including total orders, revenue, top-selling pizzas, and daily sales trends. It’s designed for pizza business owners to make data-driven decisions, with a clean UI built using Quasar and Chart.js for visualizations.

## Prerequisites


- Composer
- Node.js and npm
- MySQL
- PHP >= 8.1 (xampp recommended)
- Git

## Setup Instructions

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Lester-Fong/Pizza-hub.git
   cd pizza-hub
   ```

2. **Install Dependencies**:
   Laravel (Backend)
   ```bash
   cd backend
   composer install
   ```
   **Install Dependencies (Frontend)**:
    Vuejs (Frontend)
    ```bash
    cd quasar
    npm install
    ```

3. **Configure Environment**:
   - Copy .env.example to .env:
   ```bash
   cd backend
   cp .env.example .env
   ```
   - Update .env with your MySQL credentials:
   ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=pizza_hub
    DB_USERNAME=root
    DB_PASSWORD=
   ```
   - Generate Application Key
    ```bash
    cd backend
    php artisan key:generate
    ```

4. **Setup the database:**
    - Create the database
    ``` bash
    CREATE DATABASE pizza_hub;
    ```
    **OR**
    - Run migration and it will ask you to create a database. (make sure to setup the .env -DB_DATABASE=pizza_hub)
    ```bash
    cd backend
    php artisan migrate
    ```

5. **Run the Application:**
    - Start the Laravel development server
    ```bash
    cd backend
    php artisan serve
    ```
    - In a separate terminal, start the Vue.js development server:
    ```bash
    cd quasar
    npm run dev
    ```
    **OR**
    ```bash
    cd quasar
    quasar dev
    ```
    - Access the app by clicking to the port shown in the terminal

6. **Import Sample Data (CSV Files)**
    - Create an account and Login
    - Make sure to run the queue for the job to work. 
    ```bash
    cd backend
    php artisan queue:work --queue=orders-import
    ```
    - Use the dashboard’s CSV import feature to upload a sample sales dataset (e.g., orders.csv, pizza_types.csv) to populate the dashboard.

<br/><br/>

## Features
- Dashboard Visualization: Displays total orders, revenue vs. orders (doughnut chart), top 5 pizzas by quantity (bar chart), and daily sales trend (line chart).
- CSV Data Import: Upload sales data via a user-friendly form.
- Authentication: Secure register and login functionality with token-based access.
- Responsive Design: Built with Quasar for a mobile-friendly interface.
- API-Driven: RESTful endpoints for data retrieval and management.

## Usage
1. Register/Login: Access the app and create an account or log in.
2. Import Data: Navigate to the dashboard, upload a CSV file with order details, and wait for processing.
3. View Dashboard: Explore sales metrics and charts to analyze performance.
4. Logout: Securely end your session.

## Testing
- **Unit Test:** The Application run backend test with PHPUnit:
  ```bash
  cd backend
  php artisan test --testsuite=Unit
  ```
  -Tests cover DashboardController (sales summary) and UserController (register, login) using Mockery for isolation.
- **Setup:** Tests use an in-memory SQLite database for speed; configure .env.testing for MySQL if preferred.
<br/><br/>

## Note: If you have any questions regarding setting up or running the project, you can reach out to me and I will be happy to guide you. Thanks!

```bash
   lesternielcfong22@gmail.com
```

<br/><br/>

## Acknowledgments
### This project was made possible with the following tools and technologies:
**Vue3** - Frontend JavaScript framework <br/>
**Quasar2** - The enterprise-ready cross-platform VueJs framework <br/>
**Laravel** - Backend PHP framework <br/>
**Vite** - A blazing fast frontend build tool powering the next generation of web applications. <br/>
**Chart.js & Vue-chartjs** - Simple yet flexible JavaScript charting library for the modern web <br/>
**Axios** - HTTP Client for the browser. <br/>

## License
**This project is open-source under the MIT License.**

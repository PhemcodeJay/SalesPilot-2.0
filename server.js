// Import required modules
const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const dotenv = require('dotenv');
const path = require('path');
const PDFDocument = require('pdfkit');
const moment = require('moment');
const { Configuration, OpenAI } = require("openai");

// Initialize dotenv to load environment variables from .env file
dotenv.config();

// Initialize the Express app
const app = express();

// Set the server port (default to 3000)
const port = process.env.PORT || 3000;

// Use body-parser to parse incoming request bodies
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Serve static files (e.g., HTML, CSS, JS, images)
app.use(express.static(path.join(__dirname, 'public')));

// Create a MySQL connection pool
const db = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    connectionLimit: 10, // limit the number of connections
});

// Test database connection
db.getConnection((err, connection) => {
    if (err) {
        console.error('Database connection failed: ' + err.stack);
        return;
    }
    console.log('Connected to the database as id ' + connection.threadId);
    connection.release(); // release the connection after use
});

// Route to serve the home page
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'home.html'));
});

// Route for login page
app.get('/login', (req, res) => {
    res.sendFile(path.join(__dirname, 'loginpage.php'));
});

// Route for logout
app.get('/logout', (req, res) => {
    // Handle logout logic
    res.redirect('/login');
});

// Route for user profile edit
app.get('/user-profile', (req, res) => {
    res.sendFile(path.join(__dirname, 'user-profile-edit.php'));
});

// Route to display sales metrics (inventory and sales analysis)
app.get('/sales-metrics', (req, res) => {
    res.sendFile(path.join(__dirname, 'sales-metrics.php'));
});

// Route for viewing and editing invoices
app.get('/invoice/:id', (req, res) => {
    const invoiceId = req.params.id;
    db.query('SELECT * FROM invoices WHERE id = ?', [invoiceId], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Database query failed' });
        }
        res.render('pages-invoice.php', { invoice: result[0] });
    });
});

// Route for adding a new customer
app.post('/add-customer', (req, res) => {
    const { name, email, phone } = req.body;
    db.query('INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)', [name, email, phone], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to add customer' });
        }
        res.redirect('/page-list-customers');
    });
});

// Route for adding a product
app.post('/add-product', (req, res) => {
    const { name, price, category } = req.body;
    db.query('INSERT INTO products (name, price, category) VALUES (?, ?, ?)', [name, price, category], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to add product' });
        }
        res.redirect('/page-list-product');
    });
});

// Route for listing customers (CRUD actions like Edit, Delete)
app.get('/page-list-customers', (req, res) => {
    db.query('SELECT * FROM customers', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to fetch customers' });
        }
        res.render('page-list-customers.php', { customers: results });
    });
});

// Route for listing products
app.get('/page-list-product', (req, res) => {
    db.query('SELECT * FROM products', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to fetch products' });
        }
        res.render('page-list-product.php', { products: results });
    });
});

// Route to view feedback
app.get('/feedback', (req, res) => {
    res.sendFile(path.join(__dirname, 'feedback.php'));
});

// Route for chart data (e.g., sales data)
app.get('/chart-data', (req, res) => {
    // Example: Fetch sales data for the chart
    db.query('SELECT MONTH
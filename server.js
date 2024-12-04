const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const dotenv = require('dotenv');
const path = require('path');
const moment = require('moment');
const { Configuration, OpenAI } = require("openai");
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const PDFDocument = require('pdfkit');

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

// User Signup
app.post('/signup', async (req, res) => {
    const { username, email, password } = req.body;
    const hashedPassword = await bcrypt.hash(password, 10);

    db.query('INSERT INTO users (username, email, password) VALUES (?, ?, ?)', [username, email, hashedPassword], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to sign up user' });
        }
        res.json({ message: 'User created successfully' });
    });
});

// User Login
app.post('/login', (req, res) => {
    const { email, password } = req.body;

    db.query('SELECT * FROM users WHERE email = ?', [email], async (err, result) => {
        if (err || result.length === 0) {
            return res.status(401).json({ error: 'Invalid email or password' });
        }
        const user = result[0];
        const isValid = await bcrypt.compare(password, user.password);

        if (!isValid) {
            return res.status(401).json({ error: 'Invalid email or password' });
        }

        // Generate JWT token
        const token = jwt.sign({ id: user.id, email: user.email }, process.env.JWT_SECRET, { expiresIn: '1h' });
        res.json({ token });
    });
});

// Middleware for verifying JWT token
const authenticateJWT = (req, res, next) => {
    const token = req.header('Authorization');
    if (!token) {
        return res.status(403).json({ message: 'Access denied' });
    }

    jwt.verify(token, process.env.JWT_SECRET, (err, user) => {
        if (err) {
            return res.status(403).json({ message: 'Invalid or expired token' });
        }
        req.user = user;
        next();
    });
};

// Product Routes
app.post('/add-product', authenticateJWT, (req, res) => {
    const { name, price, category, stock } = req.body;
    db.query('INSERT INTO products (name, price, category, stock) VALUES (?, ?, ?, ?)', [name, price, category, stock], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to add product' });
        }
        res.json({ message: 'Product added successfully' });
    });
});

app.get('/products', authenticateJWT, (req, res) => {
    db.query('SELECT * FROM products', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to fetch products' });
        }
        res.json(results);
    });
});

// Sales Routes
app.post('/add-sale', authenticateJWT, (req, res) => {
    const { product_id, quantity, total_price, customer_id, staff_id } = req.body;
    db.query('INSERT INTO sales (product_id, quantity, total_price, customer_id, staff_id) VALUES (?, ?, ?, ?, ?)', [product_id, quantity, total_price, customer_id, staff_id], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to record sale' });
        }
        res.json({ message: 'Sale recorded successfully' });
    });
});

app.get('/sales', authenticateJWT, (req, res) => {
    db.query('SELECT * FROM sales', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to fetch sales' });
        }
        res.json(results);
    });
});

// Category Routes
app.get('/categories', authenticateJWT, (req, res) => {
    db.query('SELECT * FROM categories', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to fetch categories' });
        }
        res.json(results);
    });
});

// Inventory Routes
app.get('/inventory', authenticateJWT, (req, res) => {
    db.query('SELECT * FROM inventory', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to fetch inventory' });
        }
        res.json(results);
    });
});

// Expenses Routes
app.post('/add-expense', authenticateJWT, (req, res) => {
    const { description, amount, date } = req.body;
    db.query('INSERT INTO expenses (description, amount, date) VALUES (?, ?, ?)', [description, amount, date], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to add expense' });
        }
        res.json({ message: 'Expense added successfully' });
    });
});

app.get('/expenses', authenticateJWT, (req, res) => {
    db.query('SELECT * FROM expenses', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Failed to fetch expenses' });
        }
        res.json(results);
    });
});

// PDF Report Route
app.get('/generate-pdf', authenticateJWT, (req, res) => {
    const doc = new PDFDocument();
    res.setHeader('Content-Type', 'application/pdf');
    res.setHeader('Content-Disposition', 'attachment; filename=sales_report.pdf');

    doc.pipe(res);

    doc.fontSize(25).text('Sales Report', { align: 'center' });
    doc.moveDown();
    doc.fontSize(12).text('Date: ' + moment().format('YYYY-MM-DD'), { align: 'left' });

    db.query('SELECT * FROM sales', (err, results) => {
        if (err) {
            doc.text('Failed to fetch sales data');
        } else {
            results.forEach((sale, index) => {
                doc.text(`${index + 1}. Sale ID: ${sale.id}, Product ID: ${sale.product_id}, Total Price: ${sale.total_price}`);
            });
        }
        doc.end();
    });
});

// Chart Data Route (Sales)
app.get('/chart-data', authenticateJWT, (req, res) => {
    db.query('SELECT MONTH(date) AS month, SUM(total_price) AS total_sales FROM sales GROUP BY MONTH(date)', (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Database query failed' });
        }
        res.json(results);
    });
});

// Payment and Subscription Routes
app.post('/payment', authenticateJWT, (req, res) => {
    // Handle payment logic here
    const { amount, method } = req.body;
    // Example: Insert into payment table
    db.query('INSERT INTO payments (amount, method) VALUES (?, ?)', [amount, method], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Payment failed' });
        }
        res.json({ message: 'Payment processed successfully' });
    });
});

app.post('/subscribe', authenticateJWT, (req, res) => {
    // Handle subscription logic here
    const { plan, user_id } = req.body;
    db.query('INSERT INTO subscriptions (plan, user_id) VALUES (?, ?)', [plan, user_id], (err, result) => {
        if (err) {
            return res.status(500).json({ error: 'Subscription failed' });
        }
        res.json({ message: 'Subscription successful' });
    });
});

// Start the server
app.listen(port, () => {
    console.log(`Server is running at http://localhost:${port}`);
});

<?php
require_once 'Database.php';

// 1. Basic query with fetchAll
$users = Database::fetchAll("SELECT * FROM users WHERE active = ?", [1]);
foreach ($users as $user) {
    echo "User: {$user->name}, Email: {$user->email}\n";
}

// 2. Fetch single row
$user = Database::fetch("SELECT * FROM users WHERE id = ?", [5]);
if ($user) {
    echo "Found user: {$user->name}\n";
}

// 3. Fetch single column
$count = Database::fetchColumn("SELECT COUNT(*) FROM users WHERE active = ?", [1]);
echo "Total active users: $count\n";

// 4. Insert with parameters
$lastId = Database::insert(
    "INSERT INTO users (name, email, created_at) VALUES (?, ?, ?)",
    ['John Doe', 'john@example.com', date('Y-m-d H:i:s')]
);
echo "New user ID: $lastId\n";

// 5. Update with parameters
$affected = Database::execute(
    "UPDATE users SET name = ? WHERE id = ?",
    ['Jane Doe', 5]
);
echo "Updated $affected rows\n";

// 6. Delete with parameters
$deleted = Database::execute(
    "DELETE FROM users WHERE id = ? AND status = ?",
    [10, 'inactive']
);
echo "Deleted $deleted rows\n";

// 7. Transaction example
try {
    Database::beginTransaction();

    Database::insert(
        "INSERT INTO orders (user_id, total) VALUES (?, ?)",
        [1, 100.00]
    );

    Database::insert(
        "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)",
        [Database::connect()->lastInsertId(), 5, 2]
    );

    Database::commit();
    echo "Transaction completed successfully!\n";
} catch (Exception $e) {
    Database::rollBack();
    echo "Transaction failed: " . $e->getMessage();
}

// 8. Check if record exists
$exists = Database::exists("SELECT 1 FROM users WHERE email = ?", ['test@example.com']);
if ($exists) {
    echo "Email already exists!\n";
}

// 9. Using named parameters
$user = Database::fetch(
    "SELECT * FROM users WHERE email = :email AND active = :active",
    [':email' => 'john@example.com', ':active' => 1]
);

// 10. Convenient insert method
$newId = Database::insertInto('users', [
    'name' => 'John Smith',
    'email' => 'john.smith@example.com',
    'created_at' => date('Y-m-d H:i:s')
]);

// 11. Convenient update method
$updatedRows = Database::updateTable(
    'users',
    ['name' => 'John Updated', 'email' => 'updated@example.com'],
    'id = ?',
    [1]
);

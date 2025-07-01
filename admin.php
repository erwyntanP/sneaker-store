<?php
$conn = new mysqli("localhost", "root", "", "sneaker_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $idToDelete = intval($_GET['delete']);
    $conn->query("DELETE FROM payment WHERE id = $idToDelete");
    header("Location: admin.php");
    exit;
}

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT * FROM payment";
if ($search !== '') {
    $sql .= " WHERE name LIKE '%$search%'";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Back and Theme Toggle -->
    <a href="dashboard.php" class="button">Back</a>
    <button id="toggleTheme" class="theme-toggle"><span id="themeIcon">🌙</span> Theme</button>

    <h2>Payments</h2>

    <!-- Search Form -->
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Card Number</th>
            <th>Expiry</th>
            <th>CVV</th>
            <th>Action</th>
        </tr>
        <?php
        $serialNumber = 1;
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $maskedCard = str_repeat("*", 12) . substr($row["card_number"], -4);
                $maskedCVV = str_repeat("*", strlen($row["cvv"]));
                echo "<tr>";
                echo "<td>{$serialNumber}</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['phone']}</td>";
                echo "<td>{$row['address']}</td>";
                echo "<td>{$maskedCard}</td>";
                echo "<td>{$row['expiry_month']}/{$row['expiry_year']}</td>";
                echo "<td>{$maskedCVV}</td>";
                echo isset($row['id']) ? "<td><a href='admin.php?delete={$row['id']}' onclick=\"return confirm('Delete this payment?');\">Delete</a></td>" : "<td><i>No ID</i></td>";
                echo "</tr>";
                $serialNumber++;
            }
        } else {
            echo "<tr><td colspan='8'>No payments found</td></tr>";
        }
        $conn->close();
        ?>
    </table>

    <script>
        const toggleBtn = document.getElementById('toggleTheme');
        const themeIcon = document.getElementById('themeIcon');
        const body = document.body;

        function setThemeIcon() {
            themeIcon.textContent = body.classList.contains('dark-mode') ? '☀️' : '🌙';
        }

        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
        }
        setThemeIcon();

        toggleBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
            setThemeIcon();
        });
    </script>
</body>
</html>

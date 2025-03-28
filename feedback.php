<?php
session_start();
include 'db.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM feedback WHERE id = '$delete_id'");
    header("Location: feedback.php");
    exit();
}

// Fetch feedback records with join to get student details
$sql = "SELECT f.*, s.idno, s.laboratory, s.fullname 
        FROM feedback f 
        JOIN sit_in s ON f.sit_in_id = s.id 
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring - Feedbacks</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
    <style>
        @keyframes glow {
            0% { text-shadow: 0 0 5px #ffd700; }
            50% { text-shadow: 0 0 20px #ffd700, 0 0 30px #ffd700; }
            100% { text-shadow: 0 0 5px #ffd700; }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
            100% { transform: translateY(0px); }
        }
        
        .star-rating {
            color: #ffd700;
            animation: glow 2s ease-in-out infinite, float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- Admin Navbar -->
<div class="navbar bg-[#2c343c] shadow-lg">
    <div class="navbar-start">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="text-xl font-bold text-white ml-2">Admin Dashboard</span>
        </div>
    </div>
    
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1 gap-2">
            <li>
                <a href="admin_dashboard.php" class="btn btn-ghost text-white hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <a href="search.php" class="btn btn-ghost text-white hover:bg-white/10">Search</a>
            </li>
            <li>
                <a href="students.php" class="btn btn-ghost text-white hover:bg-white/10">Students</a>
            </li>
            <li>
                <a href="sit_in.php" class="btn btn-ghost text-white hover:bg-white/10">Sit-in</a>
            </li>
            <li>
                <a href="sit_in_records.php" class="btn btn-ghost text-white hover:bg-white/10">View Records</a>
            </li>
            <li>
                <a href="reservation.php" class="btn btn-ghost text-white hover:bg-white/10">Reservation</a>
            </li>
            <li>
                <a href="reports.php" class="btn btn-ghost text-white hover:bg-white/10">Reports</a>
            </li>
            <li>
                <a href="feedback.php" class="btn btn-ghost text-white hover:bg-white/10">Feedback Reports</a>
            </li>
        </ul>
    </div>

    <div class="navbar-end">
        <a href="logout.php" class="btn btn-error btn-outline gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
        </a>
    </div>
</div>

<!-- Rest of your existing content -->
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-lg mb-8 p-6">
        <h2 class="text-3xl font-bold text-center text-blue-600">FEEDBACK REPORTS</h2>
    </div>

    <!-- Search Section -->
    <div class="max-w-md mx-auto mb-6">
        <div class="relative">
            <input type="text" 
                   id="searchInput" 
                   placeholder="Search feedbacks..." 
                   class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="feedbackTable">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $date = date('Y-m-d', strtotime($row['created_at']));
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $row['idno']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['fullname']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['laboratory']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $date; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="star-rating text-lg">
                                    <?php
                                    for ($i = 0; $i < $row['rating']; $i++) {
                                        echo "<span class='inline-block'>★</span>";
                                    }
                                    for ($i = $row['rating']; $i < 5; $i++) {
                                        echo "<span class='inline-block opacity-30'>☆</span>";
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo $row['feedback_text']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="confirmDelete(<?php echo $row['id']; ?>)" 
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 transition duration-150 ease-in-out">
                                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No feedback available</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <!-- Average Rating Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <h3 class="text-2xl font-bold text-center mb-4">Average Rating</h3>
            <?php
            $avgQuery = "SELECT AVG(rating) as avg_rating FROM feedback";
            $avgResult = $conn->query($avgQuery);
            $avgRow = $avgResult->fetch_assoc();
            $avgRating = number_format($avgRow['avg_rating'], 1);
            ?>
            <div class="text-center">
                <p class="text-4xl font-bold mb-2"><?php echo $avgRating; ?></p>
                <div class="star-rating text-2xl">
                    <?php
                    $fullStars = floor($avgRating);
                    $decimal = $avgRating - $fullStars;
                    
                    for ($i = 0; $i < $fullStars; $i++) {
                        echo "<span class='inline-block'>★</span>";
                    }
                    if ($decimal >= 0.5) {
                        echo "<span class='inline-block'>★</span>";
                        $fullStars++;
                    }
                    for ($i = $fullStars; $i < 5; $i++) {
                        echo "<span class='inline-block opacity-50'>☆</span>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Total Feedbacks Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <h3 class="text-2xl font-bold text-center mb-4">Total Feedbacks</h3>
            <?php
            $countQuery = "SELECT COUNT(*) as total FROM feedback";
            $countResult = $conn->query($countQuery);
            $countRow = $countResult->fetch_assoc();
            ?>
            <p class="text-4xl font-bold text-center mb-2"><?php echo $countRow['total']; ?></p>
            <p class="text-center text-sm opacity-90">responses received</p>
        </div>

        <!-- Latest Feedback Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <h3 class="text-2xl font-bold text-center mb-4">Latest Feedback</h3>
            <?php
            $latestQuery = "SELECT created_at FROM feedback ORDER BY created_at DESC LIMIT 1";
            $latestResult = $conn->query($latestQuery);
            $latestRow = $latestResult->fetch_assoc();
            $latestDate = $latestRow ? date('M d, Y', strtotime($latestRow['created_at'])) : 'No feedback yet';
            ?>
            <p class="text-4xl font-bold text-center mb-2"><?php echo $latestDate; ?></p>
            <p class="text-center text-sm opacity-90">last feedback received</p>
        </div>
    </div>
</div><!-- End of container -->

<script>
// Enhanced search function
document.getElementById("searchInput").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#feedbackTable tr");
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

// Delete confirmation using DaisyUI modal
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this feedback?")) {
        window.location.href = `feedback.php?delete_id=${id}`;
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>

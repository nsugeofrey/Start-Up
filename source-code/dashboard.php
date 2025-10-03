<?php 
ob_start();
session_start();

//the core goal here with this php code is to make sure the dashboard
//is not accessed in anyway if there is no admin-login session.  
if(!isset($_SESSION['admin'])){
    header("location: admin-login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons (similar to Lucide React) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dist/css/dashboard.css">
</head>
<body>
    <?php include "side-bar.php"; ?>
    <!-- Main Content Area -->
    <div id="content">
        <!-- Header -->
        <header id="header">
            <button id="sidebarToggleOpen" class="btn btn-link text-secondary d-lg-none"><i class="fas fa-bars"></i></button>
            <h1 class="text-2xl font-semibold text-gray-800 d-none d-lg-block">Dashboard Overview</h1>
            <div class="d-flex align-items-center">
                <div class="text-secondary me-3 d-none d-sm-block">Admin User</div>
                <!-- Avatar with Dropdown -->
                <div class="dropdown avatar-dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://placehold.co/40x40/cccccc/ffffff?text=AU" alt="User Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="admin-logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Overlay for mobile sidebar -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Dashboard Content -->
        <main class="p-4">
            <h2 class="text-3xl font-bold text-gray-800 mb-4 d-none d-lg-block">Overview</h2>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card bg-primary">
                        <div>
                            <p class="text-sm font-medium opacity-80">Total Listings</p>
                            <p class="text-3xl font-bold mt-1" id="totalListings">0</p>
                        </div>
                        <div class="icon-wrapper"><i class="fas fa-file-alt"></i></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card bg-success">
                        <div>
                            <p class="text-sm font-medium opacity-80">Active Listings</p>
                            <p class="text-3xl font-bold mt-1" id="activeListings">0</p>
                        </div>
                        <div class="icon-wrapper"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card bg-warning text-dark">
                        <div>
                            <p class="text-sm font-medium opacity-80">Pending Sales</p>
                            <p class="text-3xl font-bold mt-1" id="pendingSales">0</p>
                        </div>
                        <div class="icon-wrapper"><i class="fas fa-dollar-sign"></i></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card bg-info text-dark">
                        <div>
                            <p class="text-sm font-medium opacity-80">New Leads Today</p>
                            <p class="text-3xl font-bold mt-1" id="newLeadsToday">0</p>
                        </div>
                        <div class="icon-wrapper"><i class="fas fa-user-plus"></i></div>
                    </div>
                </div>
            </div>

            <!-- All other charts and recent activity removed -->
            
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (Popper.js is a dependency) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- Chart.js (still included in case you add charts back later, but not actively used for this simplified view) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Mock data for the dashboard
            const mockDashboardData = {
                totalListings: 1250,
                activeListings: 870,
                pendingSales: 185,
                newLeadsToday: 42,
                totalUsers: 5600,
                recentActivity: [ // Data still here but not used for rendering
                    { id: 1, type: 'New Listing', description: 'Luxury Villa added in Palm Jumeirah', time: '2 hours ago' },
                    { id: 2, type: 'Lead Assigned', description: 'John Doe assigned to Agent Sarah', time: '4 hours ago' },
                    { id: 3, type: 'Property Update', description: 'Price reduced for Apartment in Downtown', time: '1 day ago' },
                    { id: 4, type: 'New User', description: 'Jane Smith registered', time: '2 days ago' },
                    { id: 5, type: 'Listing Deactivated', description: 'Studio in JVC sold', time: '3 days ago' },
                ],
                listingsByStatus: [ // Data still here but not used for rendering
                    { name: 'Active', value: 870 },
                    { name: 'Pending', value: 185 },
                    { name: 'Sold', value: 195 },
                ],
                leadsBySource: [ // Data still here but not used for rendering
                    { name: 'Website', value: 320 },
                    { name: 'Referral', value: 180 },
                    { name: 'Social Media', value: 110 },
                    { name: 'Portal', value: 90 },
                ],
                monthlySales: [ // Data still here but not used for rendering
                    { month: 'Jan', sales: 120 },
                    { month: 'Feb', sales: 150 },
                    { month: 'Mar', sales: 130 },
                    { month: 'Apr', sales: 180 },
                    { month: 'May', sales: 200 },
                    { month: 'Jun', sales: 220 },
                    { month: 'Jul', sales: 250 },
                ],
            };

            // Function to render Stat Cards - this remains
            function renderStatCards() {
                $('#totalListings').text(mockDashboardData.totalListings);
                $('#activeListings').text(mockDashboardData.activeListings);
                $('#pendingSales').text(mockDashboardData.pendingSales);
                $('#newLeadsToday').text(mockDashboardData.newLeadsToday);
            }

            // Functions for charts and recent activity are no longer called
            // function renderRecentActivity() { ... }
            // function createListingsByStatusChart() { ... }
            // function createLeadsBySourceChart() { ... }

            // Sidebar toggle functionality - this remains
            $('#sidebarToggleOpen').on('click', function() {
                $('#sidebar').addClass('show');
                $('#sidebarOverlay').addClass('show');
            });

            $('#sidebarToggleClose, #sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('show');
                $('#sidebarOverlay').removeClass('show');
            });

            // Initial render calls - only stat cards remain
            renderStatCards();

            // Adjust content margin on window resize for responsiveness - this remains
            $(window).on('resize', function() {
                if ($(window).width() >= 992) {
                    $('#sidebar').removeClass('show');
                    $('#sidebarOverlay').removeClass('show');
                }
            });
        });
    </script>
</body>
</html>

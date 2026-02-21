<?php
// This is where $content gets injected into the template
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f6f8fb;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Sidebar styling - kept this pretty clean */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            padding: 20px;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
        }

        .sidebar h4 {
            font-weight: 700;
        }

        /* Navigation links */
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 12px;
            color: #555;
            text-decoration: none;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .sidebar a:hover {
            background: #f1f3ff;  /* Light hover effect */
        }

        /* Active link gets gradient background - looks nice */
        .sidebar a.active {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: #fff;
        }

        .content {
            margin-left: 260px;  /* Offset for sidebar width */
            padding: 30px;
        }

        /* Card boxes for content sections */
        .card-box {
            background: #fff;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* Additional card styling - might have duplicated this accidentally */
        .card-box {
            border-radius: 25px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        /* Icon containers for dashboard cards */
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* Yeah, defined card-box three times... should probably clean this up later */
        .card-box {
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>

<!-- Sidebar navigation -->
<div class="sidebar">
    <h4>Dashboard</h4>
    
    <!-- Home link with active state check -->
    <a href="/" class="<?php echo $page == 'home' ? 'active' : ''; ?>">
        🏠 Home
    </a>

    <!-- Clients page link -->
    <a href="/clients" class="<?php echo $page == 'clients' ? 'active' : ''; ?>">
        👥 Clients
    </a>

    <!-- Contacts page link -->
    <a href="/contacts" class="<?php echo $page == 'contacts' ? 'active' : ''; ?>">
        📇 Contacts
    </a>
</div>

<!-- Main content area -->
<div class="content">

    <!-- Top bar with greeting and search -->
    <div class="topbar">
        <h3>Hello Ndamulelo 👋</h3>
        <input type="text" placeholder="Search..." class="form-control" style="width:200px;">
    </div>

    <!-- Dynamic content gets inserted here -->
    <?php echo $content; ?>

</div>

<!-- Counter animation script -->
<script>
    // Animate numbers counting up
    document.querySelectorAll('.counter').forEach(counter => {
        
        let target = +counter.getAttribute('data-target');
        let count = 0;
        
        let speed = 1000; // Adjust this to make it faster or slower
        
        let update = () => {
            let increment = Math.ceil(target / speed);
            
            if (count < target) {
                count += increment;
                counter.innerText = count;
                setTimeout(update, 100);  // Update every 100ms
            } else {
                counter.innerText = target;  // Make sure we hit the exact target
            }
        };
        
        update();  // Start the animation
    });
</script>

</body>
</html>
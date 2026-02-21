<?php

// Get the page from URL, default to home if not set
$page = $_GET['page'] ?? 'home';

// Route to different pages based on the page parameter
switch ($page) {

    case 'clients':
        require 'views/clients/index.php';
        break;

    case 'client_create':
        require 'views/clients/create.php';
        break;

    case 'client_show':
        require 'views/clients/show.php';
        break;

    case 'contacts':
        require 'views/contacts/index.php';
        break;

    case 'contact_create':
        require 'views/contacts/create.php';
        break;

    case 'contact_show':
        require 'views/contacts/show.php';
        break;

    default:
        // Home page - show dashboard with stats

        require_once 'config/database.php';

        // Dashboard Stats - pulling counts from database
        $clientsCount = $conn->query("SELECT COUNT(*) as total FROM clients")->fetch_assoc()['total'] ?? 0;
        $contactsCount = $conn->query("SELECT COUNT(*) as total FROM contacts")->fetch_assoc()['total'] ?? 0;
        $linksCount = $conn->query("SELECT COUNT(*) as total FROM client_contact")->fetch_assoc()['total'] ?? 0;

        // Start output buffering so we can capture the HTML
        ob_start();
?>

<!-- Dashboard Stats Cards -->
<div class="card-box mb-4">

    <div class="row g-4">

        <!-- Clients Card -->
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3">

                <div class="icon-box bg-success-subtle">
                    👥
                </div>

                <div>
                    <small class="text-muted">Total Clients</small>
                    <h4 class="mb-0 counter" data-target="<?php echo $clientsCount; ?>">0</h4>
                </div>

            </div>
        </div>

        <!-- Contacts Card -->
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3">

                <div class="icon-box bg-primary-subtle">
                    📇
                </div>

                <div>
                    <small class="text-muted">Total Contacts</small>
                    <h4 class="mb-0 counter" data-target="<?php echo $contactsCount; ?>">0</h4>
                </div>

            </div>
        </div>

        <!-- Links Card -->
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3">

                <div class="icon-box bg-warning-subtle">
                    🔗
                </div>

                <div>
                    <small class="text-muted">Linked Records</small>
                    <h4 class="mb-0 counter" data-target="<?php echo $linksCount; ?>">0</h4>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- Welcome Card with Action Buttons -->
<div class="card-box text-center">

    <p class="text-muted mb-4">
        Manage your clients and contacts from one place
    </p>

    <!-- Quick action buttons -->
    <div class="d-flex justify-content-center gap-3">

        <a href="/clients" class="btn btn-primary px-4">
            Clients
        </a>

        <a href="/contacts" class="btn btn-success px-4">
            Contacts
        </a>

    </div>

</div>

<?php
        // Capture the buffered content
        $content = ob_get_clean();
        
        // Load the layout template with our content
        require 'views/layout.php';
}

?>
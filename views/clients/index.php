<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Client.php';

// Initialize the Client model and fetch all clients
$client = new Client($conn);
$clients = $client->getAll();

// Start output buffering to capture the HTML content
ob_start();
?>

<div class="card-box">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">All Clients</h4>
            <small class="text-muted">Manage your clients</small>
        </div>

        <!-- Button to navigate to client creation page -->
        <a href="/client_create" class="btn btn-primary">
            + Add Client
        </a>
    </div>

    <?php if ($clients->num_rows > 0): ?>
        <!-- We have clients, so let's display them in a table -->

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th class="text-start">Name</th>
                        <th class="text-start">Client Code</th>
                        <th class="text-center">No. of Contacts</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while($row = $clients->fetch_assoc()): ?>
                        <tr>

                            <td>
                                <!-- Client name is clickable - links to detail page -->
                                <a href="/client_show?id=<?php echo $row['id']; ?>" 
                                   class="fw-semibold text-decoration-none">
                                    <?php echo $row['name']; ?>
                                </a>
                            </td>

                            <td class="text-muted">
                                <?php echo $row['client_code']; ?>
                            </td>

                            <td class="text-center">
                                <!-- Display contact count as a badge -->
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <?php echo $row['total_contacts']; ?>
                                </span>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>
        <!-- No clients found - show empty state message -->

        <div class="text-center py-5">
            <h5>No client(s) found</h5>
            <p class="text-muted">Start by adding your first client</p>
        </div>

    <?php endif; ?>

</div>

<?php
// Capture the buffered content and store it in $content variable
$content = ob_get_clean();

// Load the main layout template
require __DIR__ . '/../layout.php';
?>
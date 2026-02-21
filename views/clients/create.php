<?php
// Load database connection and client model
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Client.php';

$client = new Client($conn);

$message = ""; // Initialize message variable for error handling

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the name from POST data
    $name = $_POST["name"] ?? "";

    // Try to create the client
    $result = $client->create($name);

    // If successful, redirect back to clients list
    if ($result["success"]) {
        header("Location: /clients");
        exit;
    } else {
        // Otherwise show error message
        $message = $result["message"];
    }
}

// Start output buffering - I remember doing this to capture content for layout
ob_start();
?>

<div class="card-box" style="max-width: 500px;">

    <h4 class="mb-4">Create Client</h4>

    <!-- Back button to return to clients list -->
    <a href="/clients" class="btn btn-light mb-3">← Back</a>

    <?php 
    // Display error message if there is one
    if (!empty($message)): 
    ?>
        <div class="alert alert-danger">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Client Name</label>
            <input 
                type="text" 
                name="name" 
                class="form-control" 
                placeholder="Enter client name"
                required
            >
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary w-100">
            Save Client
        </button>

    </form>

</div>

<?php
// Capture the buffered content and include it in the layout
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
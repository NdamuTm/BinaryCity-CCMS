<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Contact.php';

$contact = new Contact($conn);

$message = "";  // Initialize error message variable

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data - using null coalescing for safety
    $name = $_POST["name"] ?? "";
    $surname = $_POST["surname"] ?? "";
    $email = $_POST["email"] ?? "";

    // Try to create the contact
    $result = $contact->create($name, $surname, $email);

    if ($result["success"]) {
        // Success - redirect back to contacts list
        header("Location: /contacts");
        exit;
    } else {
        // Something went wrong, show error
        $message = $result["message"];
    }
}

ob_start();
?>

<div class="card-box" style="max-width: 600px;">

    <h4 class="mb-4">Create Contact</h4>

    <!-- Back button to return to contacts list -->
    <a href="/contacts" class="btn btn-light mb-3">← Back</a>

    <?php if (!empty($message)): ?>
        <!-- Display error message if there is one -->
        <div class="alert alert-danger">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input 
                type="text" 
                name="name" 
                class="form-control"
                placeholder="Enter first name"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Surname</label>
            <input 
                type="text" 
                name="surname" 
                class="form-control"
                placeholder="Enter surname"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input 
                type="email" 
                name="email" 
                class="form-control"
                placeholder="Enter email address"
                required
            >
        </div>

        <button class="btn btn-primary w-100">
            Save Contact
        </button>

    </form>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
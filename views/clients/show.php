<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Client.php';
require_once __DIR__ . '/../../models/Contact.php';
require_once __DIR__ . '/../../models/ClientContact.php';

// Set up our models
$clientModel = new Client($conn);
$contactModel = new Contact($conn);
$linkModel = new ClientContact($conn);

// Get the client ID from URL, default to 0 if not provided
$id = $_GET['id'] ?? 0;
// Determine which tab we're on - general or contacts
$tab = $_GET['tab'] ?? 'general';

// Fetch the client data and related contacts
$client = $clientModel->getById($id);
$contacts = $linkModel->getContacts($id);
$allContacts = $contactModel->getAll();

// Handle contact linking when form is submitted
if (isset($_POST['contact_id'])) {
    $linkModel->link($id, $_POST['contact_id']);
    // Redirect back to contacts tab after linking
    header("Location: /client_show?id=$id&tab=contacts");
    exit;
}

// Handle contact unlinking from the URL parameter
if (isset($_GET['unlink'])) {
    $linkModel->unlink($id, $_GET['unlink']);
    // Stay on contacts tab after unlinking
    header("Location: /client_show?id=$id&tab=contacts");
    exit;
}

// Start capturing output
ob_start();
?>

<div class="card-box">

    <!-- Client header section with name and back button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0"><?php echo $client['name']; ?></h4>
            <small class="text-muted">Code: <?php echo $client['client_code']; ?></small>
        </div>

        <a href="/clients" class="btn btn-light">← Back</a>
    </div>

    <!-- Tab navigation -->
    <ul class="nav nav-pills mb-4">

        <li class="nav-item">
            <a class="nav-link <?php echo $tab == 'general' ? 'active' : ''; ?>"
               href="/client_show?id=<?php echo $id; ?>&tab=general">
               General
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo $tab == 'contacts' ? 'active' : ''; ?>"
               href="/client_show?id=<?php echo $id; ?>&tab=contacts">
               Contacts
            </a>
        </li>

    </ul>

    <?php if ($tab == 'general'): ?>

        <!-- General info tab - displays basic client details -->
        <div class="p-3 bg-light rounded">

            <p><strong>Name:</strong> <?php echo $client['name']; ?></p>
            <p><strong>Client Code:</strong> <?php echo $client['client_code']; ?></p>

        </div>

    <?php else: ?>

        <!-- Contacts tab - shows linked contacts and linking form -->
        <div class="mb-4">

            <h5 class="mb-3">Linked Contacts</h5>

            <?php if ($contacts->num_rows > 0): ?>
                <!-- Display table of contacts linked to this client -->

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($row = $contacts->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <!-- Note: Displaying surname first, then name -->
                                    <?php echo $row['surname'] . ' ' . $row['name']; ?>
                                </td>

                                <td class="text-muted">
                                    <?php echo $row['email']; ?>
                                </td>

                                <td>
                                    <!-- Unlink button - removes the association -->
                                    <a href="/client_show?id=<?php echo $id; ?>&tab=contacts&unlink=<?php echo $row['id']; ?>"
                                       class="btn btn-sm btn-outline-danger">
                                       Unlink
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>
                <!-- No contacts linked yet -->

                <div class="text-center py-4">
                    <p class="text-muted">No contacts found</p>
                </div>

            <?php endif; ?>

        </div>

        <!-- Form section for linking new contacts -->
        <div class="card-box" style="background:#f9fafc;">

            <h6 class="mb-3">Link Contact</h6>

            <form method="POST" action="/client_show?id=<?php echo $id; ?>&tab=contacts">

                <div class="d-flex gap-2">

                    <select name="contact_id" class="form-select">

                        <?php if ($allContacts->num_rows > 0): ?>
                            <!-- Loop through all available contacts -->

                            <?php while($c = $allContacts->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>">
                                    <?php echo $c['surname'] . ' ' . $c['name']; ?>
                                </option>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <!-- No contacts exist in the system -->

                            <option disabled>No contacts available</option>

                        <?php endif; ?>

                    </select>

                    <!-- Disable button if there are no contacts to link -->
                    <button class="btn btn-primary"
                            <?php echo $allContacts->num_rows == 0 ? 'disabled' : ''; ?>>
                        Link
                    </button>

                </div>

            </form>

        </div>

    <?php endif; ?>

</div>

<?php
// Get the buffered content
$content = ob_get_clean();
// Load it into the layout template
require __DIR__ . '/../layout.php';
?>
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Contact.php';
require_once __DIR__ . '/../../models/Client.php';
require_once __DIR__ . '/../../models/ClientContact.php';

// Initialize our models
$contactModel = new Contact($conn);
$clientModel = new Client($conn);
$linkModel = new ClientContact($conn);

// Get the contact ID from URL, default to 0 if not provided
$id = $_GET['id'] ?? 0;
$tab = $_GET['tab'] ?? 'general';  // Which tab are we viewing?

// Fetch the contact details from database
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$contact = $stmt->get_result()->fetch_assoc();

// Get linked clients for this contact
$clients = $linkModel->getClients($id);
// Also get all available clients for the dropdown
$allClients = $clientModel->getAll();

// Handle linking a client to this contact
if (isset($_POST['client_id'])) {
    $linkModel->link($_POST['client_id'], $id);
    // Redirect back to clients tab after linking
    header("Location: /contact_show?id=$id&tab=clients");
    exit;
}

// Handle unlinking a client
if (isset($_GET['unlink'])) {
    $linkModel->unlink($_GET['unlink'], $id);
    // Redirect back to clients tab
    header("Location: /contact_show?id=$id&tab=clients");
    exit;
}

ob_start();
?>

<div class="card-box">

    <!-- Header section with contact name and back button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">
                <?php echo $contact['surname'] . ' ' . $contact['name']; ?>
            </h4>
            <small class="text-muted"><?php echo $contact['email']; ?></small>
        </div>

        <a href="/contacts" class="btn btn-light">← Back</a>
    </div>

    <!-- Navigation tabs -->
    <ul class="nav nav-pills mb-4">

        <li class="nav-item">
            <a class="nav-link <?php echo $tab == 'general' ? 'active' : ''; ?>"
               href="/contact_show?id=<?php echo $id; ?>&tab=general">
               General
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo $tab == 'clients' ? 'active' : ''; ?>"
               href="/contact_show?id=<?php echo $id; ?>&tab=clients">
               Clients
            </a>
        </li>

    </ul>

    <?php if ($tab == 'general'): ?>

        <!-- GENERAL TAB - Basic contact information -->
        <div class="p-3 bg-light rounded">

            <p><strong>Name:</strong> <?php echo $contact['surname'] . ' ' . $contact['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $contact['email']; ?></p>

        </div>

    <?php else: ?>

        <!-- CLIENTS TAB - Shows linked clients and linking form -->
        <div class="mb-4">

            <h5 class="mb-3">Linked Clients</h5>

            <?php if ($clients->num_rows > 0): ?>

                <!-- Display table of linked clients -->
                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($row = $clients->fetch_assoc()): ?>
                            <tr>

                                <td class="fw-semibold">
                                    <?php echo $row['name']; ?>
                                </td>

                                <td class="text-muted">
                                    <?php echo $row['client_code']; ?>
                                </td>

                                <td>
                                    <!-- Unlink button removes the connection -->
                                    <a href="/contact_show?id=<?php echo $id; ?>&tab=clients&unlink=<?php echo $row['id']; ?>"
                                       class="btn btn-sm btn-outline-danger">
                                       Unlink
                                    </a>
                                </td>

                            </tr>
                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <!-- Empty state when no clients are linked -->
                <div class="text-center py-4">
                    <p class="text-muted">No clients found</p>
                </div>

            <?php endif; ?>

        </div>

        <!-- LINK FORM - Add new client link -->
        <div class="card-box" style="background:#f9fafc;">

            <h6 class="mb-3">Link Client</h6>

            <form method="POST" action="/contact_show?id=<?php echo $id; ?>&tab=clients">

                <div class="d-flex gap-2">

                    <!-- Dropdown to select client -->
                    <select name="client_id" class="form-select">

                        <?php if ($allClients->num_rows > 0): ?>

                            <?php while($c = $allClients->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>">
                                    <?php echo $c['name']; ?>
                                </option>
                            <?php endwhile; ?>

                        <?php else: ?>

                            <option disabled>No clients available</option>

                        <?php endif; ?>

                    </select>

                    <!-- Link button - disabled if no clients exist -->
                    <button class="btn btn-primary"
                            <?php echo $allClients->num_rows == 0 ? 'disabled' : ''; ?>>
                        Link
                    </button>

                </div>

            </form>

        </div>

    <?php endif; ?>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
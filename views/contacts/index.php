<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Contact.php';

// Initialize contact model and fetch all contacts
$contact = new Contact($conn);
$contacts = $contact->getAll();

ob_start();
?>

<div class="card-box">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">All Contacts</h4>
            <small class="text-muted">Manage your contacts</small>
        </div>

        <!-- Button to add new contact -->
        <a href="/contact_create" class="btn btn-primary">
            + Add Contact
        </a>
    </div>

    <?php if ($contacts->num_rows > 0): ?>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Email</th>
                        <th class="text-center">Clients</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while($row = $contacts->fetch_assoc()): ?>
                        <tr>

                            <!-- Contact name - clickable to view details -->
                            <td>
                                <a href="/contact_show?id=<?php echo $row['id']; ?>" class="fw-semibold text-decoration-none">
                                    <?php echo $row['name']; ?>
                                </a>
                            </td>

                            <!-- Surname column -->
                            <td class="text-muted">
                                <?php echo $row['surname']; ?>
                            </td>

                            <!-- Email address -->
                            <td class="text-muted">
                                <?php echo $row['email']; ?>
                            </td>

                            <!-- Display number of clients associated with this contact -->
                            <td class="text-center">
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <?php echo $row['total_clients']; ?>
                                </span>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <!-- Empty state when no contacts exist yet -->
        <div class="text-center py-5">
            <h5>No contact(s) found</h5>
            <p class="text-muted">Start by adding your first contact</p>
        </div>

    <?php endif; ?>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
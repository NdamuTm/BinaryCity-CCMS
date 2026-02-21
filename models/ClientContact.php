<?php

require_once __DIR__ . '/../config/database.php';

class ClientContact {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Link a contact to a client
    // Returns false if already linked, true if successful
    public function link($client_id, $contact_id) {
        // First, check if this link already exists
        // We don't want duplicate relationships in the database
        $check = $this->conn->prepare("
            SELECT id FROM client_contact 
            WHERE client_id = ? AND contact_id = ?
        ");
        $check->bind_param("ii", $client_id, $contact_id);
        $check->execute();
        
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            return false; // Already linked, no need to insert again
        }

        // If not linked yet, create the relationship
        $stmt = $this->conn->prepare("
            INSERT INTO client_contact (client_id, contact_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $client_id, $contact_id);

        return $stmt->execute();
    }

    // Remove the link between a client and contact
    public function unlink($client_id, $contact_id) {
        $stmt = $this->conn->prepare("
            DELETE FROM client_contact
            WHERE client_id = ? AND contact_id = ?
        ");
        $stmt->bind_param("ii", $client_id, $contact_id);
        
        return $stmt->execute();
    }

    // Get all contacts associated with a specific client
    // Sorted by surname, then first name
    public function getContacts($client_id) {
        $stmt = $this->conn->prepare("
            SELECT c.*
            FROM contacts c
            JOIN client_contact cc ON c.id = cc.contact_id
            WHERE cc.client_id = ?
            ORDER BY c.surname ASC, c.name ASC
        ");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        
        $contacts = $stmt->get_result();
        
        return $contacts;
    }

    // Get all clients associated with a specific contact
    // Note: A contact can be linked to multiple clients
    public function getClients($contact_id) {
        $stmt = $this->conn->prepare("
            SELECT c.*
            FROM clients c
            JOIN client_contact cc ON c.id = cc.client_id
            WHERE cc.contact_id = ?
            ORDER BY c.name ASC
        ");
        $stmt->bind_param("i", $contact_id);
        $stmt->execute();
        
        $clients = $stmt->get_result();
        
        return $clients;
    }
}
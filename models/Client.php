<?php

require_once __DIR__ . '/../config/database.php';

class Client {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new client with auto-generated unique code
    // Note: The code is based on initials + sequential number
    public function create($name) {
        $name = trim($name);

        // Validate name isn't empty
        if (empty($name)) {
            return ["success" => false, "message" => "Client name is required"];
        }

        // Insert the client first
        $stmt = $this->conn->prepare("INSERT INTO clients (name) VALUES (?)");
        $stmt->bind_param("s", $name);

        if (!$stmt->execute()) {
            return ["success" => false, "message" => "Error creating client"];
        }

        // Get the newly created client ID
        $client_id = $stmt->insert_id;

        // Generate unique code for this client
        $client_code = $this->generateClientCode($name);

        // Update the client record with the generated code
        $update = $this->conn->prepare("UPDATE clients SET client_code = ? WHERE id = ?");
        $update->bind_param("si", $client_code, $client_id);
        $update->execute();

        return ["success" => true, "message" => "Client created successfully"];
    }

    // Fetch all clients along with their contact counts
    // TODO: Maybe add pagination here later if we get too many clients
    public function getAll() {
        $sql = "
            SELECT 
                c.id,
                c.name,
                c.client_code,
                COUNT(cc.contact_id) as total_contacts
            FROM clients c
            LEFT JOIN client_contact cc ON c.id = cc.client_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ";

        $result = $this->conn->query($sql);

        return $result;
    }

    // Generate a unique client code using initials + number
    // Example: "ABC001", "ABC002", etc.
    private function generateClientCode($name) {
        // Split name into words and get first letter of each
        $words = explode(" ", strtoupper($name));
        $letters = "";

        foreach ($words as $word) {
            if (!empty($word)) {
                $letters .= $word[0];  // Take first character
            }
        }

        // Use only first 3 letters
        $letters = substr($letters, 0, 3);

        // If we don't have 3 letters, pad with random letters
        // This happens with single-word short names
        while (strlen($letters) < 3) {
            $letters .= chr(rand(65, 90)); // Random A-Z
        }

        $num = 1;

        // Keep incrementing until we find an unused code
        while (true) {
            $code = $letters . str_pad($num, 3, "0", STR_PAD_LEFT);

            // Check if this code already exists
            $check = $this->conn->query("SELECT id FROM clients WHERE client_code = '$code'");
            if ($check->num_rows == 0) {
                return $code;  // Found unused code
            }

            $num++;  // Try next number
        }
    }

    // Get a single client by ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result;
    }
}
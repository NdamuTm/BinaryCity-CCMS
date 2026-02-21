<?php

require_once __DIR__ . '/../config/database.php';

class Contact {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new contact entry
    public function create($name, $surname, $email) {
        // Clean up the input data first
        $name = trim($name);
        $surname = trim($surname);
        $email = trim($email);

        // Basic validation - making sure we have all required fields
        if (empty($name) || empty($surname) || empty($email)) {
            return ["success" => false, "message" => "All fields are required"];
        }

        // Check if the email format is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Invalid email address"];
        }

        // Need to verify if this email already exists in our database
        $checkQuery = "SELECT id FROM contacts WHERE email = ?";
        $check = $this->conn->prepare($checkQuery);
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Email is already taken
            return ["success" => false, "message" => "Email already exists"];
        }

        // Okay, let's insert the new contact now
        $insertQuery = "INSERT INTO contacts (name, surname, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param("sss", $name, $surname, $email);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Contact created successfully"];
        } else {
            // Something went wrong during insert
            return ["success" => false, "message" => "Error creating contact"];
        }
    }

    // Retrieve all contacts with their associated client counts
    public function getAll() {
        // Query to get contacts along with how many clients each one has
        // Using LEFT JOIN so we get contacts even if they have no clients yet
        $query = "
            SELECT 
                c.id,
                c.name,
                c.surname,
                c.email,
                COUNT(cc.client_id) as total_clients
            FROM contacts c
            LEFT JOIN client_contact cc ON c.id = cc.contact_id
            GROUP BY c.id
            ORDER BY c.surname ASC, c.name ASC
        ";

        $result = $this->conn->query($query);
        
        return $result;
    }
}
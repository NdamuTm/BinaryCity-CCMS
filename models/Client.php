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
    // private function generateClientCode($name) {
    //     // Split name into words and get first letter of each
    //     $words = explode(" ", strtoupper($name));
    //     $letters = "";

    //     foreach ($words as $word) {
    //         if (!empty($word)) {
    //             $letters .= $word[0];  // Take first character
    //         }
    //     }

    //     // Use only first 3 letters
    //     $letters = substr($letters, 0, 3);

    //     // If we don't have 3 letters, pad with random letters
    //     // This happens with single-word short names
    //     while (strlen($letters) < 3) {
    //         $letters .= chr(rand(65, 90)); // Random A-Z
    //     }

    //     $num = 1;

    //     // Keep incrementing until we find an unused code
    //     while (true) {
    //         $code = $letters . str_pad($num, 3, "0", STR_PAD_LEFT);

    //         // Check if this code already exists
    //         $check = $this->conn->query("SELECT id FROM clients WHERE client_code = '$code'");
    //         if ($check->num_rows == 0) {
    //             return $code;  // Found unused code
    //         }

    //         $num++;  // Try next number
    //     }
    // }

    public function generateClientCode(string $name): string
{
    $alpha = $this->generateAlphaPrefix($name);
    $num = 1;

    while (true) {
        // Try numbers 001 -> 999 for this alpha
        while ($num <= 999) {
            $code = $alpha . str_pad((string)$num, 3, '0', STR_PAD_LEFT);

            if (!$this->clientCodeExists($code)) {
                return $code;
            }

            $num++;
        }

        // If we got here, alpha***999 is full -> increment alpha and restart numbers
        $alpha = $this->incrementAlpha($alpha);
        $num = 1;
    }
}

/**
 * Generates the 3-letter alpha part based on your rules:
 * - 1 word: XAA
 * - 2 words: XY A
 * - 3+ words: XYZ
 */
private function generateAlphaPrefix(string $name): string
{
    $name = strtoupper(trim($name));

    // Split by spaces, remove empties
    $words = array_values(array_filter(preg_split('/\s+/', $name)));

    // Keep only A-Z letters when extracting initials
    $getFirstLetter = function ($word) {
        $word = preg_replace('/[^A-Z]/', '', $word);
        return $word !== '' ? $word[0] : 'A';
    };

    if (count($words) >= 3) {
        $a = $getFirstLetter($words[0]);
        $b = $getFirstLetter($words[1]);
        $c = $getFirstLetter($words[2]);
        return $a . $b . $c;
    }

    if (count($words) === 2) {
        $a = $getFirstLetter($words[0]);
        $b = $getFirstLetter($words[1]);
        return $a . $b . 'A';
    }

    // 1 word (or empty)
    $firstWord = $words[0] ?? 'A';
    $a = $getFirstLetter($firstWord);
    return $a . 'AA';
}

/**
 * Increments a 3-letter A-Z code like base-26:
 * AAA -> AAB -> ... -> AAZ -> ABA -> ... -> ZZZ -> AAA
 */
private function incrementAlpha(string $alpha): string
{
    $alpha = strtoupper($alpha);

    $chars = str_split($alpha);
    for ($i = 2; $i >= 0; $i--) {
        if ($chars[$i] < 'Z') {
            $chars[$i] = chr(ord($chars[$i]) + 1);
            return implode('', $chars);
        }
        $chars[$i] = 'A'; // carry
    }

    // overflow ZZZ -> AAA (extreme case)
    return 'AAA';
}

private function clientCodeExists(string $code): bool
{
    $stmt = $this->conn->prepare("SELECT 1 FROM clients WHERE client_code = ? LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res && $res->num_rows > 0;
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
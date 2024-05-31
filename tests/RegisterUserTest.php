<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../server/user_functions.php';

class RegisterUserTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        $this->conn = connectToDatabase();
        $this->conn->query("CREATE TABLE IF NOT EXISTS Users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(50) NOT NULL,
            registration_date DATE NOT NULL
        )");
    }

    protected function tearDown(): void
    {
        $this->conn->query("DROP TABLE Users");
        $this->conn->close();
    }

    public function testRegisterUser()
    {
        $result = registerUser($this->conn, 'testuser', 'password123', 'test@example.com');
        $this->assertTrue($result, "Failed to register user");

        $user = getUserByUsername($this->conn, 'testuser');
        $this->assertNotFalse($user, "User not found after registration");
        $this->assertEquals('testuser', $user['username']);
        $this->assertTrue(password_verify('password123', $user['password']));
        $this->assertEquals('test@example.com', $user['email']);
        $this->assertEquals(date('Y-m-d'), $user['registration_date']);
    }
}
?>
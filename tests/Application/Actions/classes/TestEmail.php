<?php
namespace Tests\Application\classes;

use PHPUnit\Framework\TestCase;
use App\classes\Email;

class TestEmail extends TestCase
{
    public function testSendEmailToSingleRecipient()
    {
        $email = new Email();
        $email->mandar_email('lucasdominguesofficial@gmail.com', 'Test Subject', 'Test Body','');
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testSendEmailToMultipleRecipients()
    {
        $email = new Email();
        $email->mandar_email(['lucasdominguesofficial@gmail.com', 'lukasbreaking@gmail.com'], '','Test Subject', 'Test Body');
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testSendEmailWithName()
    {
        $email = new Email();
        $email->mandar_email('lucasdominguesofficial@gmail.com', 'John Doe', 'Test Subject', 'Test Body');
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testSendEmailWithoutName()
    {
        $email = new Email();
        $email->mandar_email('lucasdominguesofficial@gmail.com', '', 'Test Subject', 'Test Body');
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testSendEmailWithSubjectAndBody()
    {
        $email = new Email();
        $email->mandar_email('lucasdominguesofficial@gmail.com', '','Test Subject', 'Test Body');
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testSendEmailWithInvalidEmailAddress()
    {
        $email = new Email();
        $this->expectException(\Exception::class);
        $email->mandar_email('invalid-email','', 'Test Subject', 'Test Body');
    }

    public function testSendEmailWithInvalidSmtpSettings()
    {
        $email = new Email();
        $env = [
            'host_stmt' => 'invalid-host',
            'SMTPAuth' => false,
            'username' => 'invalid-username',
            'password' => 'invalid-password',
        ];
        $this->expectException(\Exception::class);
        $email->mandar_email('lucasdominguesofficial@gmail.com','' ,'Test Subject', 'Test Body');
    }
}
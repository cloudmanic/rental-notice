<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:validate-email 
                            {email : The email address to validate}
                            {--timeout=10 : Connection timeout in seconds}
                            {--debug : Show detailed debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate if an email address exists using SMTP verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $timeout = $this->option('timeout');
        $debug = $this->option('debug');

        $this->info("Validating email: {$email}");

        // Basic format validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email format: {$email}");
            return 1;
        }

        list($user, $domain) = explode('@', $email);

        // Get MX records for the domain
        $this->line("Looking up MX records for {$domain}...");
        $mxRecords = $this->getMxRecords($domain);

        if (empty($mxRecords)) {
            $this->error("No mail servers found for domain: {$domain}");
            return 1;
        }

        if ($debug) {
            $this->line("Found " . count($mxRecords) . " mail servers:");
            foreach ($mxRecords as $host => $priority) {
                $this->line("- {$host} (priority: {$priority})");
            }
        }

        // Try to validate using SMTP
        $isValid = $this->validateEmailBySmtp($email, $mxRecords, $timeout, $debug);

        if ($isValid) {
            $this->info("✅ Email address appears to be valid");
            return 0;
        } else {
            $this->error("❌ Email address appears to be invalid or undeliverable");
            return 1;
        }
    }

    /**
     * Get MX records for a domain sorted by priority
     */
    private function getMxRecords(string $domain): array
    {
        $records = [];
        getmxrr($domain, $mxhosts, $mxpriorities);

        if (empty($mxhosts)) {
            return [];
        }

        // Combine hosts and priorities
        $records = array_combine($mxhosts, $mxpriorities);

        // Sort by priority (lower is more preferred)
        asort($records);

        return $records;
    }

    /**
     * Validate email by establishing an SMTP connection with the mail server
     */
    private function validateEmailBySmtp(string $email, array $mxRecords, int $timeout, bool $debug): bool
    {
        list($user, $domain) = explode('@', $email);

        // From email - we use a made-up address at the same domain
        $from = "smtp-validator@cloudmanic.com";

        foreach ($mxRecords as $host => $priority) {
            $this->line("Connecting to mail server: {$host}...");

            // Create a socket connection
            $socket = @fsockopen($host, 25, $errno, $errstr, $timeout);

            if (!$socket) {
                if ($debug) {
                    $this->warn("Failed to connect to {$host}: {$errstr} (error {$errno})");
                }
                continue; // Try next server
            }

            // Set the socket timeout
            stream_set_timeout($socket, $timeout);

            // Start SMTP conversation
            $responses = [];

            // Read the initial greeting
            $responses[] = $this->sendCommand($socket, null, '220', $debug);

            // Send HELO
            $responses[] = $this->sendCommand($socket, "HELO cloudmanic.com", '250', $debug);

            // Send MAIL FROM
            $responses[] = $this->sendCommand($socket, "MAIL FROM:<{$from}>", '250', $debug);

            // Send RCPT TO - this is where we actually validate the email
            $rcptResponse = $this->sendCommand($socket, "RCPT TO:<{$email}>", null, $debug);
            $responses[] = $rcptResponse;

            // RSET the connection
            $responses[] = $this->sendCommand($socket, "RSET", '250', $debug);

            // QUIT
            $responses[] = $this->sendCommand($socket, "QUIT", '221', $debug);

            // Close socket
            fclose($socket);

            // Check validation result
            if ($rcptResponse && preg_match('/^250/', $rcptResponse)) {
                return true; // Email appears valid
            }

            if ($debug && $rcptResponse) {
                if (preg_match('/^550/', $rcptResponse)) {
                    $this->warn("Server reported that the mailbox doesn't exist");
                } elseif (preg_match('/^4/', $rcptResponse)) {
                    $this->warn("Server reported a temporary error");
                }
            }

            // If we got a definitive error, no need to try other servers
            if ($rcptResponse && preg_match('/^5/', $rcptResponse)) {
                return false;
            }
        }

        return false; // If we got here, all servers failed
    }

    /**
     * Send a command to the SMTP server and get the response
     */
    private function sendCommand($socket, $command, $expectedCode = null, $debug = false): ?string
    {
        if ($command !== null) {
            if ($debug) {
                $this->line(">> {$command}");
            }
            fwrite($socket, $command . "\r\n");
        }

        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;

            if ($debug) {
                $this->line("<< " . trim($line));
            }

            // If this is a multi-line response, continue reading
            if (substr($line, 3, 1) !== ' ') {
                continue;
            }

            break;
        }

        // Check if the response code matches what we expected
        if ($expectedCode !== null && !preg_match('/^' . $expectedCode . '/', $response)) {
            if ($debug) {
                $this->warn("Expected code {$expectedCode}, but got: " . substr($response, 0, 3));
            }
        }

        return $response;
    }
}

<?php declare(strict_types=1);

namespace Utils;

/**
 * NOTE: Google Secret Manager is great but the Project requires a bit of setup
 * before things will work properly.
 *
 * In general the server setup is:
 *  1. Enable Google Secrets api in the Google Project
 *  2. Generate a keyfile.json in the google cloud shell in same directory
 *     as the .yaml file
 *  3. Enable the IAM api and add "Secret Manager Secret Accessor" role to...
 *     * TBH i have no idea, I added it to most roles that looked relevant
 *       and it worked. I don't need to be super secure for a demo.
 *  4. Tell google cloud to boot php with BCMath extension
 *     NOTE: This is all commited in code but it's confusing to setup on
 *           your own.
 *     * Add a `php.ini` file with the single line "extension=bcmath" in the
 *       same dir as the .yaml file
 *     * Require the extension in the composer.json file
 *
 * During Setup:
 * - If you didn't enable and configure the IAM api properly, you'll get a lot
 *   of "access denied" messages.
 * - If you didn't install the BCMath extension in PHP you'll get an
 *   "undefined function bccomp()" error.
 */
use Google\Cloud\SecretManager\V1\Replication;
use Google\Cloud\SecretManager\V1\Replication\Automatic;
use Google\Cloud\SecretManager\V1\Secret;
use Google\Cloud\SecretManager\V1\SecretManagerServiceClient;
use Google\Cloud\SecretManager\V1\SecretPayload;
use Utils\ServerUtils;

class SecretManager {
   private const PROJECT_ID = 'burnished-flare-348022';
   private const TEST_SECRET_ID = 'my-test-scret-2';
   private const LATEST_VERSION = 'latest';

   private const DB_CONN_SECRET_ID = 'demo-site-alpha-db-conn';
   private const DB_NAME_SECRET_ID = 'demo-site-alpha-db-name';
   private const DB_USER_SECRET_ID = 'demo-site-alpha-db-user';
   private const DB_PASS_SECRET_ID = 'demo-site-alpha-db-pass';

   private $client;

   public static function spoilSecret(): string {
      $payload = 'dev-site';
      if (ServerUtils::shouldUseGoogleSecrets()) {
         $sManager = new self();
         $payload = $sManager->fetchSecretData(self::TEST_SECRET_ID);
      }
      return $payload;
   }

   public static function fetchDBConnectionInfo(): array {
      if (!ServerUtils::shouldUseGoogleSecrets()) {
         return [];
      }

      $sManager = new self();
      return [
         'dbConn' => $sManager->fetchSecretData(self::DB_CONN_SECRET_ID),
         'dbName' => $sManager->fetchSecretData(self::DB_NAME_SECRET_ID),
         'dbUser' => $sManager->fetchSecretData(self::DB_USER_SECRET_ID),
         'dbPass' => $sManager->fetchSecretData(self::DB_PASS_SECRET_ID),
      ];
   }

   private function __construct() {
      $this->client = new SecretManagerServiceClient();
   }

   /**
    * NOTE: These are secrets, don't be displaying them all willy-nilly.
    *
    * Keep the secrets in the code, only display test values in dev.
    */
   private function fetchSecretData(string $secretid): string {
      $name = $this->client->secretVersionName(self::PROJECT_ID, $secretid, /*versionid*/self::LATEST_VERSION);
      $response = $this->client->accessSecretVersion($name);
      return (string)$response->getPayload()->getData();
   }

   /**
    * NOTE: We probably won't need this but during setup enabling
    * the server to talk to Google Secrets was a bit of a pain to
    * configure.
    *
    * Creating a secret worked before the IAM roles were properly
    * setup to read the secret. It let me know that the pipeline I
    * created to link the server to Google Secrets was working.
    */
   private function createSecret(string $secretid): string {
      $project = $this->client->projectName(self::PROJECT_ID);
      // Create the parent secret.
      $secret = $this->client->createSecret($project, $secretid,
      new Secret([
            'replication' => new Replication([
                  'automatic' => new Automatic(),
            ]),
         ])
      );
      // Add the secret version.
      $version = $this->client->addSecretVersion($secret->getName(), new SecretPayload([
         'data' => 'hello world',
      ]));
      // Access the secret version.
      $response = $this->client->accessSecretVersion($version->getName());
      // Return the secret payload.
      return (string)$response->getPayload()->getData();
   }
}

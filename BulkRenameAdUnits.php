<?php
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\AdsApi\Examples\Dfp\v201705\InventoryService;

require __DIR__ . '/vendor/autoload.php';

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\Dfp\DfpServices;
use Google\AdsApi\Dfp\DfpSession;
use Google\AdsApi\Dfp\DfpSessionBuilder;
use Google\AdsApi\Dfp\Util\v201705\StatementBuilder;
use Google\AdsApi\Dfp\v201705\InventoryService;
use Google\AdsApi\Dfp\v201705\InventoryStatus;

/**
 * This example gets all ad units.
 *
 * <p>It is meant to be run from a command line (not as a webpage) and requires
 * that you've setup an `adsapi_php.ini` file in your home directory with your
 * API credentials and settings. See README.md for more info.
 */
class GetAllAdUnits {

  /*########################
  #                        #
  #     Configuration      #
  #                        #
  ########################*/

  /*** Ad Unit name to match ***/
  private static $name_matcher = "%"; // SQL regular expression

  /*** Status to match ***/
  private static $status_matcher = InventoryStatus::ARCHIVED; // InventoryStatus::ACTIVE, InventoryStatus::INACTIVE and InventoryStatus::ARCHIVED are allowed.

  /*** Suffix to append to renamed Ad Units ***/
  private static $suffix = "_archive";


  /*##########################################################*/

  public static function runExample(DfpServices $dfpServices,
      DfpSession $session) {
    $inventoryService =
        $dfpServices->get($session, InventoryService::class);

    // Create a statement to select ad units.
    $pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
    $statementBuilder = (new StatementBuilder())
        ->where('name LIKE :name AND status = :status')
        ->orderBy('id ASC')
        ->limit($pageSize)
        ->withBindVariableValue('name', self::$name_matcher)
        ->withBindVariableValue('status', self::$status_matcher);

    // Retrieve a small amount of ad units at a time, paging
    // through until all ad units have been retrieved.
    $totalResultSetSize = 0;
    do {
      $page = $inventoryService->getAdUnitsByStatement(
          $statementBuilder->toStatement());

      // Print out some information for each ad unit.
      if ($page->getResults() !== null) {
        $totalResultSetSize = $page->getTotalResultSetSize();
        $i = $page->getStartIndex();
        foreach ($page->getResults() as $adUnit) {
          printf(
              "%d) Ad unit with ID '%s' and name '%s' was found.\n",
              $i++,
              $adUnit->getId(),
              $adUnit->getName()
          );
        }
      }

      $statementBuilder->increaseOffsetBy($pageSize);
    } while ($statementBuilder->getOffset() < $totalResultSetSize);

    printf("Number of results found: %d\n", $totalResultSetSize);

    if ($totalResultSetSize > 0) {
      // Remove limit and offset from statement so we can reuse the statement.
      $statementBuilder->removeLimitAndOffset();

      $adUnits = $page->getResults();

      foreach ($adUnits as $adUnit) {
        if ( strpos($adUnit->getName(), self::$suffix) !== false ) {
          printf($adUnit->getName() . " -> Not Changed\n");
        }
        else {
          $ad_name = $adUnit->getName();
          $adUnit->setName( $ad_name . self::$suffix);
          printf($adUnit->getName() . " -> Renamed\n");
        }
      }
      $adUnits = $inventoryService->updateAdUnits($adUnits);
    }

  }

  public static function main() {
    // Generate a refreshable OAuth2 credential for authentication.
    $oAuth2Credential = (new OAuth2TokenBuilder())
        ->fromFile()
        ->build();

    // Construct an API session configured from a properties file and the OAuth2
    // credentials above.
    $session = (new DfpSessionBuilder())
        ->fromFile()
        ->withOAuth2Credential($oAuth2Credential)
        ->build();

    self::runExample(new DfpServices(), $session);
  }
}

GetAllAdUnits::main();

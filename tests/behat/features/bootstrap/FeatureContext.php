<?php

use Behat\Behat\Tester\Exception\PendingException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  protected $screenshot_dir = '/tmp';

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct($parameters) {
    $this->parameters = $parameters;
    if (isset($parameters['screenshot_dir'])) {
      $this->screenshot_dir = $parameters['screenshot_dir'];
    }
  }

  /**
   * Take screenshot when step fails. Works only with Selenium2Driver.
   * Screenshot is saved at [Date]/[Feature]/[Scenario]/[Step].jpg
   *  @AfterStep
   */
  public function after(Behat\Behat\Hook\Scope\AfterStepScope $scope) {
    if ($scope->getTestResult()->getResultCode() === 99) {
      $driver = $this->getSession()->getDriver();
      if ($driver instanceof Behat\Mink\Driver\Selenium2Driver) {
        $fileName = date('d-m-y') . '-' . uniqid() . '.png';
        $this->saveScreenshot($fileName, $this->screenshot_dir);
        print 'Screenshot at: '.$this->screenshot_dir.'/' . $fileName;
      }
    }
  }

  /**
   * #camp: To go directly to an external website.
   *
   * Example: When I go to "https://www.google.com" website
   *
   * @When /^I go to "(?P<domain>[^"]*)" website$/
   */
  public function iGoToWebsite($domain) {
    $this->getSession()->visit($domain);
  }

  /**
   * #camp: To wait for seconds before going to the next step.
   *
   * Example 1:  And wait for "1" second
   * Example 2: When I wait for "5" seconds
   * Example 3:  And wait 1 second
   * Example 4: When I wait for 60 seconds
   * Example 5:  And wait 1s
   * Example 6: When I wait for 60s
   *
   * @When /^(?:|I )wait (?:|for )"(?P<seconds>\d+)" second(?:|s)$/
   * @When /^(?:|I )wait (?:|for )(?P<seconds>\d+) second(?:|s)$/
   * @When /^(?:|I )wait (?:|for )(?P<seconds>\d+)s$/
   */
  public function iWaitForSeconds($seconds) {
    $this->getSession()->wait($seconds * 1000);
  }

  /**
   * #camp: To wait for minutes before going to the next step
   *
   * Example 1:  And I wait for "1" minute
   * Example 2: When I wait for "2" minutes
   * Example 3:  And wait 1 minute
   * Example 4: When I wait for 3 minutes
   * Example 5:  And wait 1m
   * Example 6: When I wait for 3m
   *
   * @When /^(?:|I )wait (?:|for )"(?P<minutes>\d+)" minute(?:|s)$/
   * @When /^(?:|I )wait (?:|for )(?P<minutes>\d+) minute(?:|s)$/
   * @When /^(?:|I )wait (?:|for )(?P<minutes>\d+)m$/
   */
  public function iWaitForMinutes($minutes) {
    $this->getSession()->wait($minutes * 60 * 1000);
  }

  /**
   * #camp : I wait max of seconds for the page to be ready and loaded.
   *
   * Exmaple 1: And wait
   * Exmaple 2: And I wait
   * Example 3: And wait for the page
   * Example 4: And I wait for the page
   * Example 5: And wait max of 5 seconds
   * Example 6: And wait max of 5s
   * Example 7: And I wait max of 5s
   * Example 8: And I wait max of "5" seconds
   * Example 9: And I wait max of "5" seconds for the page to be ready and loaded
   *
   * @Given /^(?:|I )wait max of "(?P<time>\d+)" second(?:|s)(?:| for the page to be ready and loaded)$/
   * @Given /^(?:|I )wait max of (?P<time>\d+) second(?:|s)(?:| for the page to be ready and loaded)$/
   * @Given /^(?:|I )wait max of (?P<time>\d+)s(?:| for the page to be ready and loaded)$/
   * @Given /^(?:|I )wait(?:| for the page)$/
   *
   * @throws BehaviorException If timeout is reached
   */
  public function iWaitMaxOfSecondsForThePageToBeReadyAndLoaded($time = 10000) {
    if (!$this->getSession()->getDriver() instanceof Selenium2Driver) {
      return;
    }
    $start = microtime(true);
    $end = $start + $time / 1000.0;
    $defaultCondition = true;
    $conditions = [
      "document.readyState == 'complete'",           // Page is ready
      "typeof $ != 'undefined'",                     // jQuery is loaded
      "!$.active",                                   // No ajax request is active
      "$('#page').css('display') == 'block'",        // Page is displayed (no progress bar)
      "$('.loading-mask').css('display') == 'none'", // Page is not loading (no black mask loading page)
      "$('.jstree-loading').length == 0",            // Jstree has finished loading
    ];
    $condition = implode(' && ', $conditions);
    // Make sure the AJAX calls are fired up before checking the condition
    $this->getSession()->wait(100, false);
    $this->getSession()->wait($time, $condition);
    // Check if we reached the timeout unless the condition is false to explicitly wait the specified time
    if ($condition !== false && microtime(true) > $end) {
      throw new BehaviorException(sprintf('Timeout of %d reached when checking on %s', $time, $condition));
    }
  }

  /**
   * @When /^I do not follow redirects$/
   */
  public function iDoNotFollowRedirects() {
    $this->getSession()->getDriver()->getClient()->followRedirects(false);
  }

  /**
   * @Then /^I (?:am|should be) redirected to "([^"]*)"$/
   */
  public function iAmRedirectedTo($actualPath) {
    $headers = $this->getSession()->getResponseHeaders();
    assertTrue(isset($headers['Location'][0]));

    $redirectComponents = parse_url($headers['Location'][0]);
    assertEquals($redirectComponents['path'], $actualPath);
  }
}

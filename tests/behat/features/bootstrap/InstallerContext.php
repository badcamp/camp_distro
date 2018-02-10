<?php

use Behat\Behat\Tester\Exception\PendingException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;

/**
 * Defines application features from the specific context.
 */
class InstallerContext extends RawDrupalContext implements SnippetAcceptingContext {

  /** @var \Drupal\Driver\DrushDriver $drush  */
  protected $drush;

  /** @var \Drupal\Driver\DrupalDriver $drupal */
  protected $drupal;

  /** @BeforeScenario */
  public function gatherContexts(BeforeScenarioScope $scope)
  {
    $this->drush = $this->getDriver('drush');
    $this->drupal = $this->getDriver('drupal');
  }

  /**
   * @Given I have a clean database
   */
  public function iHaveACleanDatabase()
  {
    try {
      passthru('drupal --yes --root=/var/www/docroot database:drop');
    }catch(Exception $exception) {

    }
  }

  /**
   * @AfterStep
   */
  public function afterStep(AfterStepScope $scope)
  {
    // Do nothing on steps that pass
    $result = $scope->getTestResult();
    if ($result->isPassed()) {
      return;
    }
    // Otherwise, dump the page contents.
    $session = $this->getSession();
    $page = $session->getPage();
    $html = $page->getContent();
    $html = static::trimHead($html);
    print "::::::::::::::::::::::::::::::::::::::::::::::::\n";
    print $html . "\n";
    print "::::::::::::::::::::::::::::::::::::::::::::::::\n";
  }

  /**
   * Remove everything in the '<head>' element except the
   * title, because it is long and uninteresting.
   */
  protected static function trimHead($html)
  {
    $html = preg_replace('#\<head\>.*\<title\>#sU', '<head><title>', $html);
    $html = preg_replace('#\</title\>.*\</head\>#sU', '</title></head>', $html);
    return $html;
  }
}

@api @install @drush
Feature: Installation - Encampment - Default installation.
  As an Application site Builder
  I want to be able to install Encampment for my camp
  So that I will be able to initiate the site with the default installation

  Background: Clean Database
    Given I have a clean database

  @javascript
  Scenario: Default installation for Encampment
    Given I go to "/core/install.php"
    Then I should see "Choose language"
    When I press "Save and continue"
    And I wait for the batch job to finish
    And I wait for the batch job to finish
    Then I should see "Configure Camp"
    When I fill in "Camp Name" with "Encampment Distro"
    And I fill in "Site email address" with "admin@example.com"
    And I fill in "Username" with "admin"
    And I fill in "Password" with "admin"
    And I fill in "Confirm password" with "admin"
    And I fill in "Email address" with "admin@example.com"
    And I additionally select "United States" from "Default country"
    And I additionally select "Los Angeles" from "Default time zone"
    And I uncheck the box "Check for updates automatically"
    And I press "Save and continue"
    Then I should see "Camp Features"
    When I press "Assemble and configure"
    And I wait for the batch job to finish
    Then I should see "Configure Features"
    When I press "Assemble and install"
    And I wait for the batch job to finish
    Then I should see "Welcome to Encampment Distro"
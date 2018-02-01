Feature: Installation - Encampment - Default installation.
  As an Application site Builder
  I want to be able to install Encampment for my camp
  So that I will be able to initiate the site with the default installation

  @javascript @tools @install @local @development
  Scenario: Default installation for Encampment
    Given I go to the homepage
    Then I should be redirected to "/core/install.php"
    And I wait
    Then I should see "Choose language"
    When I press "Save and continue"
    And I wait
    Then I should see "Database configuration"
    When I fill in "test_varbase4" for "Database name"
    And I fill in "root" for "Database username"
    And I fill in "123___" for "Database password"
    And I press "Save and continue"
    And I wait for the batch job to finish
    Then I should see "Configure site"
    When I fill in "Site name" with "Varbase4"
      # And I fill in "Site email address" with "noreply@vardot.com"
      # And I fill in "Username" with "webmaster"
    And I fill in "Password" with "dD.123123"
    And I fill in "Confirm password" with "dD.123123"
      # And I fill in "Email address" with "webmaster@vardot.com"
    And I uncheck the box "Check for updates automatically"
    And I press "Save and continue"
    And I wait for the batch job to finish
    Then I should see "Multilingual configuration"
    When I press "Save and continue"
    And I wait for the batch job to finish
    Then I should see "Extra components"
    When I press "Save and continue"
    And I wait for the batch job to finish
    Then I should see "Welcome to Varbase4"
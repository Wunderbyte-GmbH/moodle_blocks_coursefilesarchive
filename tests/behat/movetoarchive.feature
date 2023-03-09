@block @block_coursefilesarchive @_file_upload @javascript
Feature: Move file to archive
  As a teacher I need to move file to the archive from the course file archive block.  Note: The duckling image is copyright 'Gareth J Barnard 2020' use only for this test without permission.

  Background:
    Given the following "users" exist:
      | username  | firstname     | lastname | email                  |
      | denise    | Denise Emma   | Bug      | debug1@local.localhost |
    And the following "categories" exist:
      | name               | category | idnumber |
      | Top                | 0        | TOP      |
      | CFA Category       | TOP      | CFACAT   |
    And the following "courses" exist:
      | fullname               | shortname | category | format  | numsections |
      | CourseFilesArchive     | CFA       | CFACAT   | topics  | 5           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | denise   | CFA    | editingteacher |
    And I log in as "denise"
    And I am on "CourseFilesArchive" course homepage with editing mode on
    And I add the "Course files archive" block
    And I upload "blocks/coursefilesarchive/tests/fixtures/Duckling.jpg" file to "Course files archive" filemanager
    And I press "Save changes"
    And "//div[contains(@class,'fp-filename') and contains(.,'Duckling.jpg')]" "xpath_element" should exist in the "#id_coursefilesarchive_filemanager_fieldset" "css_element"
    And I log out


  Scenario: Move file to archive.
    When I log in as "denise"
    And I am on "CourseFilesArchive" course homepage
    And I press "Update archive"
    And I press "Compare files"
    #redirect
    Then "//td[contains(@class,'cfaname') and contains(.,'Duckling.jpg')]" "xpath_element" should exist in the "#cfafilecomparison" "css_element"
    And "//td[contains(@class,'cfastate') and contains(@class,'cfastatecoursearchive')]" "xpath_element" should exist in the "#cfafilecomparison" "css_element"

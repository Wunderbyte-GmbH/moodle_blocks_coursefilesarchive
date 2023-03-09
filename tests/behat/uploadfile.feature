@block @block_coursefilesarchive
Feature: File upload
  As a teacher I need to upload a file to the course file archive block.

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
    And I log out

  @_file_upload @javascript
  Scenario: Upload an file to the block - note: The duckling image is copyright 'Gareth J Barnard 2020' use only for this test without permission.
    When I log in as "denise"
    And I am on "CourseFilesArchive" course homepage
    When I upload "blocks/coursefilesarchive/tests/fixtures/Duckling.jpg" file to "Course files archive" filemanager
    And I press "Save changes"
    #Then "//img[contains(@src, 'Duckling.jpg')]" "xpath_element" should exist in the "#id_coursefilesarchive_filemanager_fieldset" "css_element"
    Then "//div[contains(@class,'fp-filename') and contains(.,'Duckling.jpg')]" "xpath_element" should exist in the "#id_coursefilesarchive_filemanager_fieldset" "css_element"

@block @block_coursefilesarchive
Feature: Block visibility
  In order to have visibility of the block.
  As a teacher
  I can see the block
  As a student
  I cannot see the block

  Background:
    Given the following "users" exist:
      | denise    | Denise Emma   | Bug  | debug1@local.localhost |
      | dennis    | Dennis Edward | Bug  | debug2@local.localhost |
    And the following "categories" exist:
      | name           | category | idnumber |
      | CFA Category   | 1        | CFACAT   |
    And the following "courses" exist:
      | fullname               | shortname | category | format  | numsections |
      | CourseFilesArchive     | CFA       | CFACAT   | topics  | 5           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | denise   | CFA    | editingteacher |
      | dennis   | CFA    | student |
    And the following config values are set as admin:
      | displayoptions | 1 | block_coursefilesarchive |
    And I enable "coursefilesarchive" "block" plugin
    And I log in as "denise"
    And I am on "CourseFilesArchive" course homepage with editing mode on
    And I add the "Course files archive" block
    And I log out

  Scenario: Student cannot view course files archive
    When I log in as "dennis"
    And I am on "CourseFilesArchive" course homepage
    Then "Course files archive" "block" should exist

  Scenario: Teacher can view course files archive
    When I log in as "denise"
    And I am on "CourseFilesArchive" course homepage
    Then "Course files archive" "block" should not exist

@block @block_coursefilesarchive
Feature: Block visibility
  In order to have visibility of the block.
  As a teacher
  I can see the block
  As a student
  I cannot see the block

  Background:
    Given the following "users" exist:
      | username  | firstname     | lastname | email                |
      | denise    | Denise Emma   | Bug      | debug1@local.localhost |
      | dennis    | Dennis Edward | Bug      | debug2@local.localhost |
      | debbie    | Debbie Emily  | Bug      | debug3@local.localhost |
    And the following "categories" exist:
      | name               | category | idnumber |
      | Top                | 0        | TOP      |
      | CFA Category       | TOP      | CFACAT   |
      | Not CFA Category   | TOP      | NCFACAT  |
    And the following "courses" exist:
      | fullname               | shortname | category | format  | numsections |
      | CourseFilesArchive     | CFA       | CFACAT   | topics  | 5           |
      | NotCourseFilesArchive  | NCFA      | NCFACAT  | topics  | 5           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | denise   | CFA    | editingteacher |
      | debbie   | CFA    | teacher        |
      | dennis   | CFA    | student        |
      | denise   | NCFA   | editingteacher |
      | debbie   | NCFA   | teacher        |
      | dennis   | NCFA   | student        |
    And the following config values are set as admin:
      | config          | value  | plugin |
      | blockcategories | CFACAT | block_coursefilesarchive |
    And I enable "coursefilesarchive" "block" plugin
    And I log in as "denise"
    And I am on "CourseFilesArchive" course homepage with editing mode on
    And I add the "Course files archive" block
    And I log out

  Scenario: Student cannot view course files archive
    When I log in as "dennis"
    And I am on "CourseFilesArchive" course homepage
    Then "Course files archive" "block" should not exist

  Scenario: Teacher cannot view course files archive
    When I log in as "debbie"
    And I am on "CourseFilesArchive" course homepage
    Then "Course files archive" "block" should not exist

  Scenario: Editing teacher can view course files archive
    When I log in as "denise"
    And I am on "CourseFilesArchive" course homepage
    Then "Course files archive" "block" should exist

  Scenario: Editing teacher cannot add a course files archive block to a course in a category that is not permitted
    When I log in as "denise"
    And I am on "NotCourseFilesArchive" course homepage with editing mode on
    And I add the "Course files archive" block
    Then "Course files archive" "block" should not exist

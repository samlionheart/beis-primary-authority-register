@ci
Feature: Capture the state of a section in an entity; ie., whether or not an entity has been confirmed.

    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: Capture the state of a section in an entity
        Given I open the url "/dv/primary-authority-partnerships"
        Then the element "h1" contains the text "List of Partnerships for a Primary Authority"
        Then I expect that element "#view-partnership-status-table-column" is visible
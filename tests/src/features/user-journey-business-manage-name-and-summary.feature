@ci @journey3
Feature: As the (coordinated) Business User,
    I need to be able to see landing page for my co-ordinated Partnership,
    so that I can access the tasks required of me.

    Background:
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: Manage business name and summary
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible
        When I click on the button ".button-start"
        # PARTNERSHIPS DASHBOARD
        And I click on the link "ABCD Mart"
        # TERMS AND CONDITIONS SCREEN
        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        Then the element "h3" contains the text "Main contact at the Authority"
        When I click on the link "Review and confirm your business details"
        Then the element "#par-flow-transition-business-details" contains the text "About the business"
        And the element "#par-flow-transition-business-details" contains the text "Registered address"
        And the element "#par-flow-transition-business-details" contains the text "Legal Entities"
        And the element "#par-flow-transition-business-details" contains the text "Trading Names"
        When I click on the link "edit"
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-next"
        Then the element "#edit-about-business" contains the text "Change to the about business details section"
        When I click on the button "html.js body.js-enabled main#content div div#block-par-theme-content form#par-flow-transition-business-details.par-flow-transition-business-details div fieldset#edit-0.js-form-item.form-item.js-form-wrapper.form-wrapper.inline em.placeholder a.flow-link"
        And I add "Trading Name Change" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then the element "#par-flow-transition-business-details" contains the text "Trading Name Change"
        When I click on the link "add another trading name"
        And I add "Trading Name Add" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then the element "#par-flow-transition-business-details" contains the text "Trading Name Add"
@ci @journey5
Feature: Helpdesk As Primary Authority - Manage Addresses

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: Helpdesk As Primary Authority - Manage Addresses
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I am logged in as "par_helpdesk@example.com"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        When I click on the link "Dashboard"

        # PARTNERSHIP DETAILS

        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the button "td.views-field.views-field-authority-name a"
        Then I expect that element ".table-scroll-wrapper" contains the text "Review and confirm your partnership details"
        And I click on the link "Review and confirm your partnership details"
        And I click on the link "edit"
        And I add "test partnership info change" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-first-section" contains the text "test partnership info change"

        # CHANGE ADDRESSES

        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact a.flow-link"
        And I clear the inputfield "#edit-salutation"
        And I clear the inputfield "#edit-first-name"
        And I clear the inputfield "#edit-last-name"
        And I clear the inputfield "#edit-work-phone"
        And I clear the inputfield "#edit-mobile-phone"
        And I clear the inputfield "#edit-email"
        And I click on the button "#edit-next"
        When I add "Mr" to the inputfield "#edit-salutation"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "Animal" to the inputfield "#edit-first-name"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "the Muppet" to the inputfield "#edit-last-name"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "91723456789" to the inputfield "#edit-work-phone"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "par_authority_animal@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        Then I expect that element "#edit-authority-contacts" contains the text "Animal"
        And I expect that element "#edit-authority-contacts" contains the text "the Muppet"
        And I expect that element "#edit-authority-contacts" contains the text "par_authority_animal@example.com"
        And I expect that element "#edit-authority-contacts" contains the text "91723456789"
        And I expect that element "#edit-authority-contacts" contains the text "9777777777"
        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact-0 a.flow-link"
        And I add "Miss" to the inputfield "#edit-first-name"
        And I add "Piggy" to the inputfield "#edit-last-name"
        And I add "par_authority_piggy@example.com" to the inputfield "#edit-email"
        And I add "917234567899" to the inputfield "#edit-work-phone"
        And I add "97777777779" to the inputfield "#edit-mobile-phone"
        When I click on the button "#edit-next"
        Then I expect that element ".authority-alternative-contact-0" contains the text "Miss"
        Then I expect that element ".authority-alternative-contact-0" contains the text "Piggy"
        Then I expect that element ".authority-alternative-contact-0" contains the text "par_authority_piggy@example.com"
        Then I expect that element ".authority-alternative-contact-0" contains the text "917234567899"
        Then I expect that element ".authority-alternative-contact-0" contains the text "97777777779"
        When I click on the button "form#par-flow-transition-partnership-details-overview #edit-organisation-contacts a.flow-link"
        And I add "Fozzie" to the inputfield "#edit-first-name"
        And I add "Bear" to the inputfield "#edit-last-name"
        And I add "91723456789" to the inputfield "#edit-work-phone"
        And I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I add "par_business_fozzie@example.com" to the inputfield "#edit-email"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-organisation-contacts" contains the text "Fozzie"
        Then I expect that element "#edit-organisation-contacts" contains the text "Bear"
        And I expect that element "#edit-organisation-contacts" contains the text "par_business_fozzie@example.com"
        And I expect that element "#edit-organisation-contacts" contains the text "91723456789"
        And I expect that element "#edit-organisation-contacts" contains the text "9777777777"
        When I click on the button "form#par-flow-transition-partnership-details-overview .organisation-alternative-contacts-1 a.flow-link"
        And I add "917234567899" to the inputfield "#edit-work-phone"
        And I add "97777777779" to the inputfield "#edit-mobile-phone"
        And I add "Pepe" to the inputfield "#edit-first-name"
        And I add "the King Prawn" to the inputfield "#edit-last-name"
        And I add "par_business_pepe@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        Then I expect that element ".organisation-alternative-contacts-1" contains the text "par_business_pepe@example.com"
        And I expect that element ".organisation-alternative-contacts-1" contains the text "Pepe"
        And I expect that element ".organisation-alternative-contacts-1" contains the text "the King Prawn"
        And I expect that element ".organisation-alternative-contacts-1" contains the text "917234567899"
        And I expect that element ".organisation-alternative-contacts-1" contains the text "97777777779"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"
        Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Authority"
        When I click on the link "Go back to your partnerships"
        Then I expect that element "h1" contains the text "List of Partnerships"

        # CSV CHECK
        When I click on the link "Dashboard"
        And I click on the link "Download as CSV"
        And I click on the link "Log out"
@ci @journey5
Feature: Helpdesk - Dashboard

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: User Journey 1 - Send invitiation to business
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then the element "#block-par-theme-account-menu" contains the text "Log out"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        When I click on the link "Dashboard"
#        When I open the url "/dv/rd-dashboard"

        # PARTNERSHIP DETAILS

        Then the element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the link "List of tasks"
        Then the element ".table-scroll-wrapper" contains the text "Review and confirm your partnership details"
        And I click on the link "Review and confirm your partnership details"
        And I click on the link "edit"
        And I add "test partnership info change" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"
        Then the element "#edit-first-section" contains the text "test partnership info change"

        # CHANGE ADDRESSES

        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact a.flow-link"
        And I clear the inputfield "#edit-salutation"
        And I clear the inputfield "#edit-first-name"
        And I clear the inputfield "#edit-last-name"
        And I clear the inputfield "#edit-work-phone"
        And I clear the inputfield "#edit-mobile-phone"
        And I clear the inputfield "#edit-email"
        And I click on the button "#edit-next"
        Then I expect that element "input:focus" is visible
        When I add "Mr" to the inputfield "#edit-salutation"
        And I click on the button "#edit-next"
        Then I expect that element "input:focus" is visible
        When I add "Animal" to the inputfield "#edit-first-name"
        And I click on the button "#edit-next"
        Then I expect that element "input:focus" is visible
        When I add "the Muppet" to the inputfield "#edit-last-name"
        And I click on the button "#edit-next"
        Then I expect that element "input:focus" is visible
        When I add "91723456789" to the inputfield "#edit-work-phone"
        And I click on the button "#edit-next"
        Then I expect that element "input:focus" is visible
        When I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I click on the button "#edit-next"
        Then I expect that element "input:focus" is visible
        When I add "par_authority_animal@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        Then the element "#edit-authority-contacts" contains the text "Animal"
        And the element "#edit-authority-contacts" contains the text "the Muppet"
        And the element "#edit-authority-contacts" contains the text "par_authority_animal@example.com"
        And the element "#edit-authority-contacts" contains the text "91723456789"
        And the element "#edit-authority-contacts" contains the text "9777777777"
        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact-0 a.flow-link"
        And I add "Miss" to the inputfield "#edit-first-name"
        And I add "Piggy" to the inputfield "#edit-last-name"
        And I add "par_authority_piggy@example.com" to the inputfield "#edit-email"
        And I add "917234567899" to the inputfield "#edit-work-phone"
        And I add "97777777779" to the inputfield "#edit-mobile-phone"
        When I click on the button "#edit-next"
        Then the element ".authority-alternative-contact-0" contains the text "Miss"
        Then the element ".authority-alternative-contact-0" contains the text "Piggy"
        Then the element ".authority-alternative-contact-0" contains the text "par_authority_piggy@example.com"
        Then the element ".authority-alternative-contact-0" contains the text "917234567899"
        Then the element ".authority-alternative-contact-0" contains the text "97777777779"
        When I click on the button "form#par-flow-transition-partnership-details-overview #edit-organisation-contacts a.flow-link"
        And I add "Fozzie" to the inputfield "#edit-first-name"
        And I add "Bear" to the inputfield "#edit-last-name"
        And I add "91723456789" to the inputfield "#edit-work-phone"
        And I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I add "par_business_fozzie@example.com" to the inputfield "#edit-email"
        And I click on the button "#edit-next"
        Then the element "#edit-organisation-contacts" contains the text "Fozzie"
        Then the element "#edit-organisation-contacts" contains the text "Bear"
        And the element "#edit-organisation-contacts" contains the text "par_business_fozzie@example.com"
        And the element "#edit-organisation-contacts" contains the text "91723456789"
        And the element "#edit-organisation-contacts" contains the text "9777777777"
        When I click on the button "form#par-flow-transition-partnership-details-overview .organisation-alternative-contacts-1 a.flow-link"
        And I add "917234567899" to the inputfield "#edit-work-phone"
        And I add "97777777779" to the inputfield "#edit-mobile-phone"
        And I add "Pepe" to the inputfield "#edit-first-name"
        And I add "the King Prawn" to the inputfield "#edit-last-name"
        And I add "par_business_pepe@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        Then the element ".organisation-alternative-contacts-1" contains the text "par_business_pepe@example.com"
        And the element ".organisation-alternative-contacts-1" contains the text "Pepe"
        And the element ".organisation-alternative-contacts-1" contains the text "the King Prawn"
        And the element ".organisation-alternative-contacts-1" contains the text "917234567899"
        And the element ".organisation-alternative-contacts-1" contains the text "97777777779"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"
        Then the element "#block-par-theme-content" contains the text "Confirmed by the Authority"
        When I click on the link "Go back to your partnerships"
        Then the element "h1" contains the text "List of Partnerships"

        # CSV CHECK
        When I click on the link "Dashboard"
        And I click on the button "td.views-field.views-field-nothing-1 a"
        Then the element ".table-scroll-wrapper" contains the text "Review and confirm your business details"
        When I open the url "/dv/rd-dashboard"
        And I click on the link "Download as CSV"
        And I click on the link "Log out"





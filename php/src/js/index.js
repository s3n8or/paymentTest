$(document).ready(function() {
    const $loginForm = $('#loginForm')
    const $signUpForm = $('#signUpForm')
    const $loginGroupButton = $('#loginGroupButton')
    const $signupGroupButton = $('#signupGroupButton')
    const $loginSection = $('#loginSection')
    const $signupSection = $('#signupSection')
    const $logoutButton = $('#logoutButton')
    const $signinButton = $('#signinButton')
    const $bannerHeading = $('#bannerHeading')
    const $bannerMessage = $('#bannerMessage')
    const $closeOffcanvasButton = $('#closeOffcanvasButton')
    const $errorList = $('#errorList')
    const $loginTitle = $('#loginTitle')
    const $signupTitle = $('#signupTitle')

    updateUI()
    // Hides the signup section so we start with the sign in form
    $loginGroupButton.click()

    $loginGroupButton.on('click', function(event) {
        $signupGroupButton.prop('disabled', false)
        $(this).prop('disabled', true)

        $loginSection.show()
        $loginTitle.show()
        $signupSection.hide()
        $signupTitle.hide()
    })

    $signupGroupButton.on('click', function(event) {
        $loginGroupButton.prop('disabled', false)
        $(this).prop('disabled', true)

        $loginSection.hide()
        $loginTitle.hide()
        $signupSection.show()
        $signupTitle.show()
    })

    $signUpForm.on('submit', function(event) {
        event.preventDefault()
        $errorList.empty()

        const $usernameSignupField = $signUpForm.find('input[name="usernameSignup"]')
        const $emailSignUpField = $signUpForm.find('input[name="emailSignUp"]')
        const $passwordSignUpField = $signUpForm.find('input[name="passwordSignUp"]')
        const $rePasswordSignUpField = $signUpForm.find('input[name="rePasswordSignUp"]')

        const username = $usernameSignupField.val()
        const email = $emailSignUpField.val()
        const password = $passwordSignUpField.val()
        const rePassword = $rePasswordSignUpField.val()

        if (username === '') {
            $errorList.append(getErrorBlock('You must enter a username'))
        }
        if (email === '') {
            $errorList.append(getErrorBlock('You must enter an email'))
        }
        if (password === '') {
            $errorList.append(getErrorBlock('You must enter a password'))
        }
        // I would include a couple other checks, like email validation and making sure the password matches or standards (length, including special charaters, whatnot), but it was slowing down development making me constantly have to include all that.
        if (rePassword === '') {
            $errorList.append(getErrorBlock('You must re-enter your password'))
        } else if (password != rePassword) {
            $errorList.append(getErrorBlock('Your password and re-entered password must match'))
        }

        const url = $signUpForm.attr('action')

        $.post(url, { username, email, password })
            .done(function(data) {
                // A workaround because setting the Content-Type wasn't working
                const user = JSON.parse(data)
                isLoggedIn = true
                userName = user.username

                // Clean up the sign in form
                $usernameSignupField.val('')
                $emailSignUpField.val('')
                $passwordSignUpField.val('')
                $rePasswordSignUpField.val('') 

                updateUI()
                $signupSection.hide()
                $closeOffcanvasButton.click()
            })
            .fail(function(error) {
                const e = JSON.parse(error.responseText)
                let message

                if (e.code === 400) {
                    message = 'Missing data, make sure username, email, and password are sent'
                } else if (e.code === 403) {
                    message = 'User already exists'
                } else {
                    message = 'There was a problem, contact support'
                }

                $errorList.append(getErrorBlock(message))
            })
    })

    $loginForm.on('submit', function(event) {
        event.preventDefault()
        $errorList.empty()

        const $emailField = $loginForm.find('input[name="email"]')
        const $passwordField = $loginForm.find('input[name="password"]')

        const email = $emailField.val()
        const password = $passwordField.val()
        const url = $loginForm.attr('action')

        $.post(url, { email, password })
            .done(function(data) {
                // A workaround because setting the Content-Type wasn't working
                const user = JSON.parse(data)
                isLoggedIn = true
                userName = user.username
                
                // Clean up the sign in form
                $emailField.val('')
                $passwordField.val('')

                updateUI()
                $closeOffcanvasButton.click()
            })
            .fail(function(error) {
                const e = JSON.parse(error.responseText)
                let message

                if (e.code === 404) {
                    message = 'User not found, try another email'
                } else if (e.code === 401) {
                    message = 'Wrong credentials, please try again'
                } else {
                    message = 'There was a problem, contact support'
                }

                $errorList.append(getErrorBlock(message))
            })
    })

    /**
     * Sends an AJAX request to the logout endpoint and then calls updateUI
     */
    $logoutButton.on('click', function() {
        $errorList.empty()

        $.post('logout.php')
            .done(function(data) {
                isLoggedIn = false
                userName = ''

                updateUI()
            })
            .fail(function(error) {
                $errorList.append(getErrorBlock('There was a problem logging out'))
            })
    })

    /**
     * Method to manage the UI for the logged in/out states
     */
    function updateUI() {
        // handle shwoing/hiding logout, signin, and banner message
        if (isLoggedIn) {
            $logoutButton.show()
            $signinButton.hide()
            $bannerMessage.hide()
        } else {
            $logoutButton.hide()
            $signinButton.show()
            $bannerMessage.show()
        }
        
        const name = userName ? ` ${userName}` : ''
        const welcomeMessage = `Welcome${name}!`
        
        // update title with name
        $(document).prop('title', welcomeMessage)

        // update banner with name
        $bannerHeading.text(welcomeMessage)

        // remove errors
        $errorList.empty()
    }

    /**
     * Helper function to create error div element to go into error list
     * 
     * @param {string} errorMessage
     * 
     * @returns {jQuery}
     */
    function getErrorBlock(errorMessage) {
        return $('<div class="mb-3 mt-3 bg-danger text-white">' + errorMessage + '</div>')
    }
})

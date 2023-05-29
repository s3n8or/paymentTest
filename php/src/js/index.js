$(document).ready(function() {
    const $loginForm = $('#loginForm')
    const $loginGroupButton = $('#loginGroupButton')
    const $signupGroupButton = $('#signupGroupButton')
    const $loginSection = $('#loginSection')
    const $signupSection = $('#signupSection')
    const $logoutButton = $('#logoutButton')
    const $signinButton = $('#signinButton')
    const $bannerHeading = $('#bannerHeading')
    const $bannerMessage = $('#bannerMessage')
    const $closeOffcanvasButton = $('#closeOffcanvasButton')

    updateUI()
    $signupSection.hide()

    $loginGroupButton.on('click', function(event) {
        $signupGroupButton.prop('disabled', false)
        $(this).prop('disabled', true)
        $loginSection.show()
        $signupSection.hide()
    })

    $signupGroupButton.on('click', function(event) {
        $loginGroupButton.prop('disabled', false)
        $(this).prop('disabled', true)
        $signupSection.show()
        $loginSection.hide()
    })

    $loginForm.on('submit', function(event) {
        event.preventDefault()

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
                console.log('error = ', error)
            })
    })

    /**
     * Sends an AJAX request to the logout endpoint and then calls updateUI
     */
    $logoutButton.on('click', function() {
        $.post('logout.php')
            .done(function(data) {
                isLoggedIn = false
                userName = ''

                updateUI()
            })
            .fail(function(error) {
                console.log('error = ', error)
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
    }
})

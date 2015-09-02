/**
 * Created by Al on 07/08/2015.
 */
// ################################
//          FORM VALIDATION
//  Used for JS on submit validation
// ################################

// Signup form validation
$('.ui.signup.form').form({
    username: {
        identifier: 'username',
        rules: [
            {
                type: 'empty',
                prompt: 'Please enter a user name'
            }
        ]
    },
    password: {
        identifier: 'password',
        rules: [
            {
                type: 'empty',
                prompt: 'Please enter a password'
            }
        ]
    },
    repeatPassword: {
        identifier: 'repeatPassword',
        rules: [
            {
                type: 'empty',
                prompt: 'Please confirm your password'
            }
        ]
    },
    email: {
        identifier: 'email',
        rules: [
            {
                type: 'empty',
                prompt: 'Please enter your email address'
            }
        ]
    },
    validEmail: {
        identifier: 'email',
        rules: [
            {
                type: 'email',
                prompt: 'Email address not valid'
            }
        ]
    },
    repeatEmail: {
        identifier: 'repeatEmail',
        rules: [
            {
                type: 'empty',
                prompt: 'Please confirm your email address'
            }
        ]
    }
});


// Signup form validation
$('.ui.user-edit.form').form({
    password: {
        identifier: 'password',
        rules: [
            {
                type: 'empty',
                prompt: 'Please enter a password'
            }
        ]
    },
    repeatPassword: {
        identifier: 'repeatPassword',
        rules: [
            {
                type: 'empty',
                prompt: 'Please confirm your password'
            }
        ]
    },
    email: {
        identifier: 'email',
        rules: [
            {
                type: 'empty',
                prompt: 'Please enter your email address'
            }
        ]
    },
    validEmail: {
        identifier: 'email',
        rules: [
            {
                type: 'email',
                prompt: 'Email address not valid'
            }
        ]
    }
});



// Contact form validation
$('.ui.contact.form').form({
    email: {
        identifier: 'email',
        rules: [
            {
                type: 'email',
                prompt: 'Please enter your email address'
            }
        ]
    },
    repeatEmail: {
        identifier: 'repeatEmail',
        rules: [
            {
                type: 'empty',
                prompt: 'Please confirm your email address'
            }
        ]
    },
    fullName: {
        identifier: 'fullName',
        rules: [
            {
                type: 'empty',
                prompt: 'Please enter your full name'
            }
        ]
    },
    content: {
        identifier: 'content',
        rules: [
            {
                type: 'length[10]',
                prompt: 'Please enter a message longer than 6 characters'
            }
        ]
    }
});

// password reset form
$('.ui.pass-reset.form')
    .form({
        firstName: {
            identifier: 'pass',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Enter a new password'
                }
            ]
        },
        lastName: {
            identifier: 'pass_conf',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Please confirm your new password'
                }
            ]
        }
    },
    {
        inline: true,
        on: 'blur'
    }
);
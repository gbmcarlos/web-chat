/**
 * Created by gbmcarlos on 9/30/17.
 */

'use strict';

var LoginController = {

    elements: {
        submitButton: $('#loginSubmitButton'),
        usernameInput: $('#usernameInput')
    },

    init: function() {
        this.setEvents();
    },

    setEvents: function() {
        this.elements.submitButton.on('click', this.onSubmitClick.bind(this));
    },

    onSubmitClick: function() {

        var username = this.elements.usernameInput.val();

        var validUsername = this.validateUsername(username);

        if (validUsername) {

            this.login(username, this.loginCallback.bind(this));

        } else {
            console.log('TODO: show invalid username error');
            //TODO: show invalid username error
        }

    },

    validateUsername: function(username) {

        return !!username;

    },

    login: function(username, callback) {

        $.post(
            '/api/login/' + username,
            null,
            callback
        );

    },

    loginCallback: function(response) {

        if (response.success) {

            location.href = response.data.redirectUrl;

        } else {
            console.log('TODO: show error');
            //TODO: show error
        }

    }

};

$(document).ready(LoginController.init.bind(LoginController));
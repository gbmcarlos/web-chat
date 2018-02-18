/**
 * Created by gbmcarlos on 9/30/17.
 */

'use strict';

var DashboardController = {

    elements: {
        newChatButton: $('#newChatButton'),
        usernameInput: $('#usernameInput')
    },

    init: function() {
        this.setEvents();
        this.username1 = this.elements.newChatButton.attr('data-username1');
    },

    setEvents: function() {
        this.elements.newChatButton.on('click', this.onSubmitClick.bind(this));
    },

    onSubmitClick: function() {

        var username = this.elements.usernameInput.val();

        var validUsername = this.validateUsername(username);

        if (validUsername) {

            this.newChat(username, this.newChatCallback.bind(this));

        } else {
            console.log('TODO: show invalid username error');
            //TODO: show invalid username error
        }

    },

    validateUsername: function(username) {

        return !!username;

    },

    newChat: function(username, callback) {

        $.post(
            '/api/' + this.username1 + '/create_new_chat/' + username,
            null,
            callback
        );

    },

    newChatCallback: function(response) {

        if (response.success) {

            location.href = response.data.redirectUrl;

        } else {
            console.log('TODO: show error');
            //TODO: show error
        }

    }

};

$(document).ready(DashboardController.init.bind(DashboardController));
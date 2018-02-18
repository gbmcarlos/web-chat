/**
 * Created by gbmcarlos on 9/30/17.
 */

'use strict';

var ChatController = {

    elements: {
        submitButton: $('#submitButton'),
        messageInput: $('#messageInput'),
        sentMessageTemplate: $('#sentMessageTemplate'),
        receivedMessageTemplate: $('#receivedMessageTemplate'),
        messagesList: $('#messageList'),
        loadingIcon: $('#loadingIcon')
    },

    init: function() {
        this.setEvents();
        this.username1 = this.elements.submitButton.attr('data-username1');
        this.username2 = this.elements.submitButton.attr('data-username2');
        this.userId = this.elements.submitButton.attr('data-userid');
        this.setAutoRefresh();
    },

    setEvents: function() {
        this.elements.submitButton.on('click', this.onSubmitClick.bind(this));
    },

    setAutoRefresh: function() {

        this.autoRefresh = setInterval(this.loadLastMessages.bind(this), 1000*10);

    },

    onSubmitClick: function() {

        var message = this.elements.messageInput.val();

        var validUsername = this.validateMessage(message);

        if (validUsername) {

            this.send(message, this.sendCallback.bind(this));

        } else {
            console.log('TODO: show invalid username error');
            //TODO: show invalid username error
        }

    },

    validateMessage: function(message) {

        return !!message;

    },

    send: function(message, callback) {

        this.elements.messageInput.prop('disabled', true);
        this.elements.submitButton.prop('disabled', true);
        this.elements.loadingIcon.removeClass('hidden');

        $.post(
            '/api/' + this.username1 + '/create_new_message/' + this.username2,
            {
                text: message
            },
            callback
        );

    },

    sendCallback: function(response) {

        if (response.success) {

            this.insertNewMessage(response.data, true);

            this.elements.messageInput.prop('disabled', false);
            this.elements.submitButton.prop('disabled', false);
            this.elements.loadingIcon.addClass('hidden');
            this.elements.messageInput.val('');

        } else {
            console.log('TODO: show error');
            //TODO: show error
        }

    },

    insertNewMessage: function(message, async) {

        message.createdAt = this.formatMessageDate(message.createdAt);

        message.async = async;

        var sent = message.senderId == this.userId;

        var template = sent ? this.elements.sentMessageTemplate.text() : this.elements.receivedMessageTemplate.text();

        var result = this.parseTemplate(template, message);

        this.elements.messagesList.append($(result));

    },

    loadLastMessages: function() {

        var lastMessage = this.elements.messagesList.children(":not([data-async='true'])").last(); // load the last message that has not been sent asynchronously

        var lastId = lastMessage.attr('data-id') || 0;

        $.get(
            '/api/' + this.username1 + '/get_chat_messages_since/' + this.username2 + '/' + lastId,
            null,
            function(response){
                if (response.success && response.data.length) {
                    this.loadLastMessagesCallback(response.data, lastId);
                }
            }.bind(this)
        );

    },

    loadLastMessagesCallback: function(messages, lastId) {

        var asyncSentMessages = lastId ? this.elements.messagesList.children("[data-id=" + lastId + "]").nextAll() : this.elements.messagesList.children();

        asyncSentMessages.remove();

        for (var i = 0; i < messages.length; i++) {
            this.insertNewMessage(messages[i], false);
        }

    },

    parseTemplate: function(template, data) {

        var variablePlaceholder = /\{\S+\}/g;

        var rendered = template.replace(variablePlaceholder, function(match){

            var variableName = match.substr(1, match.length-2);

            var value = data[variableName];

            return value;

        });

        return rendered;

    },

    formatMessageDate: function(timestamp) {

        var date = new Date(timestamp*1000);

        return date.getDate() + '/' + date.getMonth()+1 + '/' + date.getFullYear() + ' ' + date.getUTCHours() + ':' + date.getMinutes();

    }

};

$(document).ready(ChatController.init.bind(ChatController));
$(document).ready(function() {
    loadChatbot();

    // Button Events
    $('#send-btn').click(function() {
        sendMessage();
    });

    $('#msg').keypress(function(e) {
        if (e.which === 13) {
            sendMessage();
        }
    });

    // Functions
    function loadChatbot() {
        $.ajax({
            url: 'app/api.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                getChatHistory: true
            }),
            success: function(response) {
                if (response.status === 'success') {
                    displayChatHistory(response.data);
                } else {
                    console.error('Error:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function displayChatHistory(data) {
        $('#chat').html('');
        data.forEach(function(item) {
            $('#chat').append('<div class="message"><b>' + item.role + ':</b> ' + item.content + '</div>');
        });
        $('#chat').scrollTop($('#chat')[0].scrollHeight);
    }

    function sendMessage() {
        var message = $('#msg').val().trim();

        if (!message) {
            return;
        }

        $.ajax({
            url: 'app/api.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                sendMessage: true,
                message: message
            }),
            success: function(response) {
                if (response.status === 'success') {
                    loadChatbot();
                    $('#chat').append('<div class="message"><b>Sir Dhine:</b> ' + response.data.reply + '</div>');
                    $('#chat').scrollTop($('#chat')[0].scrollHeight);
                } else {
                    console.error('Error:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            },
            complete: function() {
                $('#msg').val('');
                $('#msg').focus();
            }
        });
    }
});
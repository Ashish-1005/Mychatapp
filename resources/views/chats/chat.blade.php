<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat app</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->user()->id }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

</head>

<body>
    {{-- <div class="container">
        <div class="header">Chat App</div> --}}



        {{-- <div class="chat-messages" id="messages">
            @foreach ($chats as $chat)
                <p>{{ $chat->body }}</p>
            @endforeach
        </div> --}}
         <!-- msg-bottom section -->

        {{-- <div class="chat-input">
            <div class="form-wrapper">
                <form id="msg_form">
                    @csrf
                    <input type="hidden" name="from_id" value="{{ auth()->user()->id }}">
                    <input type="text" id="sender_name" value="{{ auth()->user()->name }}" readonly>
                    <textarea id="body" name="body" placeholder="Type a message..." cols="30" rows="3" style="border: none;"></textarea>
                    <select name="to_id" id="to_id" display:none ;>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <button id="sendMessage">Send</button>
                </form>
            </div>
        </div> --}}

        <div class="container">
            <div class="msg-header">
                <h2 style="text-align: center;">Chat App</h2>
                <div class="container1">
                    <div class="active">
                        <p>User Name:- {{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Chat inbox  -->
            <div class="chat-page">
                <div class="msg-inbox">
                    <div class="chats">
                        <div class="msg-page" id="messages">
                            @foreach ($chats as $chat)
                                <div class="{{ $chat->from_id == auth()->user()->id ? 'outgoing-chats' : 'received-chats' }}">

                                    <div class="{{ $chat->from_id == auth()->user()->id ? 'outgoing-msg' : 'received-msg' }}">
                                        <div class="{{ $chat->from_id == auth()->user()->id ? 'outgoing-chats-msg' : 'received-msg-inbox' }}">

                                            {{-- <div class="{{ $chat->from_id == auth()->user()->id ? 'outgoing-chats-img' : 'received-chats-img' }}">
                                                <img src="{{ $chat->from_id == auth()->user()->id ? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=50' : 'https://ui-avatars.com/api/?name=Unknown&size=50' }}" alt="User Image">
                                            </div> --}}
                                         <p>{{ $chat->body }}</p>

                                        <span class="time">{{ $chat->created_at->format('H:i A | F j') }}</span><br>
                                          @if ($chat->from_id == auth()->user()->id)
                                        <button onclick="deleteMessage({{ $chat->id }})" class="delete-btn">Delete</button><br><br>
                                        @endif
                                     </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="msg-bottom">
            <div class="input-group">
              <div class="chat-input">
                <div class="form-wrapper">
                  <form id="msg_form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="from_id" value="{{ auth()->user()->id }}">
                    <textarea id="body" name="body" placeholder="Type a message..." cols="30" rows="3" style="border: none;"></textarea>
                    <select name="to_id" id="to_id" display:none ;>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <input type="file" name="file" id="file">
                    <button id="sendMessage" type="submit">Send</button>
                </form>
            </div>
        </div>


        <div class="notifications">
            <h3>Notifications</h3>
            <ul>
                @foreach ($messagenotification as $notification)
                    <li>{{ $notification->message }}</li>
                @endforeach
           </ul>
       </div>



<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
    function deleteMessage(id) {
        if (confirm('Are you sure you want to delete this message?')) {
            $.ajax({
                url: '/chat/delete/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id
                },
                success: function(response) {
                    alert('Message deleted successfully');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.statusText);
                }
            });
        }
    }
</script>
<script>
    $(document).ready(function() {
        // Request permission for browser notifications
        if ("Notification" in window) {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    console.log("Notification permission granted.");
                } else {
                    console.log("Notification permission denied.");
                }
            });
        }

        // Send message function
        $('#msg_form').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('body', $("#body").val());
            formData.append('to_id', $("#to_id").val());
            formData.append('file', $('#file')[0].files[0]);


            $.ajax({
                url: "{{ route('chat.send') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
        // Construct the message HTML
        var messageHtml = '<div class="' + (data.from_id == {{ auth()->user()->id }} ? 'outgoing-chats' : 'received-chats') + '">' +
                '<div class="' + (data.from_id == {{ auth()->user()->id }} ? 'outgoing-msg' : 'received-msg') + '">' +
                '<div class="' + (data.from_id == {{ auth()->user()->id }} ? 'outgoing-chats-msg' : 'received-msg-inbox') + '">' +
                '<p>' + data.message + '</p><br>' +
                '<span class="time">' + data.created_at + '</span><br>';
                if (data.from_id == {{ auth()->user()->id }}) {
    messageHtml += '<button onclick="deleteMessage(' + data.id + ')" class="delete-btn">Delete</button><br>';
}

messageHtml += '</div>' +
                '</div>' +
                '</div>';

            // Append the message HTML to the chat container
            $('#messages').append(messageHtml);

            // Clear the message input
            $('#body').val('');

                    // $('#messages').append('<p>' + data.message + '</p>');
                    // $('#body').val('');

                    // Show browser notification
                    if (Notification.permission === "granted") {
                        new Notification("New Message", {
                            body: data.message
                        });
                    }

                    // Update notifications section
                    $('.notifications ul').append('<li>' + data.message + '</li>');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });


    });

</script>


</body>
</html>


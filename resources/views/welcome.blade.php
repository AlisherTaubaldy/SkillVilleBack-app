<!DOCTYPE html>
<html>
<head>
    <title>Laravel Jitsi Meet Integration</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #meet {
            width: 100%;
            height: 80vh;
            border: 1px solid #ddd;
            margin-top: 10px;
        }
        #controls {
            margin: 20px 0;
        }
    </style>
</head>
<body>
<img src="{{ asset('storage/images/7waiCJncieBldcaPSLL66B6NOtKtpYd96dzXijxv.png') }}" alt="" title="">

<img src="{{ url('storage/images/7waiCJncieBldcaPSLL66B6NOtKtpYd96dzXijxv.png') }}" alt="" title="">

<div id="controls">
    <form action="{{ route('create-room') }}" method="POST">
        @csrf
        <input type="text" name="roomName" placeholder="Enter room name">
        <button type="submit">Create Room</button>
    </form>
</div>
<div id="meet"></div>
</body>
</html>

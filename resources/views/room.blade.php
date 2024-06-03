<!DOCTYPE html>
<html>
<head>
    <title>Laravel Jitsi Meet Room</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #meet {
            width: 100%;
            height: 80vh;
        }
        #room-link {
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
<div id="room-link">
    <p>Share this link to join the meeting: <a href="{{ $roomLink }}" target="_blank">{{ $roomLink }}</a></p>
</div>
<div id="meet"></div>
<script src="https://meet.jit.si/external_api.js"></script>
<script>
    const domain = "meet.jit.si";
    const options = {
        roomName: "{{ $roomName }}",
        width: "100%",
        height: "100%",
        parentNode: document.querySelector('#meet')
    };
    const api = new JitsiMeetExternalAPI(domain, options);
</script>
</body>
</html>

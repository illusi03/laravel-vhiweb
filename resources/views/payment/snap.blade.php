<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="{{ $urlSnapJs }}" data-client-key="{{ $clientKey }}"></script>
</head>

<body>
    <script type="text/javascript">
        // For example trigger on button clicked, or any time you need
        document.addEventListener("DOMContentLoaded", function (event) {
            window.snap.pay(`{{ $snapToken }}`, {
                onSuccess: function (result) {
                    /* You may add your own implementation here */
                    // Success
                    location.replace(`{{ $successUrl }}`);
                },
                onPending: function (result) {
                    /* You may add your own implementation here */
                    // Waiting
                    location.replace(`{{ $pendingUrl }}`);
                },
                onError: function (result) {
                    /* You may add your own implementation here */
                    // Failed
                    location.replace(`{{ $errorUrl }}`);
                },
                onClose: function () {
                    /* You may add your own implementation here */
                    location.replace(`{{ $closeUrl }}`);
                }
            })
        });
    </script>
</body>

</html>
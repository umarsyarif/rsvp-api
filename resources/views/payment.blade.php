<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
</script>
<script>
    snap.pay('{{ $snapToken }}', {
        // Optional
        onSuccess: function(result) {
            /* You may add your own js here, this is just example */
            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
            Android.postMessage('close');
            Print.postMessage('close');
            console.log(result)
        },
        // Optional
        onPending: function(result) {
            /* You may add your own js here, this is just example */
            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
            console.log(result)
        },
        // Optional
        onError: function(result) {
            /* You may add your own js here, this is just example */
            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
            console.log(result)
        },
        onClose: function() {
            Android.postMessage('close');
            Print.postMessage('close');
        }
    });
</script>

</html>
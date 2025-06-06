<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
    @page {
        size: 8.5in 11in;
        margin: 0.5in;
        margin-top: 0.3in;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        position: relative;
    }

    .address-block {
        margin-bottom: 0.8in;
    }

    .fold-line-container {
        position: absolute;
        top: 3.25in;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 10px;
    }

    .fold-line-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        border-top: 1px dotted black;
        z-index: 1;
    }

    .fold-line-text {
        background: white;
        padding: 0 10px;
        position: relative;
        z-index: 2;
        display: inline-block;
    }

    .fold-line.right {
        right: 0;
    }
    </style>
</head>

<body>
    <div class="address-block from">
        <strong>{{ $fromName }}</strong><br>
        {{ $fromAddress1 }}<br>
        @if($fromAddress2)
        {{ $fromAddress2 }}<br>
        @endif
        @if($fromCity && $fromState && $fromZip)
        {{ $fromCity }}, {{ $fromState }} {{ $fromZip }}
        @endif
    </div>

    <div class="address-block to">
        <strong>{{ $toName }}</strong><br>
        {{ $toAddress1 }}<br>
        @if($toAddress2)
        {{ $toAddress2 }}<br>
        @endif
        {{ $toCity }}, {{ $toState }} {{ $toZip }}
    </div>

    <!-- Fold line at bottom third mark -->
    <div class="fold-line-container">
        <span class="fold-line-text">Fold Line</span>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice for Order #{{ $order->id }}</title>
</head>
<body>
<h1>Invoice for Order #{{ $order->id }}</h1>

<p>Dear {{  $order->user->name  }},</p>

<p>Thank you for your order. Here are the details:</p>

<h2>Order invoice</h2>

<p> invoice details is {{  $invoice  }},</p>

<p>If you have any questions about this invoice, please don't hesitate to contact us.</p>

<p>Thank you for your business!</p>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Confirmation</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .header {
      background-color: #4CAF50;
      padding: 10px 20px;
      text-align: center;
      color: #ffffff;
    }

    .header h1 {
      margin: 0;
      font-size: 24px;
    }

    .content {
      padding: 20px;
      color: #333333;
    }

    .content h2 {
      font-size: 22px;
      margin-bottom: 20px;
      color: #4CAF50;
    }

    .details {
      margin-bottom: 30px;
    }

    .details p {
      margin: 5px 0;
    }

    .details strong {
      color: #333333;
    }

    .btn {
      display: inline-block;
      background-color: #4CAF50;
      color: #ffffff;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
    }

    .footer {
      text-align: center;
      padding: 20px;
      font-size: 12px;
      color: #888888;
    }

    .footer p {
      margin: 0;
    }

    @media only screen and (max-width: 600px) {
      .container {
        padding: 10px;
      }

      .content h2 {
        font-size: 20px;
      }
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="header">
      <h1>Booking Confirmation</h1>
    </div>

    <div class="content">
      <h2>Thank You, {{ $booking->user->name }}!</h2>
      <p>Your booking for the event <strong>{{ $event->title }}</strong> has been confirmed. Below are the details of
        your booking:</p>

      <div class="details">
        <p><strong>Event:</strong> {{ $event->title }}</p>
        <p><strong>Event Date:</strong> {{ $event->start_date->format('F j, Y') }}</p>
        <p><strong>Quantity:</strong> {{ $booking->quantity }}</p>
        <p><strong>Total Price:</strong> ${{ number_format($event->price * $booking->quantity, 2) }}</p>
      </div>

      <a href="#" class="btn">View Your Booking</a>

      <p style="margin-top: 20px;">We look forward to seeing you at the event!</p>
    </div>

    <div class="footer">
      <p>For any queries, feel free to <a href="#" style="color: #4CAF50;">contact us</a>.</p>
      <p>&copy; {{ now()->year }} Event Company. All Rights Reserved.</p>
    </div>
  </div>

</body>

</html>

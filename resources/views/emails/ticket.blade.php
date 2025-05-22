<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>ZURIW25 Conference Ticket</title>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }
            .footer {
                width: 100% !important;
            }
        }
        
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #f8fafc;
            color: #3d4852;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }
        
        .wrapper {
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
        }
        
        .content {
            margin: 0;
            padding: 0;
            width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
        }
        
        .header {
            padding: 25px 0;
            text-align: center;
        }
        
        .header a {
            color: #3d4852;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
        }
        
        .body {
            background-color: #ffffff;
            border-bottom: 1px solid #edeff2;
            border-top: 1px solid #edeff2;
            margin: 0;
            padding: 0;
            width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
        }
        
        .inner-body {
            background-color: #ffffff;
            border: 1px solid #e8e5ef;
            border-radius: 2px;
            box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
            margin: 0 auto;
            padding: 0;
            width: 600px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 600px;
        }
        
        .footer {
            margin: 0 auto;
            padding: 0;
            text-align: center;
            width: 570px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 570px;
        }
        
        .footer p {
            color: #b0adc5;
            font-size: 12px;
            text-align: center;
        }
        
        .footer a {
            color: #b0adc5;
            text-decoration: underline;
        }
        
        h1 {
            color: #3d4852;
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }
        
        p {
            font-size: 16px;
            line-height: 1.5em;
            margin-top: 0;
            text-align: left;
        }
        
        table.content-cell {
            padding: 35px;
        }
    </style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="header">
                            <a href="{{ url('/') }}">
                                ZURIW25 Conference
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="content-cell">
                                        <h1>Hello {{ $ticket->participant->full_name }},</h1>
                                        <p>Thank you for registering for the ZURIW25 Conference. Your ticket is attached to this email.</p>
                                        <p>Ticket Summary:</p>
                                        <ul>
                                            <li>Ticket Number: <strong>{{ $ticket->ticket_number }}</strong></li>
                                            <li>Role: <strong>{{ $ticket->participant->role }}</strong></li>
                                            <li>Valid Dates:</li>
                                            <ul style="margin-top: 5px;">
                                                @foreach(\App\Models\ConferenceDay::all() as $day)
                                                    @if(($day->id == 1 && $ticket->day1_valid) || 
                                                        ($day->id == 2 && $ticket->day2_valid) || 
                                                        ($day->id == 3 && $ticket->day3_valid))
                                                        <li>{{ $day->name }} - {{ $day->date->format('F j, Y') }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </ul>
                                        <p>Please find your complete ticket attached as a PDF file named <strong>"Ticket-{{ $ticket->ticket_number }}.pdf"</strong>. We recommend either printing it or keeping it accessible on your mobile device for the conference.</p>
                                        
                                        <div style="background-color: #e8f0fe; padding: 15px; border-left: 4px solid #1a73e8; margin: 20px 0; border-radius: 4px;">
                                            <strong>📎 Important:</strong> Check the attachments in this email for your official conference ticket in PDF format. You will need to present this ticket for admission.
                                        </div>
                                        
                                        <p>We look forward to seeing you!</p>
                                        <p>Best regards,<br>ZURIW25 Conference Team</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="content-cell" align="center">
                                        <p>© {{ date('Y') }} ZURIW25 Conference. All rights reserved.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 
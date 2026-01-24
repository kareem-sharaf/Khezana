<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('items.email.contact_subject', ['title' => $item->title]) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h1 style="color: #2c3e50; margin-top: 0;">{{ __('items.email.contact_subject', ['title' => $item->title]) }}</h1>
    </div>

    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
        <p style="margin-top: 0;">{{ __('items.email.contact_greeting') }}</p>
        
        <p>{{ __('items.email.contact_intro', ['title' => $item->title]) }}</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>{{ __('items.email.sender_name') }}:</strong> {{ $senderName }}</p>
            <p style="margin: 5px 0 0 0;"><strong>{{ __('items.email.sender_email') }}:</strong> {{ $senderEmail }}</p>
        </div>

        <div style="margin: 20px 0;">
            <h3 style="color: #2c3e50; margin-bottom: 10px;">{{ __('items.email.message') }}:</h3>
            <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; border-radius: 4px;">
                <p style="margin: 0; white-space: pre-wrap;">{{ $message }}</p>
            </div>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
            <p style="margin: 0; color: #7f8c8d; font-size: 14px;">
                {{ __('items.email.contact_footer') }}
            </p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px; color: #7f8c8d; font-size: 12px;">
        <p>{{ __('items.email.auto_generated') }}</p>
    </div>
</body>
</html>

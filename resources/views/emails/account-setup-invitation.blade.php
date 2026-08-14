@component('mail::message')
# Set up your account

Hello {{ $userName }},

You have been invited to access {{ config('app.name') }}. Use the secure button below to create your password. This link expires in 24 hours and can only be used once.

@component('mail::button', ['url' => $setupUrl, 'color' => 'primary'])
Set Password and Log In
@endcomponent

If you were not expecting this invitation, you can ignore this email.

Thanks,  
{{ config('app.name') }}
@endcomponent

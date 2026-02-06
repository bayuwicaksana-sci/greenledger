@extends('emails.layout')

@section('subtitle', 'Request Approved')

@section('body')
<p style="margin:0 0 4px;color:#1e293b;font-size:15px;font-weight:600;">Hello,</p>
<p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.7;">
    Great news — your request has been approved successfully.
</p>

<!-- Detail card (green tinted) -->
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;margin-bottom:28px;">
<tr>
<td style="padding:20px 24px;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <p style="margin:0;color:#166534;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Approved Item</p>
                <p style="margin:4px 0 0;color:#14532d;font-size:15px;font-weight:600;">{{ $itemName }}</p>
            </td>
        </tr>
    </table>
</td>
</tr>
</table>

<!-- CTA button -->
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:24px;">
<tr>
<td align="center">
    <a href="{{ $reviewUrl }}" style="display:inline-block;padding:12px 36px;background-color:#15803d;color:#ffffff;font-size:15px;font-weight:600;border-radius:8px;text-decoration:none;line-height:1.4;" target="_blank">View Details</a>
</td>
</tr>
</table>

<p style="margin:0;color:#94a3b8;font-size:13px;text-align:center;">Click above to view the full approval record.</p>
@endsection

@extends('emails.layout')

@section('subtitle', 'Request Rejected')

@section('body')
<p style="margin:0 0 4px;color:#1e293b;font-size:15px;font-weight:600;">Hello,</p>
<p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.7;">
    Your request has been reviewed and was not approved at this time.
</p>

<!-- Detail card (red tinted) -->
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#fef2f2;border-radius:8px;border:1px solid #fecaca;margin-bottom:28px;">
<tr>
<td style="padding:20px 24px;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="detail-row">
        <tr>
            <td style="padding-bottom:14px;">
                <p style="margin:0;color:#991b1b;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Item</p>
                <p style="margin:4px 0 0;color:#7f1d1d;font-size:15px;font-weight:600;">{{ $itemName }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding-top:14px;border-top:1px solid #fecaca;">
                <p style="margin:0;color:#991b1b;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Reason</p>
                <p style="margin:4px 0 0;color:#7f1d1d;font-size:14px;line-height:1.6;">{{ $reason }}</p>
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
    <a href="{{ $reviewUrl }}" style="display:inline-block;padding:12px 36px;background-color:#64748b;color:#ffffff;font-size:15px;font-weight:600;border-radius:8px;text-decoration:none;line-height:1.4;" target="_blank">View Details</a>
</td>
</tr>
</table>

<p style="margin:0;color:#94a3b8;font-size:13px;text-align:center;">If you have questions, please contact your supervisor.</p>
@endsection

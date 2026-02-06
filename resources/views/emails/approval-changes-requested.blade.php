@extends('emails.layout')

@section('subtitle', 'Changes Requested')

@section('body')
<p style="margin:0 0 4px;color:#1e293b;font-size:15px;font-weight:600;">Hello,</p>
<p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.7;">
    A reviewer has requested changes on your submission. Please review the feedback below and resubmit when ready.
</p>

<!-- Detail card (amber tinted) -->
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#fffbeb;border-radius:8px;border:1px solid #fde68a;margin-bottom:28px;">
<tr>
<td style="padding:20px 24px;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="detail-row">
        <tr>
            <td style="padding-bottom:14px;">
                <p style="margin:0;color:#92400e;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Item</p>
                <p style="margin:4px 0 0;color:#78350f;font-size:15px;font-weight:600;">{{ $itemName }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding-top:14px;border-top:1px solid #fde68a;">
                <p style="margin:0;color:#92400e;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Feedback</p>
                <p style="margin:4px 0 0;color:#78350f;font-size:14px;line-height:1.6;">{{ $feedback }}</p>
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
    <a href="{{ $reviewUrl }}" style="display:inline-block;padding:12px 36px;background-color:#d97706;color:#ffffff;font-size:15px;font-weight:600;border-radius:8px;text-decoration:none;line-height:1.4;" target="_blank">View &amp; Resubmit</a>
</td>
</tr>
</table>

<p style="margin:0;color:#94a3b8;font-size:13px;text-align:center;">Address the feedback and resubmit to continue the approval process.</p>
@endsection

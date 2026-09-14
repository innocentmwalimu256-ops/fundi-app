<?php

namespace App\Services;

use App\Mail\VerificationOtpMail;
use App\Models\AuditLog;
use App\Models\EmailOtp;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailVerificationService
{
    /**
     * Common disposable and temporary email domains to blacklist.
     */
    protected static array $disposableDomains = [
        'mailinator.com',
        'tempmail.com',
        'temp-mail.org',
        '10minutemail.com',
        '10minutemail.net',
        'guerrillamail.com',
        'guerrillamailblock.com',
        'sharklasers.com',
        'grr.la',
        'yopmail.com',
        'yopmail.fr',
        'trashmail.com',
        'trashmail.net',
        'getnada.com',
        'inboxkitten.com',
        'dispostable.com',
        'throwawaymail.com',
        'fakemailgenerator.com',
        'burnermail.io',
        'mohmal.com',
        'mytemp.email',
        'crazymailing.com',
        'nada.ltd',
        'emailondeck.com',
        'generator.email',
        'tempail.com',
        'tempinbox.com',
        'maildrop.cc',
        'fakeinbox.com',
        'dropmail.me',
        'armyspy.com',
        'cuvox.de',
        'dayrep.com',
        'fleckens.hu',
        'gustr.com',
        'jourrapide.com',
        'rhyta.com',
        'superrito.com',
        'teleworm.us',
        'einrot.com',
    ];

    /**
     * Validate whether an email is active, valid, and not from a disposable service.
     *
     * @param string $email
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateActiveEmail(string $email): array
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => __('Please enter a valid email address.'),
            ];
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return [
                'valid' => false,
                'message' => __('Invalid email format provided.'),
            ];
        }

        $domain = trim($parts[1]);

        // 1. Check if domain is in disposable blacklist
        if (in_array($domain, self::$disposableDomains, true)) {
            return [
                'valid' => false,
                'message' => __('Temporary and disposable email addresses are not allowed. Please use your real email address.'),
            ];
        }

        // 2. Check DNS MX and A records if internet connectivity is present
        if (function_exists('checkdnsrr')) {
            $hasMx = @checkdnsrr($domain, 'MX');
            $hasA = @checkdnsrr($domain, 'A');

            // If checkdnsrr returns false for both MX and A, the domain doesn't exist
            if (!$hasMx && !$hasA) {
                // Double-check with gethostbyname to avoid false negative on some local environments
                $ip = @gethostbyname($domain);
                if ($ip === $domain) {
                    return [
                        'valid' => false,
                        'message' => __('This email domain does not exist or has no active mail server (inoperative domain). Please provide a valid active email.'),
                    ];
                }
            }
        }

        return [
            'valid' => true,
            'message' => __('Email address accepted.'),
        ];
    }

    /**
     * Generate a 6-digit OTP code and send it to user's email.
     */
    public static function generateAndSendOtp(User $user, ?string $newEmail = null): EmailOtp
    {
        $targetEmail = trim(strtolower($newEmail ?: $user->email));

        // Invalidate old unverified OTPs for this user
        EmailOtp::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->delete();

        // Generate 6-digit code
        $code = (string) random_int(100000, 999999);

        $otp = EmailOtp::create([
            'user_id' => $user->id,
            'email' => $targetEmail,
            'otp_code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        // Apply dynamic SMTP configuration from DB if configured
        self::configureDynamicMailSettings();

        // Send Email
        try {
            Mail::to($targetEmail)->send(new VerificationOtpMail($user, $code, $otp->expires_at));
            Log::info("[EmailVerification] OTP sent to {$targetEmail}: code={$code}");
        } catch (Throwable $e) {
            Log::warning("[EmailVerification] Failed to send email to {$targetEmail}: " . $e->getMessage());
        }

        // Keep last OTP code in session for dev/fallback convenience
        session(['last_verification_otp' => $code]);

        return $otp;
    }

    /**
     * Verify submitted OTP code.
     */
    public static function verifyOtp(User $user, string $code): array
    {
        $code = trim($code);

        $otp = EmailOtp::where('user_id', $user->id)
            ->where('otp_code', $code)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => __('Invalid OTP code. Please check and try again.'),
            ];
        }

        if ($otp->isExpired()) {
            return [
                'success' => false,
                'message' => __('This OTP code has expired. Please click Resend OTP.'),
            ];
        }

        $otp->update(['verified_at' => now()]);

        // Update user email verification
        $user->update([
            'email' => $otp->email,
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        AuditLog::log('email_verified', "User {$user->full_name} verified email address {$user->email}", 'User', $user->id);

        session()->forget('last_verification_otp');

        return [
            'success' => true,
            'message' => __('Email verified successfully! Your account is now active.'),
        ];
    }

    /**
     * Configure dynamic SMTP settings from SystemSetting table if set.
     */
    public static function configureDynamicMailSettings(): void
    {
        try {
            $smtpHost = SystemSetting::get('mail_host');
            $smtpPort = SystemSetting::get('mail_port');
            $smtpUser = SystemSetting::get('mail_username');
            $smtpPass = SystemSetting::get('mail_password');
            $smtpEncryption = SystemSetting::get('mail_encryption', 'tls');
            $smtpFromAddress = SystemSetting::get('mail_from_address', 'no-reply@fundiapp.co.tz');
            $smtpFromName = SystemSetting::get('mail_from_name', 'FUNDI App');

            if ($smtpHost && $smtpUser && $smtpPass) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', $smtpHost);
                Config::set('mail.mailers.smtp.port', (int) ($smtpPort ?: 587));
                Config::set('mail.mailers.smtp.username', $smtpUser);
                Config::set('mail.mailers.smtp.password', $smtpPass);
                Config::set('mail.mailers.smtp.encryption', $smtpEncryption);
                Config::set('mail.from.address', $smtpFromAddress);
                Config::set('mail.from.name', $smtpFromName);
            }
        } catch (Throwable $e) {
            // fallback to default .env config
        }
    }
}

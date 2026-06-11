<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Exception;

class SmtpSettingsController extends Controller
{
    /**
     * Display the SMTP configuration page.
     */
    public function index()
    {
        // Read current values directly from environment or config
        $config = [
            'mail_mailer' => env('MAIL_MAILER', config('mail.default')),
            'mail_host' => env('MAIL_HOST', config('mail.mailers.smtp.host')),
            'mail_port' => env('MAIL_PORT', config('mail.mailers.smtp.port')),
            'mail_username' => env('MAIL_USERNAME', config('mail.mailers.smtp.username')),
            'mail_password' => env('MAIL_PASSWORD', config('mail.mailers.smtp.password')),
            'mail_encryption' => env('MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', config('mail.from.address')),
            'mail_from_name' => env('MAIL_FROM_NAME', config('mail.from.name')),
        ];

        return view('pages.settings.smtp', [
            'title' => 'Konfigurasi Brevo',
            'config' => (object) $config
        ]);
    }

    /**
     * Update the SMTP configuration in the .env file.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        try {
            $this->updateEnv([
                'MAIL_MAILER' => $validated['mail_mailer'],
                'MAIL_HOST' => $validated['mail_host'],
                'MAIL_PORT' => $validated['mail_port'],
                'MAIL_USERNAME' => $validated['mail_username'],
                'MAIL_PASSWORD' => $validated['mail_password'],
                'MAIL_ENCRYPTION' => $validated['mail_encryption'] ?? 'null',
                'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
                'MAIL_FROM_NAME' => $validated['mail_from_name'],
            ]);

            Artisan::call('config:clear');

            return back()->with('success', 'Konfigurasi SMTP berhasil disimpan dan diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menyimpan konfigurasi: ' . $e->getMessage());
        }
    }

    /**
     * Test the SMTP connection by sending a test email.
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            Config::set('mail.default', $request->mail_mailer ?? env('MAIL_MAILER'));
            Config::set('mail.mailers.smtp.host', $request->mail_host ?? env('MAIL_HOST'));
            Config::set('mail.mailers.smtp.port', $request->mail_port ?? env('MAIL_PORT'));
            Config::set('mail.mailers.smtp.username', $request->mail_username ?? env('MAIL_USERNAME'));
            Config::set('mail.mailers.smtp.password', $request->mail_password ?? env('MAIL_PASSWORD'));
            Config::set('mail.mailers.smtp.encryption', $request->mail_encryption ?? env('MAIL_ENCRYPTION'));
            Config::set('mail.from.address', $request->mail_from_address ?? env('MAIL_FROM_ADDRESS'));
            Config::set('mail.from.name', $request->mail_from_name ?? env('MAIL_FROM_NAME'));

            Mail::raw('Ini adalah email percobaan untuk memastikan koneksi SMTP di sistem penggajian Cimol Bojot AA berfungsi dengan baik. Jika Anda menerima email ini, berarti konfigurasi SMTP Anda sudah benar.', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                    ->subject('Test Koneksi SMTP - Sistem Gaji Cimol Bojot AA');
            });

            return back()->with('success', 'Koneksi SMTP berhasil! Email uji coba telah dikirim ke ' . $validated['test_email']);
        } catch (Exception $e) {
            return back()->with('error', 'Koneksi SMTP Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Helper to safely update the .env file.
     */
    private function updateEnv(array $data)
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            throw new Exception("File .env tidak ditemukan.");
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (preg_match('/\s/', $value)) {
                $value = '"' . $value . '"';
            }

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}

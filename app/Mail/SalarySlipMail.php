<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalarySlipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $periodTitle;
    public $takeHomePay;
    public $pdfData;
    public $pdfFileName;

    /**
     * Create a new message instance.
     */
    public function __construct($employee, $periodTitle, $takeHomePay, $pdfData, $pdfFileName)
    {
        $this->employee = $employee;
        $this->periodTitle = $periodTitle;
        $this->takeHomePay = $takeHomePay;
        $this->pdfData = $pdfData;
        $this->pdfFileName = $pdfFileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Slip Gaji - ' . $this->periodTitle . ' (' . $this->employee->name . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $htmlContent = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; padding: 20px; line-height: 1.6; }
                .container { max-w-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
                .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 24px; }
                .logo-text { font-size: 24px; font-weight: 800; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
                .greeting { font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #0f172a; }
                .message { font-size: 15px; color: #475569; margin-bottom: 24px; }
                .highlight-box { background-color: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: center; }
                .highlight-title { font-size: 14px; text-transform: uppercase; color: #4f46e5; font-weight: 700; margin-bottom: 8px; letter-spacing: 0.5px; }
                .highlight-amount { font-size: 28px; font-weight: 800; color: #3730a3; margin: 0; }
                .footer { font-size: 13px; color: #94a3b8; text-align: center; margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 24px; }
                .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; margin-top: 16px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1 class="logo-text">Cimol Bojot AA</h1>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 14px;">Pemberitahuan Gaji Berkala</p>
                </div>
                
                <p class="greeting">Halo, ' . $this->employee->name . '!</p>
                
                <p class="message">
                    Terima kasih atas dedikasi dan kerja keras Anda untuk Cimol Bojot AA. Bersama email ini, kami melampirkan slip gaji Anda untuk periode <strong>' . $this->periodTitle . '</strong>.
                </p>
                
                <div class="highlight-box">
                    <div class="highlight-title">Total Gaji Bersih (Take Home Pay)</div>
                    <div class="highlight-amount">Rp ' . number_format($this->takeHomePay, 0, ',', '.') . '</div>
                </div>
                
                <p class="message" style="font-size: 14px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                    <strong>Penting:</strong> Dokumen rincian slip gaji resmi Anda telah dilampirkan dalam format PDF pada email ini. Mohon periksa lampiran tersebut untuk melihat detail kehadiran, lembur, serta tunjangan Anda secara lengkap.
                </p>
                
                <p class="message">
                    Jika ada pertanyaan atau ketidaksesuaian mengenai perhitungan gaji ini, silakan hubungi bagian Admin / HRD secepatnya.<br>Terima kasih dan sehat selalu!
                </p>
                
                <div class="footer">
                    &copy; ' . date("Y") . ' Cimol Bojot AA. Email ini dihasilkan secara otomatis oleh sistem, mohon untuk tidak membalas langsung ke alamat ini.
                </div>
            </div>
        </body>
        </html>
        ';

        return new Content(
            htmlString: $htmlContent,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdfData, $this->pdfFileName)
                ->withMime('application/pdf'),
        ];
    }
}

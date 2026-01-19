<?php

namespace App\Mail;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class BudgetCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $budget;
    public $companySettings;

    public function __construct(Budget $budget, $companySettings = null)
    {
        $this->budget = $budget;
        $this->companySettings = $companySettings;
    }

    public function envelope(): Envelope
    {
        $companyName = $this->companySettings['name'] ?? config('app.name');
        
        return new Envelope(
            subject: "Presupuesto #{$this->budget->budget_number} - {$companyName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.budget-created',
        );
    }

    public function attachments(): array
    {
        // Generar PDF
        $pdf = Pdf::loadView('budgets.pdf', [
            'budget' => $this->budget,
            'company' => $this->companySettings,
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "presupuesto_{$this->budget->budget_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Ticket;
use FPDF;

class DashboardController extends BaseController
{
    public function showDashboard()
    {
        session_start();

        if (!isset($_SESSION['logged-in']) || !$_SESSION['logged-in']) {
            header('Location: /login-form');
            exit();
        }

        $ticketModel = new Ticket();
        $tickets = $ticketModel->getAllTickets();

        file_put_contents('debug_dashboard_tickets.log', print_r($tickets, true));

        $data = [
            'tickets_json' => json_encode($tickets),
            'tickets' => $tickets,
        ];

        echo $this->render('dashboard', $data);
    }

    public function generateReport()
    {
        $startDate = $_GET['startDate'] ?? null;
        $endDate = $_GET['endDate'] ?? null;

        // Validate date range
        if (!$startDate || !$endDate) {
            die('Please select a valid date range.');
        }

        $ticketModel = new Ticket();
        $tickets = $ticketModel->getTicketsByDateRange($startDate, $endDate);

        if (empty($tickets)) {
            die('No tickets found for the selected date range.');
        }

        // Initialize PDF with legal paper size (8.5 x 14 inches)
        $pdf = new FPDF('L', 'mm', [216, 356]); // Landscape, mm, legal size
        $pdf->AddPage();
        $pdf->SetMargins(15, 20, 15);

        // Add logo
        $logoPath = 'views/assets/images/logo.png'; // Update with the actual logo path
        $pdf->Image($logoPath, 15, 10, 30);

        // Header
        $pdf->SetFont('Arial', 'B', 14); // Smaller header font
        $pdf->SetTextColor(0, 51, 102); // Corporate dark blue
        $pdf->Cell(0, 10, 'AUF Helpdesk Tickets Report', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, "Date Range: $startDate to $endDate", 0, 1, 'C');
        $pdf->Ln(8);

        // Table Header
        $pdf->SetFont('Arial', 'B', 8); // Smaller font for table headers
        $pdf->SetFillColor(220, 220, 220);

        // Calculate total table width
        $headers = ['Requester', 'Team', 'Ticket Status', 'Priority', 'Assigned To', 'Created At'];
        $widths = [40, 80, 40, 30, 50, 50]; // Adjusted widths for legal layout
        $totalWidth = array_sum($widths);
        $startX = ($pdf->GetPageWidth() - $totalWidth) / 2; // Centering logic

        $pdf->SetX($startX); // Set the X position to start the table
        foreach ($headers as $key => $header) {
            $pdf->Cell($widths[$key], 8, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Table Rows
        $pdf->SetFont('Arial', '', 8); // Smaller font for table rows
        foreach ($tickets as $ticket) {
            $this->drawRow($pdf, $ticket, $widths, $startX);
        }

        // Footer with page numbers, centered
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 0, 'C');

        // Allow saving or viewing
        if (isset($_GET['action']) && $_GET['action'] === 'download') {
            $pdf->Output('D', 'Tickets_Report.pdf'); // Download PDF
        } else {
            $pdf->Output('I', 'Tickets_Report.pdf'); // View PDF in browser
        }
    }

    private function drawRow($pdf, $ticket, $widths, $startX)
    {
        $lineHeight = 6; // Smaller line height for compact table

        // Define cell content
        $cells = [
            $ticket['requester'],
            $ticket['team'] ?: 'Unassigned',
            ucfirst($ticket['status']),
            ucfirst($ticket['priority']),
            $ticket['team_member'] ?: 'Unassigned',
            $ticket['created_at'],
        ];

        // Calculate the height required for wrapped text
        $wrapHeights = [];
        foreach ($cells as $key => $text) {
            $wrapHeights[] = $this->getWrapHeight($text, $widths[$key], $pdf, $lineHeight);
        }

        // Use the maximum height for row consistency
        $rowHeight = max($wrapHeights);

        // Set the starting position for the row
        $pdf->SetX($startX);

        // Render each cell
        foreach ($cells as $key => $text) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // For specific columns like "Team" or others with potential long text, use MultiCell
            if ($key === 1) { // Wrap only for the "Team" column
                $pdf->MultiCell($widths[$key], $lineHeight, $text, 1, 'C');
                $pdf->SetXY($x + $widths[$key], $y); // Move cursor to the next cell
            } else {
                // Render standard single-line cells
                $pdf->Cell($widths[$key], $rowHeight, $text, 1, 0, 'C');
            }
        }

        $pdf->Ln($rowHeight); // Move to the next row
    }

    private function getWrapHeight($text, $width, $pdf, $lineHeight)
    {
        $textWidth = $pdf->GetStringWidth($text);
        $lines = ceil($textWidth / $width);
        return $lines * $lineHeight;
    }
}

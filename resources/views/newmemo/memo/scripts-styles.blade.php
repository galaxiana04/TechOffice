@push('css')
<style>
:root {
    --bg-body: #f3f6f9;
    --bg-card: #ffffff;
    --color-text-main: #455a64;
    --color-text-muted: #90a4ae;
    --color-primary: #2196f3;
    --color-success: #00c853;
    --color-danger: #ff5252;
    --color-warning: #ffab00;
    --border-color: #eceff1;
    --card-radius: 4px;
}

body {
    background-color: var(--bg-body);
    color: var(--color-text-main);
    font-family: 'Segoe UI', 'Roboto', 'Helvetica', sans-serif;
}

/* Modern Card Styling */
.modern-card {
    border-radius: var(--card-radius);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    background-color: var(--bg-card);
    border: none;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.03);
    margin-bottom: 24px;
}

.modern-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.gradient-header {
    background: linear-gradient(135deg, var(--color-primary) 0%, #1976d2 100%);
    color: white;
    padding: 20px 24px;
    border: none;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.gradient-header .card-title {
    font-size: 1.15rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    letter-spacing: 0.3px;
    color: white;
}

.modern-body {
    padding: 24px;
    background: var(--bg-body);
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.info-item {
    background: var(--bg-card);
    padding: 16px;
    border-radius: var(--card-radius);
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 3px solid var(--color-primary);
}

.info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}

.info-icon {
    width: 40px;
    height: 40px;
    background-color: rgba(33, 150, 243, 0.1);
    border-radius: var(--card-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    font-size: 1.2rem;
    flex-shrink: 0;
}

.info-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.info-label {
    font-size: 0.75rem;
    color: var(--color-text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    color: var(--color-text-main);
    font-weight: 600;
}

/* Status Container */
.status-container {
    background: var(--bg-card);
    padding: 16px 20px;
    border-radius: var(--card-radius);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    border-left: 3px solid var(--color-primary);
}

.status-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--color-text-main);
}

.status-badge-modern {
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-terbuka-modern {
    background-color: rgba(255, 82, 82, 0.1);
    color: var(--color-danger);
}

.badge-tertutup-modern {
    background-color: rgba(0, 200, 83, 0.1);
    color: var(--color-success);
}

/* Additional Info */
.additional-info {
    background: var(--bg-card);
    padding: 20px;
    border-radius: var(--card-radius);
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.info-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.info-row:last-child {
    border-bottom: none;
}

.info-row i {
    color: var(--color-primary);
    font-size: 1.1rem;
    width: 20px;
}

/* Section Title */
.section-title {
    color: #263238;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Komat Section */
.komat-section {
    background: var(--bg-card);
    padding: 20px;
    border-radius: var(--card-radius);
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.modern-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.modern-table thead {
    background-color: var(--color-primary);
    color: white;
}

.modern-table thead th {
    padding: 12px;
    font-weight: 600;
    border: none;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-table tbody tr {
    transition: all 0.2s ease;
}

.modern-table tbody tr:hover {
    background-color: rgba(33, 150, 243, 0.05);
}

.modern-table tbody td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.9rem;
}

.modern-table code {
    background-color: rgba(33, 150, 243, 0.1);
    color: var(--color-primary);
    padding: 3px 8px;
    border-radius: 3px;
    font-weight: 600;
    font-size: 0.85rem;
}

/* Timeline Section */
.timeline-section {
    background: var(--bg-card);
    padding: 20px;
    border-radius: var(--card-radius);
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0 10px 16px;
    border-left: 3px solid var(--color-primary);
    margin-bottom: 8px;
}

.timeline-item i {
    color: var(--color-primary);
    font-size: 1.1rem;
}

/* PIC Section */
.pic-section {
    background: var(--bg-card);
    padding: 20px;
    border-radius: var(--card-radius);
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.pic-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.pic-badge {
    background-color: var(--color-primary);
    color: white;
    padding: 8px 16px;
    border-radius: 3px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.pic-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    opacity: 0.9;
    color: white;
}

/* Files Section */
.files-section {
    background: var(--bg-card);
    padding: 20px;
    border-radius: var(--card-radius);
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.file-item {
    padding: 12px;
    background-color: rgba(33, 150, 243, 0.05);
    border-radius: var(--card-radius);
    border-left: 3px solid var(--color-primary);
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.file-item:hover {
    background-color: rgba(33, 150, 243, 0.1);
    transform: translateX(3px);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-modern {
    padding: 8px 16px;
    border-radius: 3px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    opacity: 0.9;
}

.btn-modern.btn-warning {
    background-color: var(--color-warning);
    color: #fff;
}

.btn-modern.btn-primary {
    background-color: var(--color-primary);
    color: #fff;
}

.btn-modern.btn-success {
    background-color: var(--color-success);
    color: #fff;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease forwards;
    opacity: 0;
}

/* Sticky Sidebar */
@media (min-width: 992px) {
    .sticky-sidebar {
        position: sticky;
        top: 20px;
        z-index: 99;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .status-container {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
    }
    
    .modern-body {
        padding: 16px;
    }
}
</style>
@endpush
</style>
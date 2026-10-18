<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdooPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_badge',
        'hero_country',
        'hero_title',
        'hero_subtitle',
        'contact_phone',
        'contact_email',
        'trust_items',
        'modules',
        'services',
        'faqs',
        'comparison_points',
        'focus_keyword',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'seo_score',
        'readability_score',
        'is_indexable',
    ];

    protected function casts(): array
    {
        return [
            'trust_items' => 'array',
            'modules' => 'array',
            'services' => 'array',
            'faqs' => 'array',
            'comparison_points' => 'array',
            'seo_score' => 'integer',
            'readability_score' => 'integer',
            'is_indexable' => 'boolean',
        ];
    }

    /**
     * Retrieve the Odoo Page settings record, creating default if not exists.
     */
    public static function getSettings(): self
    {
        $settings = static::first();

        if ($settings) {
            return $settings;
        }

        return static::create([
            'hero_badge' => 'Official Partner',
            'hero_country' => 'United Arab Emirates',
            'hero_title' => 'Transform Your Business with Official Odoo ERP in Dubai',
            'hero_subtitle' => 'Unify your Accounting, HR & WPS Payroll, CRM, Inventory, and Manufacturing into one intuitive platform. Fully pre-configured for UAE FTA 5% VAT, Central Bank WPS SIF files, and bilingual operations.',
            'contact_phone' => '+971 43 933 082',
            'contact_email' => 'info@lifeguarddatarecovery.com',
            'trust_items' => [
                'FTA 5% VAT Ready',
                'UAE WPS SIF Export',
                'Arabic & English UI',
                '5 UAE Branch Offices',
            ],
            'modules' => [
                [
                    'id' => 'accounting',
                    'name' => 'Accounting & UAE VAT',
                    'icon' => 'receipt',
                    'badge' => 'FTA Compliant',
                    'headline' => 'UAE FTA-Compliant Bilingual Accounting & E-Invoicing',
                    'desc' => 'Automate your 5% UAE VAT calculations, generate FTA Audit Files (FAF), and reconcile seamlessly with UAE Central Bank authorized banks.',
                    'features' => [
                        'Standard UAE Chart of Accounts pre-configured',
                        'Automated 5% VAT computation & FTA Tax Return reports',
                        'Bilingual Arabic-English Tax Invoices with QR code',
                        'Direct bank feeds for ENBD, FAB, ADCB, and Mashreq',
                        'Multi-currency handling (AED, SAR, USD, EUR, GBP)',
                    ],
                ],
                [
                    'id' => 'hrms',
                    'name' => 'HR & WPS Payroll',
                    'icon' => 'users',
                    'badge' => 'WPS Ready',
                    'headline' => 'MOHRE-Compliant UAE Payroll & Workforce Management',
                    'desc' => 'Generate Wage Protection System (WPS) SIF files in one click and calculate End of Service Gratuity (EOSB) strictly per UAE Labour Law 33.',
                    'features' => [
                        'One-click UAE Central Bank SIF format export',
                        'Automated Gratuity (EOSB) accruals per UAE Labour Law',
                        'Emirates ID, Visa, Passport, and labour card expiry alerts',
                        'Biometric device integration & mobile GPS geofencing',
                        'Employee Self-Service (ESS) for leaves, loans, and expenses',
                    ],
                ],
                [
                    'id' => 'crm',
                    'name' => 'CRM & Sales Automation',
                    'icon' => 'target',
                    'badge' => 'WhatsApp Cloud',
                    'headline' => 'Omnichannel Sales Acceleration for UAE Sales Teams',
                    'desc' => 'Connect WhatsApp Cloud API directly to Odoo CRM leads, visualize deal stages, automate quotations, and track sales rep performance.',
                    'features' => [
                        'Direct WhatsApp Business Cloud API integration',
                        'Visual Kanban pipelines with deal probability scoring',
                        'Automated quotation generation with digital e-signatures',
                        'Sales team commissions and target dashboards',
                        'Automated email & WhatsApp follow-up drip sequences',
                    ],
                ],
                [
                    'id' => 'inventory',
                    'name' => 'Inventory & Supply Chain',
                    'icon' => 'boxes',
                    'badge' => 'Multi-Warehouse',
                    'headline' => 'Multi-Location Warehousing for Mainland & Freezone Operations',
                    'desc' => 'Track stock movements across UAE freezones and mainland facilities with double-entry inventory, batch/lot tracking, and landed costs.',
                    'features' => [
                        'Mainland & Freezone (JAFZA, DAFZA, HFZA) warehouse routing',
                        'Landed costs calculation including customs duties & freight',
                        'Batch, Lot, and Expiry date tracking with FIFO/LIFO rules',
                        'Barcode & QR code mobile scanner integration',
                        'Automated reordering rules to eliminate stockouts',
                    ],
                ],
                [
                    'id' => 'mrp',
                    'name' => 'Manufacturing (MRP)',
                    'icon' => 'cog',
                    'badge' => 'Smart Factory',
                    'headline' => 'Precision Manufacturing, BOMs & Work Orders',
                    'desc' => 'Coordinate work orders, multi-level bill of materials, subcontractor operations, and preventive equipment maintenance.',
                    'features' => [
                        'Multi-level Bill of Materials (BOM) & kit management',
                        'Work center capacity planning and routing schedules',
                        'Subcontracting & third-party job work tracking',
                        'Real-time shop floor control tablet interface',
                        'Quality checkpoints and waste reduction analytics',
                    ],
                ],
                [
                    'id' => 'pos',
                    'name' => 'Point of Sale (POS)',
                    'icon' => 'shopping-cart',
                    'badge' => 'Offline Capable',
                    'headline' => 'Blazing-Fast Retail & Restaurant POS for the GCC',
                    'desc' => 'Fully integrated POS that operates online and offline, synchronizing inventory instantly across physical stores and web shops.',
                    'features' => [
                        'Works 100% offline with automatic cloud re-sync',
                        'Integrated payment terminals (Network Intl, Telr, Geidea)',
                        'Customer loyalty programs, gift cards, and coupons',
                        'Kitchen display system (KDS) & table management for F&B',
                        'Unified inventory across physical branches and e-commerce',
                    ],
                ],
            ],
            'services' => [
                [
                    'title' => 'Odoo Implementation',
                    'desc' => 'Turnkey deployment from requirement discovery and business blueprint to configuration, testing, and successful UAE go-live.',
                    'icon' => 'rocket',
                ],
                [
                    'title' => 'Customization & Module Dev',
                    'desc' => "Tailored Python module development and workflow extensions to accommodate your organization's unique operational rules.",
                    'icon' => 'code-2',
                ],
                [
                    'title' => 'Legacy ERP Data Migration',
                    'desc' => 'Secure extraction, sanitization, and migration of historical records from Tally, SAP, Oracle, Zoho, QuickBooks, or Excel.',
                    'icon' => 'database',
                ],
                [
                    'title' => 'UAE Third-Party Integrations',
                    'desc' => 'API bridges to payment gateways, UAE courier services (Aramex, DHL), biometric scanners, and WhatsApp Business API.',
                    'icon' => 'workflow',
                ],
                [
                    'title' => 'Corporate Training & Enablement',
                    'desc' => 'Role-based, bilingual training for executives, accountants, warehouse supervisors, and sales teams in Dubai or on-site.',
                    'icon' => 'graduation-cap',
                ],
                [
                    'title' => '24/7 Managed Support & SLA',
                    'desc' => 'Dedicated Dubai helpdesk with guaranteed response times, proactive updates, bug resolution, and version migrations.',
                    'icon' => 'headphones',
                ],
            ],
            'faqs' => [
                [
                    'q' => 'Is Odoo fully approved and compliant with UAE Federal Tax Authority (FTA) regulations?',
                    'a' => 'Yes. Our Odoo UAE deployment is strictly aligned with UAE FTA requirements. It calculates 5% VAT automatically, generates bilingual Tax Invoices with mandatory QR codes, handles reverse charges, and exports Federal Tax Authority Audit Files (FAF) for seamless quarterly return filings.',
                ],
                [
                    'q' => 'Can Odoo generate Wage Protection System (WPS) SIF files for UAE banks?',
                    'a' => 'Yes, in a single click. Our UAE payroll module formats employee wages into the standard UAE Central Bank .SIF file format, fully compatible with WPS agents like Al Ansari Exchange, ENBD, FAB, ADCB, and Mashreq.',
                ],
                [
                    'q' => 'How does your team handle migration from our existing ERP (Tally, QuickBooks, Zoho, SAP)?',
                    'a' => 'We execute a 4-phase data migration strategy: extraction, cleaning & reconciliation, staging import, and final trial balance verification. Your historical ledgers, open customer balances, stock valuation, and supplier dues are ported with zero downtime.',
                ],
                [
                    'q' => 'How long does a typical Odoo implementation take for a UAE business?',
                    'a' => 'Standard deployments (Accounting, Sales, Purchase & Inventory) usually take between 3 to 6 weeks. Complex enterprise implementations with custom manufacturing, POS, and HRMS take 8 to 12 weeks with full user acceptance testing.',
                ],
            ],
            'focus_keyword' => 'odoo partner dubai',
            'meta_title' => 'Official Odoo Partner Dubai & UAE | Certified ERP Implementation',
            'meta_description' => 'Official Odoo Partner in UAE. Certified Odoo ERP implementation, FTA-compliant VAT accounting, UAE WPS payroll, and customization across Dubai & UAE.',
            'meta_keywords' => 'odoo partner dubai, odoo uae, official odoo partner uae, odoo erp dubai, odoo implementation uae, odoo vat accounting fta, odoo wps payroll dubai',
            'seo_score' => 95,
            'readability_score' => 90,
            'is_indexable' => true,
        ]);
    }
}

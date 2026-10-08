<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialService;

class FinancialServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'type' => 'loan',
                'name' => 'Personal Loan',
                'description' => 'Unsecured personal loan with competitive rates.',
                'currency_code' => 'USD',
                'min_amount' => 1000,
                'max_amount' => 50000,
                'interest_rate' => 8.50,
                'term_months' => 36,
                'is_active' => true,
                'criteria' => ['min_credit_score' => 650, 'min_income' => 2000],
                'sort_order' => 1,
            ],
            [
                'type' => 'loan',
                'name' => 'Business Loan',
                'description' => 'Funding for business expansion and working capital.',
                'currency_code' => 'USD',
                'min_amount' => 5000,
                'max_amount' => 250000,
                'interest_rate' => 12.00,
                'term_months' => 60,
                'is_active' => true,
                'criteria' => ['min_credit_score' => 700, 'min_revenue' => 50000],
                'sort_order' => 2,
            ],
            [
                'type' => 'loan',
                'name' => 'Payday Loan',
                'description' => 'Quick short-term loan for emergency needs.',
                'currency_code' => 'USD',
                'min_amount' => 100,
                'max_amount' => 1000,
                'interest_rate' => 15.00,
                'term_months' => 3,
                'is_active' => true,
                'criteria' => ['min_credit_score' => 550, 'min_income' => 500],
                'sort_order' => 3,
            ],
            [
                'type' => 'grant',
                'name' => 'Educational Grant',
                'description' => 'Financial aid for education and professional development.',
                'currency_code' => 'USD',
                'min_amount' => 500,
                'max_amount' => 10000,
                'interest_rate' => 0,
                'term_months' => 0,
                'is_active' => true,
                'criteria' => ['min_age' => 18, 'max_debt_to_income' => 0.3],
                'sort_order' => 4,
            ],
            [
                'type' => 'grant',
                'name' => 'Business Grant',
                'description' => 'Non-repayable funding for qualifying businesses.',
                'currency_code' => 'USD',
                'min_amount' => 2500,
                'max_amount' => 50000,
                'interest_rate' => 0,
                'term_months' => 0,
                'is_active' => true,
                'criteria' => ['min_revenue' => 10000, 'industry_focus' => 'tech'],
                'sort_order' => 5,
            ],
            [
                'type' => 'tax_refund',
                'name' => 'Tax Refund Processing',
                'description' => 'File your tax refund claim and track status.',
                'currency_code' => 'USD',
                'min_amount' => 0,
                'max_amount' => null,
                'interest_rate' => 0,
                'term_months' => 0,
                'is_active' => true,
                'criteria' => [],
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            FinancialService::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}

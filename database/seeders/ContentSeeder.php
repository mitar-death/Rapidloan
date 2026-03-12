<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LoanPlan;
use App\Models\Frontend;
use App\Models\Form;
use Illuminate\Database\Seeder;
use App\Constants\Status;
use Illuminate\Support\Facades\File;

class ContentSeeder extends Seeder
{
    public function run()
    {
        // 1. Create a Form for Loan Plans if it doesn't exist
        $form = Form::updateOrCreate(
            ['act' => 'loan_plan'],
            [
                'form_data' => [
                    'personal_id' => [
                        'name' => 'National ID / Passport',
                        'label' => 'personal_id',
                        'is_required' => 'required',
                        'instruction' => 'Upload a clear copy of your ID',
                        'extensions' => 'jpg,jpeg,png,pdf',
                        'type' => 'file',
                        'width' => '12'
                    ],
                    'proof_of_income' => [
                        'name' => 'Proof of Income',
                        'label' => 'proof_of_income',
                        'is_required' => 'required',
                        'instruction' => 'Latest 3 months bank statements or payslips',
                        'extensions' => 'pdf,jpg,png',
                        'type' => 'file',
                        'width' => '12'
                    ]
                ]
            ]
        );

        // 2. Create Categories
        $businessCat = Category::updateOrCreate(['name' => 'Business'], ['status' => Status::ENABLE]);
        $individualCat = Category::updateOrCreate(['name' => 'Individual'], ['status' => Status::ENABLE]);
        $studentCat = Category::updateOrCreate(['name' => 'Educational'], ['status' => Status::ENABLE]);

        // 3. Create Loan Plans with Sensible Interest Rates
        // Formula: (total_installment * per_installment) - 100
        $plans = [
            [
                'category_id' => $businessCat->id,
                'form_id' => $form->id,
                'name' => 'SME Growth Accelerator',
                'title' => 'Scaling your small business with ease',
                'minimum_amount' => 15000,
                'maximum_amount' => 75000,
                'per_installment' => 4.6, // 10.4% APR (24 installments)
                'installment_interval' => 30,
                'total_installment' => 24,
                'application_fixed_charge' => 150,
                'application_percent_charge' => 1.5,
                'delay_value' => 5,
                'fixed_charge' => 20,
                'percent_charge' => 2,
                'is_featured' => Status::YES,
                'status' => Status::ENABLE,
            ],
            [
                'category_id' => $businessCat->id,
                'form_id' => $form->id,
                'name' => 'Corporate Equipment Finance',
                'title' => 'Modernize your infrastructure today',
                'minimum_amount' => 50000,
                'maximum_amount' => 500000,
                'per_installment' => 2.2, // 5.6% APR (48 installments)
                'installment_interval' => 30,
                'total_installment' => 48,
                'application_fixed_charge' => 1000,
                'application_percent_charge' => 0.5,
                'delay_value' => 10,
                'fixed_charge' => 50,
                'percent_charge' => 1,
                'is_featured' => Status::YES,
                'status' => Status::ENABLE,
            ],
            [
                'category_id' => $individualCat->id,
                'form_id' => $form->id,
                'name' => 'Smart Home Improvement',
                'title' => 'Upgrade your living space',
                'minimum_amount' => 2000,
                'maximum_amount' => 25000,
                'per_installment' => 9.0, // 8% APR (12 installments)
                'installment_interval' => 30,
                'total_installment' => 12,
                'application_fixed_charge' => 50,
                'application_percent_charge' => 1,
                'delay_value' => 3,
                'fixed_charge' => 15,
                'percent_charge' => 3,
                'is_featured' => Status::YES,
                'status' => Status::ENABLE,
            ],
            [
                'category_id' => $studentCat->id,
                'form_id' => $form->id,
                'name' => 'Higher Education Grant',
                'title' => 'Invest in your academic future',
                'minimum_amount' => 5000,
                'maximum_amount' => 40000,
                'per_installment' => 1.8, // 8% APR (60 installments)
                'installment_interval' => 30,
                'total_installment' => 60,
                'application_fixed_charge' => 0,
                'application_percent_charge' => 0,
                'delay_value' => 15,
                'fixed_charge' => 0,
                'percent_charge' => 0.5,
                'is_featured' => Status::YES,
                'status' => Status::ENABLE,
            ],
            [
                'category_id' => $individualCat->id,
                'form_id' => $form->id,
                'name' => 'Debt Consolidation Relief',
                'title' => 'Simplify your financial life',
                'minimum_amount' => 1000,
                'maximum_amount' => 15000,
                'per_installment' => 6.0, // 20% APR (20 installments)
                'installment_interval' => 15,
                'total_installment' => 20,
                'application_fixed_charge' => 25,
                'application_percent_charge' => 0.5,
                'delay_value' => 2,
                'fixed_charge' => 10,
                'percent_charge' => 2,
                'is_featured' => Status::YES,
                'status' => Status::ENABLE,
            ],
        ];

        foreach ($plans as $plan) {
            LoanPlan::updateOrCreate(['name' => $plan['name']], $plan);
        }

        // 4. Update FAQ Content
        Frontend::updateOrCreate(
            ['data_keys' => 'faq.content', 'tempname' => 'basic'],
            [
                'data_values' => [
                    'heading' => 'Your Questions, Answered',
                    'subheading' => 'FAQ',
                    'description' => 'We believe in full transparency. Here are the most common questions our clients ask about our lending processes and support.'
                ]
            ]
        );

        Frontend::where('data_keys', 'faq.element')->delete();
        $faqs = [
            ['How quickly can I get approved for a loan?', 'Our standard review process takes between 24 to 48 business hours. Once all required documents are submitted and verified, you will receive a notification regarding your status.'],
            ['What documents are required for a business loan?', 'Generally, we require business registration certificates, the last 6 months of bank statements, and a valid government-issued ID for all majority shareholders.'],
            ['Can I repay my loan earlier than scheduled?', 'Yes, we encourage early repayments and do not charge any early settlement penalties on most of our loan categories. This helps you save on future interest.'],
            ['How is my loan interest rate determined?', 'Interest rates are calculated based on the loan category, current market conditions, and a preliminary assessment of your documentation and business stability.'],
            ['What happens if I miss an installment payment?', 'If a payment is missed, a grace period of 2 days is usually provided. After this, a late fee (fixed and/or percentage-based) will be applied as specified in your loan agreement.'],
            ['Are my personal and financial details secure?', 'Absolutely. We utilize bank-grade 256-bit SSL encryption to ensure all your data remains private and secure throughout the application process and beyond.'],
            ['Do you offer loans to international applicants?', 'Currently, our primary focus is on residents and registered businesses within our operating regions. Please contact support to check eligibility for your specific location.'],
            ['Can I have multiple active loans simultaneously?', 'Typically, we allow one active loan per category. However, a second loan may be considered if the first has been consistently repaid for at least 50% of its duration.'],
            ['What is the maximum loan amount available?', 'Our Corporate Expansion loans offer up to $500,000. For specific requirements above this amount, please reach out to our dedicated corporate relations team.'],
            ['How do I contact support for urgent issues?', 'Our support team is available via live chat during business hours. You can also email us at support@yournetinvestment.com for a response within 24 hours.']
        ];
        foreach ($faqs as $faq) {
            Frontend::create(['data_keys' => 'faq.element', 'tempname' => 'basic', 'data_values' => ['question' => $faq[0], 'answer' => $faq[1]]]);
        }

        // 5. Testimonials
        Frontend::where('data_keys', 'testimonial.element')->delete();
        $testimonials = [
            ['Sarah Jenkins', 'SME Owner', 'Your Net Investment provided the capital I needed to scale my bakery when traditional banks said no. The process was fast and transparent.'],
            ['Michael Chen', 'Tech Startup Founder', 'The growth accelerator loan was a game-changer for our software launch. Exceptional support and reasonable rates.'],
            ['Amanda Rodriguez', 'Freelance Designer', 'I used the emergency loan for an unexpected medical bill. Approval happened the same day, giving me much-needed peace of mind.'],
            ['David Thompson', 'Real Estate Investor', 'Reliable, professional, and built for modern business. I highly recommend their corporate finance solutions.'],
            ['Emily White', 'Graduate Student', 'The educational loan allowed me to focus on my Master\'s degree without the stress of immediate high-interest repayments. Simple and fair.']
        ];
        foreach ($testimonials as $t) {
            Frontend::create(['data_keys' => 'testimonial.element', 'tempname' => 'basic', 'data_values' => ['author' => $t[0], 'designation' => $t[1], 'quote' => $t[2], 'image' => null]]);
        }

        // 6. Comprehensive Blogs with Stock Images
        Frontend::updateOrCreate(['data_keys' => 'blog.content', 'tempname' => 'basic'], ['data_values' => ['heading' => 'Financial Insights & Success Stories', 'subheading' => 'The Origin Blog']]);
        Frontend::where('data_keys', 'blog.element')->delete();

        $blogs = [
            [
                'title' => 'From Kitchen to High Street: The Bakery Expansion Story',
                'slug' => 'bakery-expansion-story',
                'description' => "<p>Sarah Jenkins started 'The Flourish Kitchen' in her modest home oven with a single goal: to share her grandmother's sourdough recipe with her neighborhood. Within months, her weekly subscription list grew from five neighbors to over fifty, and her kitchen was overflowing with flour sacks and proofing baskets. Sarah knew she had reached a crossroads—scale up or stop growing.</p><p>Scaling meant more than just a larger oven; it meant a professional-grade storefront, three industrial mixers, and hiring her first staff member. Traditional lenders were hesitant to support a home-based business with less than a year of commercial history. That's where our SME Growth Accelerator loan came in. With a $15,000 injection, Sarah secured a lease in Canary Wharf, fitted out her boutique shop, and purchased high-capacity equipment.</p><p>Today, 'The Flourish Kitchen' is a staple of North Street. Sarah has tripled her daily production, employs three local residents, and is already planning her second location. 'The loan wasn't just capital,' Sarah says, 'it was the bridge between a hobby and a legacy.'</p>",
                'img_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=860&q=80'
            ],
            [
                'title' => 'Navigating the Credit Maze: A Professional\'s Guide',
                'slug' => 'navigating-credit-maze',
                'description' => "<p>At 28, John Clarkson, a junior analyst in London, had a clear vision: owning his first apartment. However, years of small credit card debts and a lack of financial planning had left him with a credit score that made mortgage brokers look the other way. John felt trapped in a cycle of high-interest short-term loans that barely covered his expenses.</p><p>His journey to financial health began with our Debt Consolidation Relief plan. By combining his high-interest debts into one manageable monthly payment with a lower interest rate, John was able to breathe. We provided him with a structured 18-month roadmap, focusing on consistent repayment and strategic credit utilization. This wasn't just about paying off debt; it was about rebuilding his financial identity.</p><p>Fast forward to today: John's credit score has increased by 165 points. He recently secured a Premier Home Upgrade loan at a competitive rate, transforming his new flat into a modern living space. John's story proves that with the right tools and a solid plan, the 'credit maze' can be successfully navigated by anyone.</p>",
                'img_url' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=860&q=80'
            ],
            [
                'title' => 'The 24-Hour Approval: Meeting Urgent Tech Needs',
                'slug' => 'urgent-tech-financing',
                'description' => "<p>For Michael Chen, CEO of 'Vortex Stream', Tuesday morning started with a nightmare. Just three days before their biggest product launch of the year, a catastrophic power surge at their primary data center fried their core server array. Without immediate replacement, the launch—representing six months of development and 40% of their annual revenue—would fail.</p><p>Michael contacted his primary bank immediately, only to be told the approval process for an emergency equipment loan would take 7 to 10 business days. He couldn't wait 10 hours, let alone 10 days. Seeking a faster solution, he applied for our Emergency Financing. Because our platform is built for speed, we were able to verify his business standing and revenue metrics within hours.</p><p>By Wednesday morning, $7,500 was in Michael's account. By Wednesday afternoon, the new server array was installed and being configured. The launch went off without a hitch on Friday, breaking all previous sales records. 'Your Net Investment didn't just give us a loan,' Michael explains, 'they saved our company's future.'</p>",
                'img_url' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=860&q=80'
            ]
        ];

        foreach ($blogs as $b) {
            $imageName = null;
            // Attempt to download stock image for local use if possible, or just use URL if app supports it
            // For this seeder, we will stick to the URL in data_values as placeholders often use them, 
            // but we'll populate the 'slug' field correctly this time.
            Frontend::create([
                'data_keys' => 'blog.element',
                'tempname' => 'basic',
                'slug' => $b['slug'],
                'data_values' => [
                    'title' => $b['title'],
                    'slug' => $b['slug'],
                    'description_nic' => $b['description'],
                    'image' => $b['img_url'] // Storing the direct Unsplash URL
                ]
            ]);
        }

        // 7. Contact Information
        Frontend::updateOrCreate(['data_keys' => 'contact.content', 'tempname' => 'basic'], [
            'data_values' => [
                'heading' => 'Get in Touch with Our Experts',
                'phone' => '+44 20 7946 0123',
                'email' => 'support@yournetinvestment.com',
                'location' => '38/D North Street, Canary Wharf, London, UK',
                'map' => 'https://www.google.com/maps/embed?pb=...'
            ]
        ]);

        // 8. Policy Pages
        Frontend::where('data_keys', 'policy_pages.element')->delete();
        Frontend::create(['data_keys' => 'policy_pages.element', 'tempname' => 'basic', 'slug' => 'privacy-policy', 'data_values' => [
            'title' => 'Privacy Policy',
            'details' => 'At Your Net Investment, we value your privacy. This policy outlines how we collect, use, and protect your personal data in compliance with GDPR. We use bank-grade encryption for all financial transmissions and never share your data with unauthorized third parties. By using our services, you consent to our data handling practices focused on providing secure loan processing.'
        ]]);
        Frontend::create(['data_keys' => 'policy_pages.element', 'tempname' => 'basic', 'slug' => 'terms-of-service', 'data_values' => [
            'title' => 'Terms of Service',
            'details' => 'These terms govern your use of the Your Net Investment platform. All loan applications are subject to a standard credit assessment and documentation check. Upon approval, repayment schedules are binding. We provide a 2-day grace period for installments, after which late fees apply as specified in the loan plan details. Continued use of the platform constitutes agreement to these operational terms.'
        ]]);

        // 9. SEO Data
        $seo = Frontend::where('data_keys', 'seo.data')->first();
        if ($seo) {
            $data_values = $seo->data_values;
            $data_values->description = "Empower your dreams with Your Net Investment. We provide professional loan solutions for startups, corporate expansion, and personal needs with competitive rates.";
            $data_values->social_title = "Your Net Investment - Professional Loan Solutions for Growth";
            $data_values->social_description = "Secure the funding you need to scale your business or manage personal finances. Fast approval and fully transparent terms.";
            $seo->data_values = $data_values;
            $seo->save();
        }

        // 10. Update Plan Section Header
        Frontend::updateOrCreate(['data_keys' => 'plan.content', 'tempname' => 'basic'], ['data_values' => ['subheading' => 'Featured Plans', 'heading' => 'Flexible Loan Solutions Tailored for Your Success Country Wide']]);

        // 11. About Section Content
        Frontend::updateOrCreate(['data_keys' => 'about.content', 'tempname' => 'basic'], [
            'data_values' => [
                'has_image' => '1',
                'heading' => 'Empowering Financial Freedom and Trusted Solutions',
                'subheading' => 'About',
                'description' => 'Empowering dreams with flexible and transparent loan solutions. At Your Net Investment, we are committed to providing accessible capital to help businesses scale and individuals thrive.',
                'button_name' => 'About Us',
                'button_link' => 'about',
                'image' => '64cb498c47a021691044236.png'
            ]
        ]);

        // 12. Feature Section Content
        Frontend::updateOrCreate(['data_keys' => 'feature.content', 'tempname' => 'basic'], [
            'data_values' => [
                'has_image' => '1',
                'subheading' => 'Feature',
                'heading' => 'Unlocking the Power of Our Loan Features',
                'content' => 'We believe in transparency, and that\'s why we offer competitive interest rates and flexible repayment options. Our user-friendly loan management platform makes it easy for you to monitor your loan status, make payments, and stay on top of your financial journey.',
                'image' => '64cb4da7b5efa1691045287.png'
            ]
        ]);

        Frontend::where('data_keys', 'feature.element')->delete();
        $features = [
            ['las la-clock', 'Flexible Repayment', 'Customize your loan with easy payment plans.'],
            ['las la-percentage', 'Low-Interest Rates', 'Enjoy competitive rates for affordable borrowing.'],
            ['las la-bolt', 'Quick Approval Process', 'Get funds swiftly with fast approvals.'],
            ['las la-file-invoice-dollar', 'No Hidden Fees', 'Transparent loan terms, no surprises or extras.'],
            ['las la-user-tie', 'Loan Assistance', 'Our team is here to guide and support you.'],
            ['las la-headset', '24/7 Support', 'Flexible Repayment : Customize your loan with easy payment plans.'],
            ['fas fa-hand-holding-usd', 'Low Cost', 'Low-Interest Rates: Enjoy competitive rates for affordable borrowing.']
        ];
        foreach ($features as $f) {
            Frontend::create(['data_keys' => 'feature.element', 'tempname' => 'basic', 'data_values' => ['icon' => $f[0], 'title' => $f[1], 'details' => $f[2]]]);
        }

        // 13. Counter Section
        Frontend::where('data_keys', 'counter.element')->delete();
        $counters = [
            ['Total Loan', '$8M+'],
            ['Winning Awards', '195'],
            ['Happy Client', '1K+'],
            ['Country Wide', '110+']
        ];
        foreach ($counters as $c) {
            Frontend::create(['data_keys' => 'counter.element', 'tempname' => 'basic', 'data_values' => ['title' => $c[0], 'counter_digit' => $c[1]]]);
        }

        // 14. Testimonial Header
        Frontend::updateOrCreate(['data_keys' => 'testimonial.content', 'tempname' => 'basic'], [
            'data_values' => [
                'heading' => 'Happy Client',
                'subheading' => 'Winning Awards Get in Touch with Our Experts'
            ]
        ]);

        // 15. Realistic Client Logos
        Frontend::where('data_keys', 'client.element')->delete();
        $clientLogos = [
            'https://images.unsplash.com/photo-1614028674026-6329139d441e?w=200&h=200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1599305090598-fe179d501c27?w=200&h=200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1583608205776-bfd35f0d9fd6?w=200&h=200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=200&h=200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1614850523296-d8c1af93d400?w=200&h=200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1615915468310-720c788ed07a?w=200&h=200&fit=crop&q=80',
            'https://images.unsplash.com/photo-1618111516067-12bd40294fc9?w=200&h=200&fit=crop&q=80'
        ];

        foreach ($clientLogos as $logo) {
            Frontend::create([
                'data_keys' => 'client.element',
                'tempname' => 'basic',
                'data_values' => [
                    'has_image' => '1',
                    'image' => $logo
                ]
            ]);
        }
    }
}

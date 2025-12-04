<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPageSection;
use App\Models\LandingPageElement;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Hero section based on your existing landing page
        $heroSection = LandingPageSection::create([
            'name' => 'hero',
            'title' => 'Discover Amazing Products at Unbeatable Prices',
            'content' => 'Shop our extensive collection of premium quality items. Fast shipping, competitive prices, and exceptional customer service.',
            'section_type' => 'hero',
            'position' => 0,
            'is_active' => true,
        ]);

        // Add elements to the hero section (the features shown in the hero section)
        LandingPageElement::create([
            'name' => 'Free Shipping',
            'element_type' => 'icon',
            'content' => 'bi bi-truck',
            'attributes' => ['title' => 'On orders over $50'],
            'position' => 0,
            'section_id' => $heroSection->id,
            'is_active' => true,
        ]);

        LandingPageElement::create([
            'name' => 'Secure Payment',
            'element_type' => 'icon',
            'content' => 'bi bi-lock',
            'attributes' => ['title' => 'Safe and encrypted'],
            'position' => 1,
            'section_id' => $heroSection->id,
            'is_active' => true,
        ]);

        LandingPageElement::create([
            'name' => '24/7 Support',
            'element_type' => 'icon',
            'content' => 'bi bi-headset',
            'attributes' => ['title' => 'Dedicated assistance'],
            'position' => 2,
            'section_id' => $heroSection->id,
            'is_active' => true,
        ]);

        LandingPageElement::create([
            'name' => 'Easy Returns',
            'element_type' => 'icon',
            'content' => 'bi bi-arrow-return-right',
            'attributes' => ['title' => '30-day guarantee'],
            'position' => 3,
            'section_id' => $heroSection->id,
            'is_active' => true,
        ]);

        // Create Features section based on your existing landing page
        $featuresSection = LandingPageSection::create([
            'name' => 'features',
            'title' => 'Why Choose Us',
            'content' => 'We provide the best shopping experience with quality products',
            'section_type' => 'features',
            'position' => 1,
            'is_active' => true,
        ]);

        // Add elements to the features section
        LandingPageElement::create([
            'name' => 'Quality Guaranteed',
            'element_type' => 'heading',
            'content' => 'fas fa-shield-alt',
            'attributes' => ['description' => 'All our products are carefully selected and guaranteed for quality. We stand behind every purchase with our quality promise.'],
            'position' => 0,
            'section_id' => $featuresSection->id,
            'is_active' => true,
        ]);

        LandingPageElement::create([
            'name' => 'Best Prices',
            'element_type' => 'heading',
            'content' => 'fas fa-tag',
            'attributes' => ['description' => 'We offer competitive pricing on all products with regular promotions and discounts for our valued customers.'],
            'position' => 1,
            'section_id' => $featuresSection->id,
            'is_active' => true,
        ]);

        LandingPageElement::create([
            'name' => 'Support Team',
            'element_type' => 'heading',
            'content' => 'fas fa-headset',
            'attributes' => ['description' => 'Our dedicated support team is available to assist you with any questions or concerns you may have.'],
            'position' => 2,
            'section_id' => $featuresSection->id,
            'is_active' => true,
        ]);

        // Create Products section to match your existing landing page
        $productsSection = LandingPageSection::create([
            'name' => 'products',
            'title' => 'Featured Products',
            'content' => 'Check out our most popular items',
            'section_type' => 'products',
            'position' => 2,
            'is_active' => true,
        ]);

        // The products are dynamically loaded from the database, so no specific elements needed for individual products

        // Create CTA (Call to Action) section based on your existing landing page
        $ctaSection = LandingPageSection::create([
            'name' => 'cta',
            'title' => 'Ready to Start Shopping?',
            'content' => 'Become a member today and enjoy exclusive benefits, special discounts, and early access to new products.',
            'section_type' => 'cta',
            'position' => 3,
            'is_active' => true,
        ]);

        // The CTA section has the join button which is part of the template
        
        // Create Newsletter section based on your existing landing page
        LandingPageSection::create([
            'name' => 'newsletter',
            'title' => 'Stay Updated',
            'content' => 'Subscribe to our newsletter to receive updates and offers.',
            'section_type' => 'newsletter',
            'position' => 4,
            'is_active' => true,
        ]);
    }
}
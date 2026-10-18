<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchLocationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed target services if not present
        $services = [
            'hard-drive-data-recovery-dubai' => 'Hard Drive Data Recovery Dubai',
            'ssd-drive-data-recovery-in-dubai' => 'SSD Drive Data Recovery Dubai',
            'raid-data-recovery-service-dubai' => 'RAID Data Recovery Dubai',
            'nas-data-recovery-dubai' => 'NAS Data Recovery Dubai',
            'server-data-recovery-dubai' => 'Server Data Recovery Dubai',
            'cctv-footage-recovery-dubai' => 'CCTV Footage Recovery Dubai',
            'sd-card-data-recovery' => 'SD Card & USB Data Recovery',
            'iphone-data-recovery' => 'iPhone & Mobile Data Recovery',
            'ransomware-data-recovery' => 'Ransomware Data Recovery',
            'mac-data-recovery' => 'Macbook & Mac Data Recovery',
        ];

        foreach ($services as $slug => $title) {
            Service::firstOrCreate(['slug' => $slug], [
                'title' => $title,
                'summary' => "Recovery service for {$title}",
                'status' => 'published',
            ]);
        }
    }

    /**
     * Test the Locations Hub (/locations) displays all 7 official branch cards.
     */
    public function test_locations_hub_displays_all_seven_branches(): void
    {
        $response = $this->get('/locations');
        $response->assertStatus(200);

        // Check for all 7 branch office titles
        $response->assertSee('Dubai (Main Office)');
        $response->assertSee('Al Barsha, Dubai');
        $response->assertSee('Abu Dhabi');
        $response->assertSee('Sharjah');
        $response->assertSee('Al Wasl, Dubai');
        $response->assertSee('India');
        $response->assertSee('Kenya');

        // Check addresses
        $response->assertSee('Bayat center S25 Computer Street - Bur Dubai - Dubai.');
        $response->assertSee('DasselDorf Business Point, AL Barsha 1, Behind Mall Of Emirates, Dubai, United Arab Emirates.');
        $response->assertSee('Electra Street, 11 Al Raqqay St - Al Danah - E10 - Abu Dhabi.');
        $response->assertSee('Office 1202-4, Avenue Business Centre, Al Majaz Waterfront, Buhaira Corniche, Sharjah.');
        $response->assertSee('RAG Biz HQ, Addiyar Bldg - Office M17 Sheikh Zayed Rd - Al Wasl - Dubai - UAE.');
        $response->assertSee('404, 4th Floor, A Block, CTC Park Ln, Sarojini Devi Rd, Secunderabad, Telangana 500003.');
        $response->assertSee('Mirage Tower, 13th Floor, Nairobi, Kenya.');

        // Check phone numbers & contact info
        $response->assertSee('+971 55 483 8545');
        $response->assertSee('+971 43 933 082');
        $response->assertSee('+971 56 410 1040');
        $response->assertSee('+971 50 217 6793');
        $response->assertSee('+971 50 634 3878');
        $response->assertSee('+971 56 997 8107');
        $response->assertSee('+91 89775 44544');
        $response->assertSee('040-6638 6333');
        $response->assertSee('+254 748 020 399');
        $response->assertSee('lifeguarddatarecovery.in');
        $response->assertSee('lifeguarddatarecovery.co.ke');

        // Check aliases redirect
        $this->get('/all-locations')->assertRedirect('/locations');
        $this->get('/our-locations')->assertRedirect('/locations');
        $this->get('/location')->assertRedirect('/locations');
    }

    /**
     * Test Dubai Main Office page has all services and internal links.
     */
    public function test_dubai_main_office_page_is_rich_and_linked_to_all_services(): void
    {
        $response = $this->get('/data-recovery-dubai');
        $response->assertStatus(200);

        // Title and verification
        $response->assertSee('Data Recovery Services in Dubai (Main Office)');
        $response->assertSee('Bayat center S25 Computer Street - Bur Dubai - Dubai.');
        $response->assertSee('+971 55 483 8545');
        $response->assertSee('+971 43 933 082');

        // Check heading required by test suite
        $response->assertSee('Specialized Data Recovery Services at this Location');

        // All 10 services linked internally
        $response->assertSee('/hard-drive-data-recovery-dubai');
        $response->assertSee('/ssd-drive-data-recovery-in-dubai');
        $response->assertSee('/raid-data-recovery-service-dubai');
        $response->assertSee('/nas-data-recovery-dubai');
        $response->assertSee('/server-data-recovery-dubai');
        $response->assertSee('/cctv-footage-recovery-dubai');
        $response->assertSee('/sd-card-data-recovery');
        $response->assertSee('/iphone-data-recovery');
        $response->assertSee('/ransomware-data-recovery');
        $response->assertSee('/mac-data-recovery');

        // Inter-branch links
        $response->assertSee('/data-recovery-al-barsha');
        $response->assertSee('/data-recovery-abu-dhabi');
        $response->assertSee('/data-recovery-sharjah');
        $response->assertSee('/data-recovery-al-wasl');
        $response->assertSee('/data-recovery-india');
        $response->assertSee('/data-recovery-service-in-kenya');

        // Schema verification
        $response->assertSee('Bayat center S25 Computer Street - Bur Dubai - Dubai.');
        $response->assertSee('"@type": "ProfessionalService"', false);
    }

    /**
     * Test other branches render their specific verified contact details.
     */
    public function test_other_branches_render_verified_details(): void
    {
        // Al Barsha
        $barsha = $this->get('/data-recovery-al-barsha');
        $barsha->assertStatus(200);
        $barsha->assertSee('DasselDorf Business Point, AL Barsha 1, Behind Mall Of Emirates, Dubai, United Arab Emirates.');
        $barsha->assertSee('+971 56 410 1040');
        $barsha->assertSee('Specialized Data Recovery Services at this Location');
        $barsha->assertSee('/data-recovery-dubai');

        // Abu Dhabi
        $ad = $this->get('/data-recovery-abu-dhabi');
        $ad->assertStatus(200);
        $ad->assertSee('Electra Street, 11 Al Raqqay St - Al Danah - E10 - Abu Dhabi.');
        $ad->assertSee('+971 50 217 6793');

        // Sharjah
        $shj = $this->get('/data-recovery-sharjah');
        $shj->assertStatus(200);
        $shj->assertSee('Office 1202-4, Avenue Business Centre, Al Majaz Waterfront, Buhaira Corniche, Sharjah.');
        $shj->assertSee('+971 50 634 3878');

        // Al Wasl
        $wasl = $this->get('/data-recovery-al-wasl');
        $wasl->assertStatus(200);
        $wasl->assertSee('RAG Biz HQ, Addiyar Bldg - Office M17 Sheikh Zayed Rd - Al Wasl - Dubai - UAE.');
        $wasl->assertSee('+971 56 997 8107');

        // India
        $ind = $this->get('/data-recovery-india');
        $ind->assertStatus(200);
        $ind->assertSee('404, 4th Floor, A Block, CTC Park Ln, Sarojini Devi Rd, Secunderabad, Telangana 500003.');
        $ind->assertSee('+91 89775 44544');
        $ind->assertSee('040-6638 6333');
        $ind->assertSee('lifeguarddatarecovery.in');

        // Kenya
        $ken = $this->get('/data-recovery-service-in-kenya');
        $ken->assertStatus(200);
        $ken->assertSee('Mirage Tower, 13th Floor, Nairobi, Kenya.');
        $ken->assertSee('+254 748 020 399');
        $ken->assertSee('lifeguarddatarecovery.co.ke');
    }
}

<?php

namespace App\Services;

class LocationService
{
    /**
     * Master configuration of official LifeGuard Data Recovery branch locations.
     * Transcribed directly from verified physical offices and cleanroom laboratories.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'dubai' => [
                'id' => 'dubai',
                'slug' => 'data-recovery-dubai',
                'aliases' => ['data-recovery-bur-dubai'],
                'name' => 'Dubai (Main Office)',
                'full_name' => 'LifeGuard Data Recovery Dubai (Main Office - Bur Dubai)',
                'headline' => 'Class 100 Cleanroom Certified Data Recovery Laboratory in Bur Dubai',
                'country' => 'United Arab Emirates',
                'country_code' => 'AE',
                'flag' => '🇦🇪',
                'is_headquarters' => true,
                'office_title' => 'Dubai (Main Office)',
                'address' => 'Bayat center S25 Computer Street - Bur Dubai - Dubai.',
                'city' => 'Dubai',
                'region' => 'Bur Dubai',
                'postal_code' => '00000',
                'phone' => '+971 55 483 8545',
                'phone_link' => '+971554838545',
                'hotline' => '+971 43 933 082',
                'hotline_link' => '+97143933082',
                'fax' => '+971 43 933 082',
                'email' => 'info@lifeguarddatarecovery.com',
                'whatsapp' => '+971 55 483 8545',
                'whatsapp_link' => 'https://wa.me/971554838545',
                'website' => 'lifeguarddatarecovery.com',
                'website_url' => 'https://lifeguarddatarecovery.com',
                'hours' => 'Mon - Sat: 9:00 AM - 9:00 PM | 24/7 Emergency Lab Dispatch',
                'landmark' => 'Bayat Center S25, Computer Street (Opposite Al Ain Center), Bur Dubai',
                'tag' => 'Global Lab Headquarters',
                'badge' => 'Main Cleanroom Laboratory',
                'description' => 'Our primary cleanroom recovery lab in Bur Dubai is equipped with ISO-5 Class 100 laminar workstations, Rusolut NAND readers, and hardware PC-3000 systems. Dedicated drop-off counter and immediate emergency diagnostic triage.',
                'areas' => [
                    'Bur Dubai', 'Deira', 'Al Karama', 'Oud Metha', 'Al Fahidi', 'Al Raffa',
                    'Mankhool', 'Port Rashid', 'Al Rigga', 'Dubai Creek', 'Al Garhoud', 'Al Jaddaf',
                ],
                'latitude' => 25.2532,
                'longitude' => 55.2974,
            ],
            'al-barsha' => [
                'id' => 'al-barsha',
                'slug' => 'data-recovery-al-barsha',
                'aliases' => [],
                'name' => 'Al Barsha, Dubai',
                'full_name' => 'LifeGuard Data Recovery Al Barsha Branch',
                'headline' => 'Express Data Recovery Drop-Off & Rapid Diagnostic Center in Al Barsha 1',
                'country' => 'United Arab Emirates',
                'country_code' => 'AE',
                'flag' => '🇦🇪',
                'is_headquarters' => false,
                'office_title' => 'Al Barsha, Dubai',
                'address' => 'DasselDorf Business Point, AL Barsha 1, Behind Mall Of Emirates, Dubai, United Arab Emirates.',
                'city' => 'Dubai',
                'region' => 'Al Barsha 1',
                'postal_code' => '00000',
                'phone' => '+971 56 410 1040',
                'phone_link' => '+971564101040',
                'hotline' => '+971 43 933 082',
                'hotline_link' => '+97143933082',
                'fax' => null,
                'email' => 'info@lifeguarddatarecovery.com',
                'whatsapp' => '+971 56 410 1040',
                'whatsapp_link' => 'https://wa.me/971564101040',
                'website' => 'lifeguarddatarecovery.com',
                'website_url' => 'https://lifeguarddatarecovery.com',
                'hours' => 'Mon - Sat: 9:30 AM - 8:30 PM (Sunday On-Call)',
                'landmark' => 'Dusseldorf Business Point, Al Barsha 1 (Directly behind Mall of the Emirates)',
                'tag' => 'Al Barsha 1 Hub',
                'badge' => 'Express Diagnostic Branch',
                'description' => 'Strategically situated behind Mall of the Emirates in Al Barsha 1, offering rapid turnaround diagnostics, device drop-off, and free express courier dispatch across New Dubai.',
                'areas' => [
                    'Al Barsha 1', 'Al Barsha 2', 'Al Barsha 3', 'Al Barsha South', 'Barsha Heights (Tecom)',
                    'Dubai Marina', 'Jumeirah Lake Towers (JLT)', 'Palm Jumeirah', 'Dubai Media City',
                    'Dubai Internet City', 'Al Sufouh', 'The Greens', 'Jumeirah Village Circle (JVC)',
                ],
                'latitude' => 25.1182,
                'longitude' => 55.2001,
            ],
            'abu-dhabi' => [
                'id' => 'abu-dhabi',
                'slug' => 'data-recovery-abu-dhabi',
                'aliases' => ['data-recovery-in-abu-dhabi', 'data-recovery-abu-dhabi-2'],
                'name' => 'Abu Dhabi',
                'full_name' => 'LifeGuard Data Recovery Abu Dhabi Regional Hub',
                'headline' => 'Capital City Enterprise & Government-Grade Data Recovery in Abu Dhabi',
                'country' => 'United Arab Emirates',
                'country_code' => 'AE',
                'flag' => '🇦🇪',
                'is_headquarters' => false,
                'office_title' => 'Abu Dhabi',
                'address' => 'Electra Street, 11 Al Raqqay St - Al Danah - E10 - Abu Dhabi.',
                'city' => 'Abu Dhabi',
                'region' => 'Al Danah - E10',
                'postal_code' => '00000',
                'phone' => '+971 50 217 6793',
                'phone_link' => '+971502176793',
                'hotline' => '+971 43 933 082',
                'hotline_link' => '+97143933082',
                'fax' => null,
                'email' => 'info@lifeguarddatarecovery.com',
                'whatsapp' => '+971 50 217 6793',
                'whatsapp_link' => 'https://wa.me/971502176793',
                'website' => 'lifeguarddatarecovery.com',
                'website_url' => 'https://lifeguarddatarecovery.com',
                'hours' => 'Mon - Sat: 9:00 AM - 8:30 PM (24/7 Hotline)',
                'landmark' => 'Electra Street, 11 Al Raqqay Street, Al Danah E10 Sector',
                'tag' => 'Abu Dhabi Capital Hub',
                'badge' => 'Capital Regional Operations',
                'description' => 'Dedicated facility in the heart of Abu Dhabi catering to corporate, government, enterprise RAID systems, and critical hard drive recovery with free courier pickup across the capital.',
                'areas' => [
                    'Al Danah', 'Electra Street', 'Corniche', 'Al Reem Island', 'Al Maryah Island',
                    'Al Khalidiya', 'Musaffah Industrial', 'Khalifa City', 'Yas Island', 'Al Raha Beach',
                    'Mohammed Bin Zayed City', 'Al Bateen', 'Saadiyat Island',
                ],
                'latitude' => 24.4925,
                'longitude' => 54.3688,
            ],
            'sharjah' => [
                'id' => 'sharjah',
                'slug' => 'data-recovery-sharjah',
                'aliases' => [],
                'name' => 'Sharjah',
                'full_name' => 'LifeGuard Data Recovery Sharjah Regional Branch',
                'headline' => 'Professional Data Recovery Services at Al Majaz Waterfront, Sharjah',
                'country' => 'United Arab Emirates',
                'country_code' => 'AE',
                'flag' => '🇦🇪',
                'is_headquarters' => false,
                'office_title' => 'Sharjah',
                'address' => 'Office 1202-4, Avenue Business Centre, Al Majaz Waterfront, Buhaira Corniche, Sharjah.',
                'city' => 'Sharjah',
                'region' => 'Al Majaz Waterfront',
                'postal_code' => '00000',
                'phone' => '+971 50 634 3878',
                'phone_link' => '+971506343878',
                'hotline' => '+971 43 933 082',
                'hotline_link' => '+97143933082',
                'fax' => null,
                'email' => 'info@lifeguarddatarecovery.com',
                'whatsapp' => '+971 50 634 3878',
                'whatsapp_link' => 'https://wa.me/971506343878',
                'website' => 'lifeguarddatarecovery.com',
                'website_url' => 'https://lifeguarddatarecovery.com',
                'hours' => 'Mon - Sat: 9:00 AM - 8:00 PM',
                'landmark' => 'Avenue Business Centre, 12th Floor, Al Majaz Waterfront overlooking Buhaira Corniche',
                'tag' => 'Northern Emirates Hub',
                'badge' => 'Sharjah & Northern Hub',
                'description' => 'Conveniently located at Al Majaz Waterfront overlooking Buhaira Corniche, providing premier data recovery for industrial, commercial, and personal storage media across Sharjah and the Northern Emirates.',
                'areas' => [
                    'Al Majaz 1, 2, 3', 'Buhaira Corniche', 'Al Nahda Sharjah', 'Al Qasimia', 'Muwailih',
                    'Sharjah Industrial Areas 1-17', 'Al Taawun', 'Al Khan', 'Ajman', 'Umm Al Quwain',
                ],
                'latitude' => 25.3283,
                'longitude' => 55.3857,
            ],
            'al-wasl' => [
                'id' => 'al-wasl',
                'slug' => 'data-recovery-al-wasl',
                'aliases' => ['data-recovery-sheikh-zayed-road', 'sheikh-zayed-road'],
                'name' => 'Al Wasl, Dubai',
                'full_name' => 'LifeGuard Data Recovery Al Wasl (Sheikh Zayed Road)',
                'headline' => 'Sheikh Zayed Road Corporate Data Recovery Center at RAG Biz HQ',
                'country' => 'United Arab Emirates',
                'country_code' => 'AE',
                'flag' => '🇦🇪',
                'is_headquarters' => false,
                'office_title' => 'Al Wasl, Dubai',
                'address' => 'RAG Biz HQ, Addiyar Bldg - Office M17 Sheikh Zayed Rd - Al Wasl - Dubai - UAE.',
                'city' => 'Dubai',
                'region' => 'Al Wasl / Sheikh Zayed Road',
                'postal_code' => '00000',
                'phone' => '+971 56 997 8107',
                'phone_link' => '+971569978107',
                'hotline' => '+971 43 933 082',
                'hotline_link' => '+97143933082',
                'fax' => null,
                'email' => 'info@lifeguarddatarecovery.com',
                'whatsapp' => '+971 56 997 8107',
                'whatsapp_link' => 'https://wa.me/971569978107',
                'website' => 'lifeguarddatarecovery.com',
                'website_url' => 'https://lifeguarddatarecovery.com',
                'hours' => 'Mon - Sat: 9:00 AM - 8:00 PM',
                'landmark' => 'Addiyar Building, Office M17, Sheikh Zayed Road, Al Wasl area',
                'tag' => 'Sheikh Zayed Road Hub',
                'badge' => 'Corporate SZR Business Center',
                'description' => 'Positioned right on Sheikh Zayed Road in Al Wasl at RAG Biz HQ, delivering high-speed emergency data retrieval for businesses, financial firms in DIFC, Downtown Dubai, and Business Bay.',
                'areas' => [
                    'Al Wasl', 'Sheikh Zayed Road (SZR)', 'Downtown Dubai', 'Business Bay', 'DIFC',
                    'City Walk', 'Al Satwa', 'Jumeirah 1, 2, 3', 'Al Safa', 'Trade Centre 1 & 2',
                ],
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ],
            'india' => [
                'id' => 'india',
                'slug' => 'data-recovery-india',
                'aliases' => [],
                'name' => 'India',
                'full_name' => 'LifeGuard Data Recovery India (Secunderabad / Hyderabad)',
                'headline' => 'State-of-the-Art Hard Drive, SSD & NAND Flash Recovery Hub in Telangana, India',
                'country' => 'India',
                'country_code' => 'IN',
                'flag' => '🇮🇳',
                'is_headquarters' => false,
                'office_title' => 'India',
                'address' => '404, 4th Floor, A Block, CTC Park Ln, Sarojini Devi Rd, Secunderabad, Telangana 500003.',
                'city' => 'Secunderabad',
                'region' => 'Telangana',
                'postal_code' => '500003',
                'phone' => '+91 89775 44544',
                'phone_link' => '+918977544544',
                'hotline' => '+91 89775 44544',
                'hotline_link' => '+918977544544',
                'fax' => '040-6638 6333',
                'email' => 'info@lifeguarddatarecovery.in',
                'whatsapp' => '+91 89775 44544',
                'whatsapp_link' => 'https://wa.me/918977544544',
                'website' => 'lifeguarddatarecovery.in',
                'website_url' => 'https://lifeguarddatarecovery.in',
                'hours' => 'Mon - Sat: 10:00 AM - 7:30 PM (IST)',
                'landmark' => '4th Floor, A Block, CTC Park Lane, Sarojini Devi Road, Secunderabad',
                'tag' => 'India Technology Center',
                'badge' => 'India Subcontinent Operations',
                'description' => 'Our specialized Indian subcontinent facility in CTC Park Lane Secunderabad provides chip-off NAND recovery, degraded drive rebuilds, and PAN-India courier pickup for IT enterprises and consumers.',
                'areas' => [
                    'Secunderabad', 'Hyderabad', 'HITEC City', 'Gachibowli', 'Begumpet',
                    'Banjara Hills', 'Jubilee Hills', 'Madhapur', 'Kondapur', 'Telangana', 'Andhra Pradesh', 'Pan-India',
                ],
                'latitude' => 17.4399,
                'longitude' => 78.4983,
            ],
            'kenya' => [
                'id' => 'kenya',
                'slug' => 'data-recovery-service-in-kenya',
                'aliases' => ['data-recovery-kenya'],
                'name' => 'Kenya',
                'full_name' => 'LifeGuard Data Recovery Kenya (Nairobi)',
                'headline' => 'Premier Cleanroom Data Recovery Lab in Mirage Tower, Nairobi, Kenya',
                'country' => 'Kenya',
                'country_code' => 'KE',
                'flag' => '🇰🇪',
                'is_headquarters' => false,
                'office_title' => 'Kenya',
                'address' => 'Mirage Tower, 13th Floor, Nairobi, Kenya.',
                'city' => 'Nairobi',
                'region' => 'Westlands',
                'postal_code' => '00100',
                'phone' => '+254 748 020 399',
                'phone_link' => '+254748020399',
                'hotline' => '+254 748 020 399',
                'hotline_link' => '+254748020399',
                'fax' => null,
                'email' => 'info@lifeguarddatarecovery.co.ke',
                'whatsapp' => '+254 748 020 399',
                'whatsapp_link' => 'https://wa.me/254748020399',
                'website' => 'lifeguarddatarecovery.co.ke',
                'website_url' => 'https://lifeguarddatarecovery.co.ke',
                'hours' => 'Mon - Fri: 8:30 AM - 5:30 PM | Sat: 9:00 AM - 2:00 PM (EAT)',
                'landmark' => '13th Floor, Mirage Tower, Chiromo Road, Westlands, Nairobi',
                'tag' => 'East Africa Regional Hub',
                'badge' => 'East Africa Operations',
                'description' => 'Located on the 13th Floor of the prestigious Mirage Tower in Westlands, Nairobi, serving Kenya and East Africa with high-tier data recovery solutions for enterprise RAID, servers, and damaged media.',
                'areas' => [
                    'Nairobi', 'Westlands', 'Kilimani', 'Upper Hill', 'Nairobi CBD',
                    'Parklands', 'Karen', 'Lavington', 'Industrial Area', 'Mombasa', 'Kisumu', 'East Africa',
                ],
                'latitude' => -1.2673,
                'longitude' => 36.8049,
            ],
        ];
    }

    /**
     * Find a location by key or slug (including aliases).
     *
     * @return array<string, mixed>|null
     */
    public static function find(string $slugOrKey): ?array
    {
        $all = self::all();

        // 1. Direct match on key
        if (isset($all[$slugOrKey])) {
            return $all[$slugOrKey];
        }

        // 2. Match on slug or alias
        foreach ($all as $loc) {
            if ($loc['slug'] === $slugOrKey) {
                return $loc;
            }
            if (! empty($loc['aliases']) && in_array($slugOrKey, $loc['aliases'], true)) {
                return $loc;
            }
        }

        return null;
    }

    /**
     * Determine whether the given slug corresponds to an official branch location.
     */
    public static function isLocationSlug(string $slug): bool
    {
        return self::find($slug) !== null;
    }

    /**
     * Get the other branches for inter-branch cross-linking.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getOtherLocations(string $currentSlugOrKey): array
    {
        $current = self::find($currentSlugOrKey);
        $currentId = $current['id'] ?? $currentSlugOrKey;

        return array_filter(self::all(), fn ($loc) => $loc['id'] !== $currentId);
    }

    /**
     * Get full catalog of 10 Data Recovery Services with icons, descriptions, and internal URLs.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getServicesCatalog(): array
    {
        return [
            [
                'title' => 'Hard Drive Data Recovery',
                'slug' => 'hard-drive-data-recovery-dubai',
                'url' => url('hard-drive-data-recovery-dubai'),
                'icon' => 'hard-drive',
                'category' => 'Mechanical & External HDDs',
                'turnaround' => '24 - 48 Hours (12h Emergency)',
                'symptoms' => 'Clicking, beeping, grinding noise, motor seizure, dropped drives, bad sectors, undetected disks.',
                'description' => 'Comprehensive recovery for 2.5" & 3.5" desktop and portable hard drives. Performed inside ISO-5 Class 100 cleanrooms using precision head-stack replacement, donor parts matching, platter swaps, and PC-3000 firmware microcode rebuilding.',
                'brands' => 'Seagate, Western Digital, Toshiba, Hitachi, Samsung, LaCie, G-Technology',
            ],
            [
                'title' => 'SSD & NVMe Data Recovery',
                'slug' => 'ssd-drive-data-recovery-in-dubai',
                'url' => url('ssd-drive-data-recovery-in-dubai'),
                'icon' => 'cpu',
                'category' => 'Solid State & NAND Flash',
                'turnaround' => '24 - 48 Hours',
                'symptoms' => 'Drive shows 0 MB or incorrect capacity, controller dead, firmware lock, completely unresponsive, sudden read-only mode.',
                'description' => 'Direct flash memory chip extraction, hardware controller bypass, and translator table reconstruction using PC-3000 SSD systems. We handle SATA, M.2 NVMe, PCIe, and soldered motherboard storage without risking memory cell degradation.',
                'brands' => 'Samsung 980/990, Crucial, SanDisk, Kingston, WD Black, Intel, Micron',
            ],
            [
                'title' => 'RAID Server Data Recovery',
                'slug' => 'raid-data-recovery-service-dubai',
                'url' => url('raid-data-recovery-service-dubai'),
                'icon' => 'server',
                'category' => 'Enterprise Multi-Disk Arrays',
                'turnaround' => '24 - 72 Hours (24/7 Rapid Response)',
                'symptoms' => 'Multiple drive failures, degraded RAID array, failed rebuild attempt, configuration loss, corrupted controller card.',
                'description' => 'Advanced virtual array reconstruction for RAID 0, RAID 1, RAID 5, RAID 6, RAID 10, RAID 50, and hybrid storage pools. Every drive is sector-cloned first before virtual assembly to protect original drives from further damage.',
                'brands' => 'Dell PowerEdge, HPE Smart Array, LSI MegaRAID, Synology SHR, Cisco UCS',
            ],
            [
                'title' => 'NAS Storage Recovery',
                'slug' => 'nas-data-recovery-dubai',
                'url' => url('nas-data-recovery-dubai'),
                'icon' => 'network',
                'category' => 'Network Attached Storage',
                'turnaround' => '24 - 48 Hours',
                'symptoms' => 'Volume unmounted, blinking amber alert LED, corrupted Btrfs/EXT4 file system, snapshot deletion, failed firmware update.',
                'description' => 'Dedicated recovery for multi-bay corporate and home NAS appliances. We reconstruct custom Linux MDADM RAID, proprietary metadata, and snapshot trees to extract lost shares, databases, and media folders intact.',
                'brands' => 'Synology DiskStation, QNAP Turbo NAS, Asustor, WD My Cloud, Netgear ReadyNAS, TrueNAS',
            ],
            [
                'title' => 'Enterprise Server Recovery',
                'slug' => 'server-data-recovery-dubai',
                'url' => url('server-data-recovery-dubai'),
                'icon' => 'layers',
                'category' => 'Datacenters & Virtual Machines',
                'turnaround' => '24 - 48 Hours (Priority Dispatch)',
                'symptoms' => 'VMware ESXi VMFS crash, Hyper-V VHDX corrupted, Microsoft Exchange dirty shutdown, SQL Server database suspect mode.',
                'description' => 'Forensic recovery for enterprise production servers, SAN/DAS storage arrays, and virtualized workloads. We extract unbootable virtual disks (VMDK, VHD, QCOW2) and recover raw SQL/Oracle database tables.',
                'brands' => 'Dell EMC, HPE ProLiant, Lenovo ThinkSystem, IBM xSeries, Supermicro',
            ],
            [
                'title' => 'CCTV Footage & DVR Recovery',
                'slug' => 'cctv-footage-recovery-dubai',
                'url' => url('cctv-footage-recovery-dubai'),
                'icon' => 'video',
                'category' => 'Surveillance Security Systems',
                'turnaround' => '24 - 48 Hours',
                'symptoms' => 'Deleted surveillance video, overwritten timestamps, uninitialized DVR hard disk, water-damaged recorder, power spike failure.',
                'description' => 'Forensic frame-by-frame parsing of proprietary CCTV video streams (H.264, H.265, DAV, MP4). We extract missing or damaged camera feeds from standalone DVR/NVR hard drives for legal, corporate, and private security verification.',
                'brands' => 'Hikvision, Dahua, CP Plus, Samsung Techwin, Uniview, Bosch, Tiandy',
            ],
            [
                'title' => 'SD Card & USB Flash Recovery',
                'slug' => 'sd-card-data-recovery',
                'url' => url('sd-card-data-recovery'),
                'icon' => 'disc',
                'category' => 'Removable Flash Media',
                'turnaround' => '12 - 36 Hours',
                'symptoms' => 'USB not recognized, "Please format disk before using", broken connector, raw file system, sudden capacity drop.',
                'description' => 'Micro-soldering and monolith chip-off recovery for camera SD cards, microSD chips, and USB thumb drives. We bypass broken physical interfaces to tap into internal memory layers and reconstruct raw files.',
                'brands' => 'SanDisk Extreme, Lexar Professional, Sony Tough, Samsung EVO, Kingston, Transcend',
            ],
            [
                'title' => 'iPhone & Mobile Phone Recovery',
                'slug' => 'iphone-data-recovery-dubai',
                'url' => url('iphone-data-recovery-dubai'),
                'icon' => 'smartphone',
                'category' => 'iOS & Android Smart Devices',
                'turnaround' => '24 - 48 Hours',
                'symptoms' => 'Liquid/water damage, motherboard short circuit, Apple logo bootloop, completely unresponsive dead phone, cracked board.',
                'description' => 'Level 4 component-level micro-soldering on device logic boards. We repair damaged power rails, CPU/PMIC circuits, and extract encrypted device data directly to external encrypted storage.',
                'brands' => 'Apple iPhone (all series), iPad, Samsung Galaxy S/Z, Google Pixel, OnePlus',
            ],
            [
                'title' => 'Ransomware Data Recovery',
                'slug' => 'ransomware-data-recovery',
                'url' => url('ransomware-data-recovery'),
                'icon' => 'shield-alert',
                'category' => 'Cybersecurity Incident Recovery',
                'turnaround' => 'Emergency Rapid Assessment',
                'symptoms' => 'Files locked with unfamiliar extensions (.lockbit, .phobos, .mallox, .djvu), ransom note left, database service stopped.',
                'description' => 'Forensic analysis of encrypted file systems and databases. We utilize private key repositories, volume shadow reconstruction, hex-level fragmented record parsing, and database table rebuilding to retrieve corporate assets safely.',
                'brands' => 'Windows Server, Linux clusters, MS SQL databases, QuickBooks, ERP systems',
            ],
            [
                'title' => 'MacBook & Apple Mac Recovery',
                'slug' => 'mac-data-recovery',
                'url' => url('mac-data-recovery'),
                'icon' => 'laptop',
                'category' => 'Apple Silicon & T2 Computers',
                'turnaround' => '24 - 48 Hours',
                'symptoms' => 'Flashing question mark folder, liquid spill on MacBook, dead M1/M2/M3 logic board with soldered SSD, APFS volume corruption.',
                'description' => 'Specialized recovery for modern Apple laptops with soldered-on NAND chips and hardware encryption (T2, M1, M2, M3, M4). We bring dead logic boards back to life temporarily to extract the APFS encrypted volume safely.',
                'brands' => 'MacBook Pro, MacBook Air, iMac, Mac Studio, Mac Mini, Mac Pro',
            ],
        ];
    }

    /**
     * Get list of other UAE Emirates / Neighborhood location pages in database for wide internal linking.
     *
     * @return array<int, array<string, string>>
     */
    public static function getUaeAreaHubs(): array
    {
        return [
            ['title' => 'Bur Dubai Central Lab', 'slug' => 'data-recovery-dubai', 'url' => url('data-recovery-dubai')],
            ['title' => 'Al Barsha & MOE', 'slug' => 'data-recovery-al-barsha', 'url' => url('data-recovery-al-barsha')],
            ['title' => 'Abu Dhabi Capital Hub', 'slug' => 'data-recovery-abu-dhabi', 'url' => url('data-recovery-abu-dhabi')],
            ['title' => 'Sharjah & Northern Emirates', 'slug' => 'data-recovery-sharjah', 'url' => url('data-recovery-sharjah')],
            ['title' => 'Al Wasl & Sheikh Zayed Road', 'slug' => 'data-recovery-al-wasl', 'url' => url('data-recovery-al-wasl')],
            ['title' => 'Deira Dubai', 'slug' => 'data-recovery-deira', 'url' => url('data-recovery-deira')],
            ['title' => 'Business Bay & Downtown', 'slug' => 'data-recovery-sheikh-zayed-road', 'url' => url('data-recovery-sheikh-zayed-road')],
            ['title' => 'Dubai Marina', 'slug' => 'data-recovery-dubai-marina', 'url' => url('data-recovery-dubai-marina')],
            ['title' => 'Jumeirah Lake Towers (JLT)', 'slug' => 'data-recovery-jumeirah-lake-tower', 'url' => url('data-recovery-jumeirah-lake-tower')],
            ['title' => 'Ajman', 'slug' => 'data-recovery-ajman', 'url' => url('data-recovery-ajman')],
            ['title' => 'Al Ain', 'slug' => 'data-recovery-al-ain', 'url' => url('data-recovery-al-ain')],
            ['title' => 'Ras Al Khaimah', 'slug' => 'data-recovery-ras-al-khaimah', 'url' => url('data-recovery-ras-al-khaimah')],
            ['title' => 'Fujairah', 'slug' => 'data-recovery-fujairah', 'url' => url('data-recovery-fujairah')],
            ['title' => 'Umm Al Quwain', 'slug' => 'data-recovery-umm-al-quwain', 'url' => url('data-recovery-umm-al-quwain')],
        ];
    }

    /**
     * Generate Schema.org LocalBusiness / EmergencyService JSON-LD for a location.
     *
     * @param  array<string, mixed>  $location
     * @return array<string, mixed>
     */
    public static function getSchema(array $location): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'LifeGuard Data Recovery - '.$location['name'],
            'legalName' => 'LifeGuard Data Recovery LLC',
            'url' => url($location['slug']),
            'telephone' => $location['phone_link'],
            'email' => $location['email'],
            'priceRange' => $location['country_code'] === 'AE' ? 'AED' : ($location['country_code'] === 'IN' ? 'INR' : 'KES'),
            'image' => url('/images/logo.png'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $location['address'],
                'addressLocality' => $location['city'],
                'addressRegion' => $location['region'],
                'postalCode' => $location['postal_code'] ?? '00000',
                'addressCountry' => $location['country_code'],
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens' => '09:00',
                    'closes' => '21:00',
                ],
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    'opens' => '00:00',
                    'closes' => '23:59',
                    'description' => 'Emergency Cleanroom Data Recovery Hotline',
                ],
            ],
            'areaServed' => $location['areas'],
        ];

        if (! empty($location['latitude']) && ! empty($location['longitude'])) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
            ];
        }

        if (! empty($location['fax'])) {
            $schema['faxNumber'] = $location['fax'];
        }

        return $schema;
    }
}

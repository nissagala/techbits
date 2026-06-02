<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('categories')->pluck('id', 'name');

        $products = [
            // Keyboards (4 products)
            ['name' => 'Logitech MK270 Wireless Keyboard', 'sku' => 'KB-001', 'cat' => 'Keyboards', 'price' => 4500, 'stock' => 25, 'featured' => true, 'active' => true, 'short' => 'Reliable wireless keyboard for everyday use.', 'desc' => 'The Logitech MK270 delivers a comfortable typing experience with a full-size layout and reliable 2.4GHz wireless connectivity. Ideal for home and office use.', 'specs' => [['Connectivity', 'Wireless 2.4GHz'], ['Battery Life', '24 months'], ['Layout', 'Full-size']]],
            ['name' => 'Redragon K552 Mechanical Gaming Keyboard', 'sku' => 'KB-002', 'cat' => 'Keyboards', 'price' => 8900, 'stock' => 12, 'featured' => true, 'active' => true, 'short' => 'Compact tenkeyless mechanical keyboard for gamers.', 'desc' => 'The Redragon K552 features tactile and clicky blue mechanical switches in a compact tenkeyless layout. RGB backlit keys and durable aluminium construction make it a favourite among gamers.', 'specs' => [['Switch Type', 'Blue Mechanical'], ['Backlight', 'RGB'], ['Form Factor', 'Tenkeyless']]],
            ['name' => 'HP K1000 Wired USB Keyboard', 'sku' => 'KB-003', 'cat' => 'Keyboards', 'price' => 2200, 'stock' => 40, 'featured' => false, 'active' => true, 'short' => 'Budget-friendly wired keyboard for daily tasks.', 'desc' => 'A straightforward full-size USB keyboard from HP offering quiet membrane keys, a spill-resistant design, and broad OS compatibility.', 'specs' => [['Connectivity', 'USB Wired'], ['Key Type', 'Membrane'], ['OS Support', 'Windows / Linux']]],
            ['name' => 'Keychron K2 Wireless Mechanical Keyboard', 'sku' => 'KB-004', 'cat' => 'Keyboards', 'price' => 18500, 'stock' => 0, 'featured' => false, 'active' => true, 'short' => 'Premium wireless mechanical keyboard for professionals.', 'desc' => 'The Keychron K2 supports both Bluetooth 5.1 and USB-C wired mode. With a 75% compact layout and hot-swappable switches, it is the go-to keyboard for professionals who demand quality.', 'specs' => [['Connectivity', 'Bluetooth 5.1 / USB-C'], ['Hot-swap', 'Yes'], ['Battery', '4000 mAh']]],

            // Mice (4 products)
            ['name' => 'Logitech M185 Wireless Mouse', 'sku' => 'MS-001', 'cat' => 'Mice', 'price' => 2800, 'stock' => 50, 'featured' => true, 'active' => true, 'short' => 'Compact and reliable wireless mouse for everyday computing.', 'desc' => 'The Logitech M185 is a compact wireless mouse with a nano receiver and up to 12 months battery life. Its plug-and-forget connectivity makes it perfect for travel and office use.', 'specs' => [['Connectivity', 'Wireless 2.4GHz'], ['DPI', '1000'], ['Battery', 'AA (12 months)']]],
            ['name' => 'Razer DeathAdder Essential Gaming Mouse', 'sku' => 'MS-002', 'cat' => 'Mice', 'price' => 7500, 'stock' => 8, 'featured' => false, 'active' => true, 'short' => 'Ergonomic gaming mouse with 6400 DPI optical sensor.', 'desc' => 'The Razer DeathAdder Essential combines an ergonomic right-handed design with a high-precision 6400 DPI optical sensor. Durable mechanical switches rated for 10 million clicks.', 'specs' => [['DPI', '6400'], ['Buttons', '5'], ['Switch Life', '10 million clicks']]],
            ['name' => 'HP X1000 USB Optical Mouse', 'sku' => 'MS-003', 'cat' => 'Mice', 'price' => 1200, 'stock' => 3, 'featured' => false, 'active' => true, 'short' => 'Simple plug-and-play USB mouse for basic use.', 'desc' => 'The HP X1000 is a no-frills USB optical mouse suitable for everyday computing. Symmetrical design fits both left and right-handed users.', 'specs' => [['DPI', '800 / 1600'], ['Connectivity', 'USB Wired'], ['Buttons', '3']]],
            ['name' => 'Logitech MX Master 3S Wireless Mouse', 'sku' => 'MS-004', 'cat' => 'Mice', 'price' => 22000, 'stock' => 5, 'featured' => true, 'active' => true, 'short' => 'Advanced wireless mouse for power users and creatives.', 'desc' => 'The Logitech MX Master 3S features an ultra-fast MagSpeed electromagnetic scroll wheel, 8000 DPI sensor, and multi-device Bluetooth connectivity. Designed for professionals working with large datasets, design software, and creative tools.', 'specs' => [['DPI', '200–8000'], ['Connectivity', 'Bluetooth / USB-C'], ['Battery Life', '70 days']]],

            // RAM (3 products)
            ['name' => 'Kingston 8GB DDR4 3200MHz RAM', 'sku' => 'RAM-001', 'cat' => 'RAM', 'price' => 6500, 'stock' => 30, 'featured' => false, 'active' => true, 'short' => '8GB DDR4 module for desktop PCs.', 'desc' => 'Kingston ValueRAM 8GB DDR4-3200 offers reliable performance for everyday computing and light multitasking. Compatible with most Intel and AMD desktop platforms.', 'specs' => [['Capacity', '8GB'], ['Type', 'DDR4'], ['Speed', '3200MHz'], ['Form Factor', 'DIMM']]],
            ['name' => 'Corsair Vengeance 16GB DDR4 3600MHz RAM Kit', 'sku' => 'RAM-002', 'cat' => 'RAM', 'price' => 14500, 'stock' => 15, 'featured' => true, 'active' => true, 'short' => 'High-performance 2x8GB DDR4 kit for gaming and content creation.', 'desc' => 'Corsair Vengeance LPX 16GB (2x8GB) DDR4-3600 delivers exceptional bandwidth for gaming rigs and workstations. Low-profile aluminium heat spreader ensures stable temperatures during demanding tasks.', 'specs' => [['Capacity', '16GB (2x8GB)'], ['Type', 'DDR4'], ['Speed', '3600MHz'], ['Latency', 'CL18']]],
            ['name' => 'Samsung 32GB DDR5 4800MHz RAM', 'sku' => 'RAM-003', 'cat' => 'RAM', 'price' => 32000, 'stock' => 0, 'featured' => false, 'active' => true, 'short' => 'Next-gen 32GB DDR5 module for the latest platforms.', 'desc' => 'Samsung DDR5 32GB module at 4800MHz is designed for 12th/13th-gen Intel and AMD Ryzen 7000 platforms. Ideal for AI workloads, 3D rendering, and extreme multitasking.', 'specs' => [['Capacity', '32GB'], ['Type', 'DDR5'], ['Speed', '4800MHz'], ['Form Factor', 'DIMM']]],

            // Storage (4 products)
            ['name' => 'Samsung 870 EVO 500GB SATA SSD', 'sku' => 'SSD-001', 'cat' => 'Storage', 'price' => 12000, 'stock' => 20, 'featured' => true, 'active' => true, 'short' => 'Fast and reliable 500GB SATA SSD from Samsung.', 'desc' => 'The Samsung 870 EVO offers sequential read speeds of up to 560 MB/s and 530 MB/s write, making it a significant upgrade over traditional hard drives. Backed by a 5-year warranty.', 'specs' => [['Capacity', '500GB'], ['Interface', 'SATA III'], ['Read Speed', '560 MB/s'], ['Write Speed', '530 MB/s']]],
            ['name' => 'WD Blue 1TB 2.5" HDD', 'sku' => 'HDD-001', 'cat' => 'Storage', 'price' => 8500, 'stock' => 35, 'featured' => false, 'active' => true, 'short' => '1TB laptop hard drive with reliable performance.', 'desc' => 'The Western Digital Blue 1TB 2.5" hard drive offers ample storage for laptops and desktops. Suitable for bulk data storage at a budget-friendly price.', 'specs' => [['Capacity', '1TB'], ['Interface', 'SATA III'], ['RPM', '5400'], ['Cache', '128MB']]],
            ['name' => 'Kingston NV2 250GB M.2 NVMe SSD', 'sku' => 'NVME-001', 'cat' => 'Storage', 'price' => 9500, 'stock' => 2, 'featured' => false, 'active' => true, 'short' => 'Compact M.2 NVMe SSD for ultra-fast boot times.', 'desc' => 'The Kingston NV2 250GB M.2 2280 NVMe SSD delivers read speeds up to 3000 MB/s, perfect for OS boot drives and performance-critical applications.', 'specs' => [['Capacity', '250GB'], ['Interface', 'PCIe 4.0 NVMe M.2'], ['Read Speed', '3000 MB/s'], ['Write Speed', '1300 MB/s']]],
            ['name' => 'Seagate Expansion 2TB USB Portable HDD', 'sku' => 'EXT-001', 'cat' => 'Storage', 'price' => 14000, 'stock' => 18, 'featured' => false, 'active' => false, 'short' => 'Compact 2TB portable hard drive for backups.', 'desc' => 'The Seagate Expansion 2TB portable drive connects via USB 3.0 for fast file transfer. No power adapter required — bus-powered for complete portability.', 'specs' => [['Capacity', '2TB'], ['Interface', 'USB 3.0'], ['Power', 'Bus-powered']]],

            // Monitors (3 products)
            ['name' => 'LG 24" Full HD IPS Monitor', 'sku' => 'MON-001', 'cat' => 'Monitors', 'price' => 48000, 'stock' => 10, 'featured' => true, 'active' => true, 'short' => '24-inch Full HD IPS monitor with wide viewing angles.', 'desc' => 'The LG 24MP400 features an IPS panel delivering accurate colours and 178° viewing angles. Three-side virtually borderless design and AMD FreeSync support at 75Hz.', 'specs' => [['Size', '24 inch'], ['Resolution', '1920x1080 FHD'], ['Panel', 'IPS'], ['Refresh Rate', '75Hz']]],
            ['name' => 'Samsung 27" QHD 165Hz Gaming Monitor', 'sku' => 'MON-002', 'cat' => 'Monitors', 'price' => 95000, 'stock' => 4, 'featured' => true, 'active' => true, 'short' => '27-inch QHD 165Hz gaming monitor with 1ms response time.', 'desc' => 'The Samsung Odyssey G5 delivers a curved QHD display at 165Hz with 1ms GTG response time. Ideal for competitive gaming and immersive single-player experiences.', 'specs' => [['Size', '27 inch'], ['Resolution', '2560x1440 QHD'], ['Refresh Rate', '165Hz'], ['Response Time', '1ms GTG']]],
            ['name' => 'AOC 21.5" Full HD LED Monitor', 'sku' => 'MON-003', 'cat' => 'Monitors', 'price' => 32000, 'stock' => 22, 'featured' => false, 'active' => true, 'short' => 'Affordable 21.5-inch FHD display for home and office.', 'desc' => 'The AOC 22B2H is a slim, budget-friendly 21.5" Full HD monitor ideal for office productivity and casual home use. Features VGA and HDMI inputs.', 'specs' => [['Size', '21.5 inch'], ['Resolution', '1920x1080 FHD'], ['Panel', 'VA'], ['Ports', 'HDMI, VGA']]],

            // Headsets & Audio (3 products)
            ['name' => 'HyperX Cloud Stinger Wireless Gaming Headset', 'sku' => 'AUD-001', 'cat' => 'Headsets & Audio', 'price' => 18500, 'stock' => 7, 'featured' => true, 'active' => true, 'short' => 'Lightweight wireless gaming headset with swivel-to-mute mic.', 'desc' => 'The HyperX Cloud Stinger Wireless features 7.1 virtual surround sound, a 90-degree swivel-to-mute microphone, and up to 20 hours battery life. Compatible with PC and PlayStation.', 'specs' => [['Connectivity', 'Wireless 2.4GHz'], ['Driver Size', '40mm'], ['Battery Life', '20 hours'], ['Surround Sound', 'Virtual 7.1']]],
            ['name' => 'JBL Tune 510BT Wireless Headphones', 'sku' => 'AUD-002', 'cat' => 'Headsets & Audio', 'price' => 8900, 'stock' => 14, 'featured' => false, 'active' => true, 'short' => 'On-ear Bluetooth headphones with JBL Pure Bass Sound.', 'desc' => 'The JBL Tune 510BT delivers powerful JBL Pure Bass Sound and up to 40 hours of battery life. Foldable design and multi-point connection for up to two devices simultaneously.', 'specs' => [['Connectivity', 'Bluetooth 5.0'], ['Battery Life', '40 hours'], ['Driver Size', '32mm'], ['Foldable', 'Yes']]],
            ['name' => 'Logitech H111 Stereo Headset', 'sku' => 'AUD-003', 'cat' => 'Headsets & Audio', 'price' => 2500, 'stock' => 60, 'featured' => false, 'active' => true, 'short' => 'Basic stereo headset with noise-cancelling microphone.', 'desc' => 'The Logitech H111 is a budget-friendly on-ear headset with a noise-cancelling boom microphone and a 3.5mm single plug. Ideal for video calls and online classes.', 'specs' => [['Connectivity', '3.5mm'], ['Mic', 'Noise-cancelling boom'], ['Plug Type', 'Single 3.5mm']]],

            // Webcams (3 products)
            ['name' => 'Logitech C920 HD Pro Webcam', 'sku' => 'WEB-001', 'cat' => 'Webcams', 'price' => 22000, 'stock' => 9, 'featured' => true, 'active' => true, 'short' => '1080p Full HD webcam with stereo audio.', 'desc' => 'The Logitech C920 delivers crisp 1080p/30fps video and stereo audio, making it the industry standard for professional video calls, streaming, and content creation.', 'specs' => [['Resolution', '1080p @ 30fps'], ['Autofocus', 'Yes'], ['Microphone', 'Dual stereo'], ['Field of View', '78°']]],
            ['name' => 'Razer Kiyo 720p Webcam with Ring Light', 'sku' => 'WEB-002', 'cat' => 'Webcams', 'price' => 16500, 'stock' => 0, 'featured' => false, 'active' => true, 'short' => '720p webcam with built-in adjustable ring light.', 'desc' => 'The Razer Kiyo features a built-in LED ring light for even, flattering illumination without external lighting. Ideal for streamers and video creators working in low-light environments.', 'specs' => [['Resolution', '720p @ 60fps / 1080p @ 30fps'], ['Ring Light', 'Yes, adjustable'], ['Interface', 'USB-A']]],
            ['name' => 'HP w200 720p HD Webcam', 'sku' => 'WEB-003', 'cat' => 'Webcams', 'price' => 4500, 'stock' => 28, 'featured' => false, 'active' => true, 'short' => 'Affordable 720p plug-and-play webcam for video calls.', 'desc' => 'The HP w200 is a compact, no-frills 720p USB webcam suitable for video conferencing on a budget. Clip-on design fits most monitors and laptops.', 'specs' => [['Resolution', '720p @ 30fps'], ['Microphone', 'Built-in'], ['Interface', 'USB-A']]],

            // Cables & Adapters (3 products)
            ['name' => 'Anker USB-C to USB-A Cable 1.8m', 'sku' => 'CBL-001', 'cat' => 'Cables & Adapters', 'price' => 1500, 'stock' => 100, 'featured' => false, 'active' => true, 'short' => 'Braided USB-C to USB-A cable for charging and data.', 'desc' => 'Premium nylon braided USB-C to USB-A cable rated for 60W charging and USB 3.0 data speeds up to 5 Gbps. 1.8m length for flexible placement.', 'specs' => [['Length', '1.8m'], ['Power Delivery', '60W'], ['Data Speed', 'USB 3.0 5Gbps'], ['Material', 'Braided Nylon']]],
            ['name' => 'uni HDMI to DisplayPort Adapter', 'sku' => 'CBL-002', 'cat' => 'Cables & Adapters', 'price' => 2800, 'stock' => 1, 'featured' => false, 'active' => true, 'short' => 'Active HDMI to DisplayPort adapter supporting 4K@60Hz.', 'desc' => 'Active chipset-based adapter converting HDMI output to DisplayPort input. Supports 4K 60Hz and HDR pass-through for connecting laptops or gaming consoles to DP monitors.', 'specs' => [['Input', 'HDMI 2.0'], ['Output', 'DisplayPort 1.2'], ['Max Resolution', '4K @ 60Hz'], ['HDR', 'Yes']]],
            ['name' => 'TP-Link UH400 4-Port USB 3.0 Hub', 'sku' => 'CBL-003', 'cat' => 'Cables & Adapters', 'price' => 3200, 'stock' => 45, 'featured' => false, 'active' => true, 'short' => 'Compact 4-port USB 3.0 hub for expanding connectivity.', 'desc' => 'The TP-Link UH400 adds four USB 3.0 ports to any laptop or desktop. Supports simultaneous 5Gbps data transfer on all ports and is compatible with Windows, macOS, and Linux.', 'specs' => [['Ports', '4x USB-A 3.0'], ['Data Speed', '5Gbps per port'], ['Power', 'Bus-powered'], ['Cable Length', '15cm']]],

            // Laptop Accessories (4 products)
            ['name' => 'Targus 15.6" Laptop Backpack', 'sku' => 'LAP-001', 'cat' => 'Laptop Accessories', 'price' => 6800, 'stock' => 20, 'featured' => false, 'active' => true, 'short' => 'Padded 15.6-inch laptop backpack with multiple compartments.', 'desc' => 'The Targus Classic Backpack fits laptops up to 15.6 inches with a dedicated padded compartment, accessory pockets, and a scratch-resistant base for durability.', 'specs' => [['Fits Laptops Up To', '15.6 inch'], ['Material', 'Polyester'], ['Weight', '0.5 kg']]],
            ['name' => 'UGREEN 9-in-1 USB-C Hub for Laptops', 'sku' => 'LAP-002', 'cat' => 'Laptop Accessories', 'price' => 12500, 'stock' => 11, 'featured' => true, 'active' => true, 'short' => '9-in-1 USB-C docking hub with HDMI, SD, USB-A, and PD.', 'desc' => 'Expand your laptop connectivity with this 9-in-1 USB-C hub: 4K HDMI, 3x USB-A 3.0, SD/TF card readers, USB-C PD charging, and Gigabit Ethernet. Ideal for MacBook and Windows ultrabooks.', 'specs' => [['Ports', 'HDMI, 3x USB-A, SD, TF, USB-C PD, Ethernet'], ['HDMI Output', '4K @ 30Hz'], ['PD Charging', 'Up to 100W']]],
            ['name' => 'Kensington Laptop Stand K52788WW', 'sku' => 'LAP-003', 'cat' => 'Laptop Accessories', 'price' => 7200, 'stock' => 16, 'featured' => false, 'active' => true, 'short' => 'Adjustable aluminium laptop stand for ergonomic working.', 'desc' => 'The Kensington SmartFit Easy Riser stand holds laptops up to 14 inches and tilts at 6° for ergonomic posture improvement. Foldable and lightweight for portability.', 'specs' => [['Compatible Laptop Size', 'Up to 14 inch'], ['Material', 'Aluminium'], ['Adjustable', 'Yes, 6°']]],
            ['name' => 'Havit HV-F2056 Laptop Cooling Pad', 'sku' => 'LAP-004', 'cat' => 'Laptop Accessories', 'price' => 3500, 'stock' => 33, 'featured' => false, 'active' => true, 'short' => 'Slim laptop cooling pad with three quiet fans.', 'desc' => 'The Havit HV-F2056 features three 110mm fans for efficient cooling of laptops up to 17 inches. USB-powered with two extra USB ports and an adjustable height stand.', 'specs' => [['Fan Count', '3x 110mm'], ['Compatible Size', 'Up to 17 inch'], ['Interface', 'USB-A'], ['Extra Ports', '2x USB-A']]],

            // Power & Charging (3 products)
            ['name' => 'Anker 65W GaN USB-C Charger', 'sku' => 'PWR-001', 'cat' => 'Power & Charging', 'price' => 9500, 'stock' => 25, 'featured' => true, 'active' => true, 'short' => 'Compact 65W GaN charger for laptops and phones.', 'desc' => 'The Anker 65W GaN fast charger is 50% smaller than traditional chargers. Charges a MacBook Air in 1.5 hours and an iPhone 14 in under 30 minutes. Includes USB-C to USB-C cable.', 'specs' => [['Output', '65W USB-C PD'], ['Technology', 'GaN II'], ['Ports', '1x USB-C'], ['Compatibility', 'MacBook, iPhone, Android']]],
            ['name' => 'Romoss Sense 8P 30000mAh Power Bank', 'sku' => 'PWR-002', 'cat' => 'Power & Charging', 'price' => 8200, 'stock' => 18, 'featured' => false, 'active' => true, 'short' => '30000mAh power bank with 18W fast charging.', 'desc' => 'The Romoss Sense 8P offers 30000mAh capacity to charge most smartphones 7+ times. Dual USB-A and one USB-C output with 18W PD fast charging. LED charge indicator included.', 'specs' => [['Capacity', '30000mAh'], ['Input', 'USB-C 18W'], ['Output', '2x USB-A + 1x USB-C'], ['Weight', '580g']]],
            ['name' => 'TP-Link TL-SG1005D 5-Port Gigabit Switch', 'sku' => 'PWR-003', 'cat' => 'Power & Charging', 'price' => 4800, 'stock' => 30, 'featured' => false, 'active' => true, 'short' => 'Unmanaged 5-port Gigabit Ethernet switch for home networking.', 'desc' => 'The TP-Link TL-SG1005D provides five 10/100/1000Mbps ports for wired home and office networking. Plug-and-play, fanless design with metal housing.', 'specs' => [['Ports', '5x RJ45 Gigabit'], ['Speed', '10/100/1000 Mbps'], ['Power', 'External adapter'], ['Fanless', 'Yes']]],
        ];

        foreach ($products as $p) {
            $categoryId = $categories[$p['cat']] ?? null;
            if (! $categoryId) {
                continue;
            }

            $productId = DB::table('products')->insertGetId([
                'category_id'       => $categoryId,
                'name'              => $p['name'],
                'sku'               => $p['sku'],
                'short_description' => $p['short'],
                'description'       => $p['desc'],
                'price'             => $p['price'],
                'stock'             => $p['stock'],
                'is_featured'       => $p['featured'] ? 1 : 0,
                'is_active'         => $p['active'] ? 1 : 0,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Insert specs
            foreach ($p['specs'] as $idx => $spec) {
                DB::table('product_specs')->insert([
                    'product_id' => $productId,
                    'spec_key'   => $spec[0],
                    'spec_value' => $spec[1],
                    'sort_order' => $idx,
                ]);
            }

            // Insert a placeholder primary image for every product
            DB::table('product_images')->insert([
                'product_id' => $productId,
                'path'       => 'products/placeholder.jpg',
                'is_primary' => 1,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

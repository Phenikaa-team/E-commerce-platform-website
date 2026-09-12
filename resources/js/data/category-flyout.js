// Sidebar Category Flyout Navigation Data
const categoryFlyoutData = {
    phone: {
        title: 'Điện thoại & Phụ kiện',
        subtitle: 'Khám phá thế giới công nghệ, kết nối mọi khoảnh khắc',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`,
        topCards: [
            { title: 'iPhone', image: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=300&q=80' },
            { title: 'Tai nghe TWS', image: 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=300&q=80' },
            { title: 'Ốp lưng & Bao da', image: 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=300&q=80' },
            { title: 'Sạc nhanh GaN', image: 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=300&q=80' },
            { title: 'Pin dự phòng', image: 'https://images.unsplash.com/photo-1609592424367-e9ee1e765be1?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Apple', iconHtml: `<img src="/icons/brands/apple_light.svg" alt="Apple" class="w-3.5 h-3.5 object-contain shrink-0">` },
                    { name: 'Samsung', iconHtml: `<img src="/icons/brands/samsung_default.svg" alt="Samsung" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'Xiaomi', iconHtml: `<img src="/icons/brands/xiaomi_default.svg" alt="Xiaomi" class="w-3.5 h-3.5 rounded object-contain shrink-0">` },
                    { name: 'OPPO', iconHtml: `<img src="/icons/brands/oppo_default.svg" alt="OPPO" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'vivo', iconHtml: `<img src="/icons/brands/vivo_default.svg" alt="vivo" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Sony', iconHtml: `<img src="/icons/brands/sony_mono.svg" alt="Sony" class="w-4.5 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'DÒNG SẢN PHẨM',
                items: [
                    { name: 'iPhone 15 / 15 Pro Max' },
                    { name: 'Samsung Galaxy S24 Ultra' },
                    { name: 'Xiaomi 14 / Redmi Note' },
                    { name: 'OPPO Find N3 & Reno11' },
                    { name: 'vivo X100 & V30 Pro' },
                    { name: 'Smartphone gập Flip / Fold' }
                ]
            },
            {
                heading: 'PHỤ KIỆN HOT',
                items: [
                    { name: 'Củ sạc nhanh 65W GaN' },
                    { name: 'Cáp sạc Type-C & Lightning' },
                    { name: 'Tai nghe chống ồn ANC' },
                    { name: 'Ốp lưng từ tính MagSafe' },
                    { name: 'Kính cường lực 9D' },
                    { name: 'Gimbal chống rung quay phim' }
                ]
            }
        ],
        deal: {
            badge: 'HOT DEAL',
            title: 'iPhone 15 Pro Max 256GB',
            desc: 'Hiệu năng vượt trội. Giảm thêm 3.500.000₫ hôm nay.',
            btnText: 'Mua ngay',
            image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=400&q=80'
        }
    },
    laptop: {
        title: 'Laptop & Thiết bị số',
        subtitle: 'Công cụ đắc lực cho công việc, học tập và sáng tạo không giới hạn',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`,
        topCards: [
            { title: 'MacBook M3', image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=300&q=80' },
            { title: 'Laptop Gaming', image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=300&q=80' },
            { title: 'Màn hình 4K', image: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bàn phím cơ', image: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=300&q=80' },
            { title: 'Chuột không dây', image: 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Apple MacBook', iconHtml: `<img src="/icons/brands/apple_light.svg" alt="Apple" class="w-3.5 h-3.5 object-contain shrink-0">` },
                    { name: 'ASUS ROG', iconHtml: `<img src="/icons/brands/asus_default.svg" alt="ASUS" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'Dell XPS', iconHtml: `<img src="/icons/brands/dell_default.svg" alt="Dell" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Lenovo Legion', iconHtml: `<img src="/icons/brands/lenovo_default.svg" alt="Lenovo" class="w-5 h-auto object-contain shrink-0">` },
                    { name: 'HP Pavilion', iconHtml: `<img src="/icons/brands/hp_default.svg" alt="HP" class="w-4 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'DÒNG MÁY PHỔ BIẾN',
                items: [
                    { name: 'MacBook Pro / Air M3' },
                    { name: 'Laptop Gaming RTX 40 Series' },
                    { name: 'Laptop mỏng nhẹ văn phòng' },
                    { name: 'Laptop đồ họa & Kỹ thuật' },
                    { name: 'Laptop 2-in-1 cảm ứng' },
                    { name: 'Máy trạm đồ họa chuyên nghiệp' }
                ]
            },
            {
                heading: 'PHỤ KIỆN MÁY TÍNH',
                items: [
                    { name: 'Màn hình 2K/4K 144Hz' },
                    { name: 'Bàn phím cơ Custom' },
                    { name: 'Chuột công thái học' },
                    { name: 'Hub Type-C đa năng' },
                    { name: 'Balo laptop chống sốc' },
                    { name: 'Ổ cứng SSD di động' }
                ]
            }
        ],
        deal: {
            badge: 'APPLE OFFICIAL',
            title: 'MacBook Pro 14 M3 Chip',
            desc: 'Hiệu năng đỉnh cao cho sáng tạo nội dung & lập trình.',
            btnText: 'Khám phá ngay',
            image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=400&q=80'
        }
    },
    electronics: {
        title: 'Điện tử & Điện lạnh',
        subtitle: 'Tiện nghi đẳng cấp, nâng tầm cuộc sống hiện đại cho gia đình',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>`,
        topCards: [
            { title: 'Smart Tivi 4K', image: 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=300&q=80' },
            { title: 'Tủ lạnh Inverter', image: 'https://images.unsplash.com/photo-1571175443880-49e1d25b2bc5?auto=format&fit=crop&w=300&q=80' },
            { title: 'Máy giặt sấy', image: 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?auto=format&fit=crop&w=300&q=80' },
            { title: 'Điều hòa Inverter', image: 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=300&q=80' },
            { title: 'Loa Soundbar', image: 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Sony Bravia', iconHtml: `<img src="/icons/brands/sony_mono.svg" alt="Sony" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'LG Electronics', iconHtml: `<img src="/icons/brands/lg_default.svg" alt="LG" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Samsung Digital', iconHtml: `<img src="/icons/brands/samsung_default.svg" alt="Samsung" class="w-5 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'THIẾT BỊ GIẢI TRÍ',
                items: [
                    { name: 'Tivi OLED / QLED 4K' },
                    { name: 'Loa Soundbar rạp hát' },
                    { name: 'Android TV Box 4K' },
                    { name: 'Máy chiếu mini thông minh' },
                    { name: 'Dàn âm thanh Karaoke' },
                    { name: 'Giá treo tivi di động' }
                ]
            },
            {
                heading: 'ĐIỆN LẠNH GIA ĐÌNH',
                items: [
                    { name: 'Tủ lạnh Side-by-side' },
                    { name: 'Máy giặt cửa trước Inverter' },
                    { name: 'Điều hòa tiết kiệm điện' },
                    { name: 'Máy sấy quần áo thông minh' },
                    { name: 'Máy lọc không khí HEPA' },
                    { name: 'Bình nước nóng trực tiếp' }
                ]
            }
        ],
        deal: {
            badge: 'GIẢM ĐẾN 45%',
            title: 'Smart TV QLED 65 inch 4K',
            desc: 'Đắm chìm không gian rạp chiếu phim đỉnh cao tại gia.',
            btnText: 'Xem ngay',
            image: 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=400&q=80'
        }
    },
    fashion: {
        title: 'Thời trang & Phụ kiện',
        subtitle: 'Khẳng định phong cách riêng với hàng ngàn mẫu mã dẫn đầu xu hướng',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`,
        topCards: [
            { title: 'Áo thun nam', image: 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=300&q=80' },
            { title: 'Váy đầm nữ', image: 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=300&q=80' },
            { title: 'Giày Sneaker', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80' },
            { title: 'Túi xách cao cấp', image: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồng hồ nam nữ', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU ĐÌNH ĐÁM',
                items: [
                    { name: 'Nike Sportswear', iconHtml: `<img src="/icons/brands/nike_mono.svg" alt="Nike" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Adidas Originals', iconHtml: `<img src="/icons/brands/adidas_mono.svg" alt="Adidas" class="w-4.5 h-auto object-contain shrink-0">` },
                    { name: 'Zara Man & Woman', iconHtml: `<img src="/icons/brands/zara_default.svg" alt="Zara" class="w-5 h-auto object-contain shrink-0">` }
                ]
            },
            {
                heading: 'THỜI TRANG NAM & NỮ',
                items: [
                    { name: 'Áo thun basic & Graphic' },
                    { name: 'Váy đầm dự tiệc thanh lịch' },
                    { name: 'Áo sơ mi công sở cao cấp' },
                    { name: 'Quần Jean & Quần Kaki' },
                    { name: 'Áo khoác Blazer thời thượng' },
                    { name: 'Đồ mặc nhà & Pijama' }
                ]
            },
            {
                heading: 'GIÀY DÉP & PHỤ KIỆN',
                items: [
                    { name: 'Giày Sneaker hot trend' },
                    { name: 'Túi xách & Ví da cao cấp' },
                    { name: 'Đồng hồ cơ & Smartwatch' },
                    { name: 'Mắt kính chống tia UV' },
                    { name: 'Thắt lưng da thật' },
                    { name: 'Trang sức bạc & Vàng tây' }
                ]
            }
        ],
        deal: {
            badge: 'BỘ SƯU TẬP MỚI',
            title: 'Thu Đông Capsule 2026',
            desc: 'Giảm 30% toàn bộ bộ sưu tập mới phong cách Parisian.',
            btnText: 'Khám phá ngay',
            image: 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=400&q=80'
        }
    },
    home: {
        title: 'Nhà cửa & Đời sống',
        subtitle: 'Không gian sống ấm cúng, thiết bị gia dụng tiện nghi thông minh',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>`,
        topCards: [
            { title: 'Robot hút bụi', image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nồi chiên không dầu', image: 'https://images.unsplash.com/photo-1585515320310-259814833e62?auto=format&fit=crop&w=300&q=80' },
            { title: 'Chăn ga gối nệm', image: 'https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nồi chảo nhà bếp', image: 'https://images.unsplash.com/photo-1584990347449-399eb2e0b503?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đèn decor phòng', image: 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU GIA DỤNG',
                items: [
                    { name: 'Philips Domestic' },
                    { name: 'Lock&Lock Official' },
                    { name: 'Tefal France' },
                    { name: 'Sunhouse Vietnam' },
                    { name: 'Dreame Technology' },
                    { name: 'Xiaomi SmartHome' }
                ]
            },
            {
                heading: 'THIẾT BỊ NHÀ BẾP',
                items: [
                    { name: 'Nồi chiên không dầu điện tử' },
                    { name: 'Nồi cơm điện cao tần IH' },
                    { name: 'Máy xay sinh tố đa năng' },
                    { name: 'Máy ép chậm rau củ quả' },
                    { name: 'Bếp từ đôi cảm ứng Inverter' },
                    { name: 'Bộ nồi chảo inox nguyên khối' }
                ]
            },
            {
                heading: 'NỘI THẤT & PHÒNG NGỦ',
                items: [
                    { name: 'Robot hút bụi lau nhà tự động' },
                    { name: 'Bộ drap chăn ga Tencel 60s' },
                    { name: 'Máy lọc nước RO tạo kiềm' },
                    { name: 'Máy khuếch tán tinh dầu thơm' },
                    { name: 'Đèn cây trang trí phong cách Bắc Âu' },
                    { name: 'Kệ giày thông minh đa tầng' }
                ]
            }
        ],
        deal: {
            badge: 'DEAL HỜI GIA ĐÌNH',
            title: 'Robot hút bụi lau nhà Dreame',
            desc: 'Tự động giặt sấy giẻ lau và đổ rác. Giảm sốc 2.500.000₫.',
            btnText: 'Sắm ngay',
            image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=400&q=80'
        }
    },
    beauty: {
        title: 'Làm đẹp & Sức khỏe',
        subtitle: 'Chăm sóc sắc đẹp toàn diện, mỹ phẩm chính hãng cam kết 100%',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>`,
        topCards: [
            { title: 'Serum phục hồi', image: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=300&q=80' },
            { title: 'Son môi lì', image: 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nước hoa chính hãng', image: 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=300&q=80' },
            { title: 'Kem chống nắng', image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&q=80' },
            { title: 'Máy rửa mặt', image: 'https://images.unsplash.com/photo-1512290900672-1f0284f18d79?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU UY TÍN',
                items: [
                    { name: 'Estée Lauder Paris' },
                    { name: "L'Oréal Paris" },
                    { name: 'La Roche-Posay' },
                    { name: 'MAC Cosmetics' },
                    { name: 'Laneige Korea' },
                    { name: 'Innisfree Official' }
                ]
            },
            {
                heading: 'CHĂM SÓC DA (SKINCARE)',
                items: [
                    { name: 'Serum phục hồi B5 & HA' },
                    { name: 'Kem chống nắng quang phổ rộng' },
                    { name: 'Nước tẩy trang dịu nhẹ Micellar' },
                    { name: 'Kem dưỡng ẩm chuyên sâu' },
                    { name: 'Toner cân bằng độ pH' },
                    { name: 'Mặt nạ dưỡng trắng & Cấp ẩm' }
                ]
            },
            {
                heading: 'TRANG ĐIỂM & NƯỚC HOA',
                items: [
                    { name: 'Son kem lì chuẩn màu' },
                    { name: 'Cushion mỏng nhẹ tự nhiên' },
                    { name: 'Nước hoa nam / nữ EDP' },
                    { name: 'Bảng phấn mắt đa sắc' },
                    { name: 'Chì kẻ mày & Mascara kháng nước' },
                    { name: 'Bộ cọ trang điểm chuyên nghiệp' }
                ]
            }
        ],
        deal: {
            badge: 'MALL CHÍNH HÃNG',
            title: 'Serum Estée Lauder 50ml',
            desc: 'Tái sinh làn da ban đêm số 1 thế giới. Tặng quà trị giá 600K.',
            btnText: 'Mua ngay',
            image: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80'
        }
    },
    mom: {
        title: 'Mẹ & Bé',
        subtitle: 'Đồng hành cùng sự phát triển khỏe mạnh và thông minh của bé yêu',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        topCards: [
            { title: 'Tã bỉm cho bé', image: 'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=300&q=80' },
            { title: 'Sữa bột dinh dưỡng', image: 'https://images.unsplash.com/photo-1527661591475-527312dd65f5?auto=format&fit=crop&w=300&q=80' },
            { title: 'Xe đẩy em bé', image: 'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bình sữa tiệt trùng', image: 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồ chơi giáo dục', image: 'https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Merries Japan' },
                    { name: 'Moony Natural' },
                    { name: 'Meiji Official' },
                    { name: 'Aptamil Profutura' },
                    { name: 'Pigeon Japan' },
                    { name: 'Chicco Italy' }
                ]
            },
            {
                heading: 'TÃ BỈM & DINH DƯỠNG',
                items: [
                    { name: 'Tã dán & Tã quần Merries' },
                    { name: 'Sữa bột Meiji / Aptamil Úc' },
                    { name: 'Bột & Bánh ăn dặm hữu cơ' },
                    { name: 'Bình sữa cổ rộng silicone' },
                    { name: 'Máy hâm sữa & Tiệt trùng UV' },
                    { name: 'Men vi sinh BioGaia cho bé' }
                ]
            },
            {
                heading: 'ĐỒ DÙNG & ĐỒ CHƠI',
                items: [
                    { name: 'Xe đẩy em bé siêu nhẹ du lịch' },
                    { name: 'Địu em bé trợ lực thoáng khí' },
                    { name: 'Bộ quần áo sơ sinh sợi tre' },
                    { name: 'Đồ chơi xếp hình trí tuệ Lego' },
                    { name: 'Ghế ngồi ăn dặm nâng hạ' },
                    { name: 'Thảm nằm chơi chống trượt' }
                ]
            }
        ],
        deal: {
            badge: 'FESTIVAL MẸ & BÉ',
            title: 'Tã bỉm & Sữa bột chính hãng',
            desc: 'Mua 2 tặng 1 kèm quà tặng đồ chơi trị giá 300K.',
            btnText: 'Săn quà ngay',
            image: 'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=400&q=80'
        }
    },
    sports: {
        title: 'Thể thao & Du lịch',
        subtitle: 'Rèn luyện sức khỏe, bứt phá giới hạn và chinh phục mọi cung đường',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
        topCards: [
            { title: 'Giày chạy bộ', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồ tập Gym Yoga', image: 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=300&q=80' },
            { title: 'Vợt cầu lông', image: 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=300&q=80' },
            { title: 'Balo dã ngoại', image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bình giữ nhiệt', image: 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU THỂ THAO',
                items: [
                    { name: 'Nike Running' },
                    { name: 'Adidas Training' },
                    { name: 'Yonex Japan' },
                    { name: 'Under Armour' },
                    { name: 'The North Face' },
                    { name: 'Decathlon Sport' }
                ]
            },
            {
                heading: 'TRANG PHỤC & TẬP LUYỆN',
                items: [
                    { name: 'Giày chạy bộ êm ái đàn hồi' },
                    { name: 'Bộ quần áo tập Gym thấm mồ hôi' },
                    { name: 'Thảm tập Yoga định tuyến' },
                    { name: 'Vợt cầu lông & Tennis chuyên nghiệp' },
                    { name: 'Quả bóng đá thi đấu FIFA' },
                    { name: 'Dây kháng lực & Con lăn tập bụng' }
                ]
            },
            {
                heading: 'DÃ NGOẠI & DU LỊCH',
                items: [
                    { name: 'Lều cắm trại tự bung 4 người' },
                    { name: 'Balo phượt chống nước chuyên dụng' },
                    { name: 'Bàn ghế dã ngoại gấp gọn nhôm' },
                    { name: 'Bình nước giữ nhiệt 24h inox 316' },
                    { name: 'Đèn pin cắm trại đa chế độ' },
                    { name: 'Túi ngủ giữ ấm ngoài trời' }
                ]
            }
        ],
        deal: {
            badge: 'SALE THỂ THAO',
            title: 'Giày chạy bộ Nike Air Zoom',
            desc: 'Êm ái trợ lực từng bước chạy. Giảm 35% dịp cuối tuần.',
            btnText: 'Xem chi tiết',
            image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=400&q=80'
        }
    },
    books: {
        title: 'Sách & Văn phòng phẩm',
        subtitle: 'Mở rộng tri thức, thăng hoa cảm xúc và hỗ trợ công việc hiệu quả',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>`,
        topCards: [
            { title: 'Sách kinh tế', image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=300&q=80' },
            { title: 'Tiểu thuyết văn học', image: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=300&q=80' },
            { title: 'Sổ tay bìa da', image: 'https://images.unsplash.com/photo-1586075010923-2dd4570fb338?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bút ký cao cấp', image: 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?auto=format&fit=crop&w=300&q=80' },
            { title: 'Dụng cụ vẽ tranh', image: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'NHÀ XUẤT BẢN & ĐỐI TÁC',
                items: [
                    { name: 'Nhã Nam Official' },
                    { name: 'NXB Trẻ' },
                    { name: 'NXB Kim Đồng' },
                    { name: 'Alpha Books' },
                    { name: 'First News Trí Việt' },
                    { name: 'NXB Phụ Nữ Việt Nam' }
                ]
            },
            {
                heading: 'TỦ SÁCH TINH HOA',
                items: [
                    { name: 'Sách kinh tế & Quản trị kinh doanh' },
                    { name: 'Tiểu thuyết văn học kinh điển' },
                    { name: 'Sách tâm lý học & Phát triển bản thân' },
                    { name: 'Truyện tranh Manga & Comic' },
                    { name: 'Sách học ngoại ngữ IELTS / TOEIC' },
                    { name: 'Sách thiếu nhi & Nuôi dạy con' }
                ]
            },
            {
                heading: 'VĂN PHÒNG PHẨM & SỔ TAY',
                items: [
                    { name: 'Sổ tay còng Planner bìa da' },
                    { name: 'Bút máy & Bút ký kim loại cao cấp' },
                    { name: 'Giấy note & Bút dạ quang pastel' },
                    { name: 'Bút chì màu & Màu nước vẽ tranh' },
                    { name: 'Cặp tài liệu & Bìa lá lưu hồ sơ' },
                    { name: 'Máy tính bỏ túi khoa học Casio' }
                ]
            }
        ],
        deal: {
            badge: 'HỘI SÁCH ONLINE',
            title: 'Tủ sách Best-seller Nhã Nam',
            desc: 'Giảm đồng loạt đến 40% tặng kèm Bookmark kỷ niệm.',
            btnText: 'Xem sách ngay',
            image: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=400&q=80'
        }
    },
    auto: {
        title: 'Ô tô, Xe máy & Phụ kiện',
        subtitle: 'Chăm sóc và nâng cấp xế yêu an toàn trên mọi hành trình',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 14l-2 4m0 0l-2-4m2 4V6a2 2 0 00-2-2H9a2 2 0 00-2 2v12m0 0l-2-4m2 4l2-4"/></svg>`,
        topCards: [
            { title: 'Camera hành trình', image: 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=300&q=80' },
            { title: 'Mũ bảo hiểm', image: 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=300&q=80' },
            { title: 'Dầu nhớt động cơ', image: 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bơm lốp điện tử', image: 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=300&q=80' },
            { title: 'Khử mùi ô tô', image: 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU HÀNG ĐẦU',
                items: [
                    { name: 'Vietmap Official' },
                    { name: '70mai Xiaomi' },
                    { name: 'Castrol Vietnam' },
                    { name: 'Motul High Performance' },
                    { name: 'Royal Helmet' },
                    { name: 'Michelin Automotive' }
                ]
            },
            {
                heading: 'PHỤ KIỆN Ô TÔ HIỆN ĐẠI',
                items: [
                    { name: 'Camera hành trình 4K trước sau' },
                    { name: 'Bơm lốp điện tử tự ngắt thông minh' },
                    { name: 'Tẩu sạc nhanh & Giá đỡ sạc không dây' },
                    { name: 'Thảm lót sàn ô tô 6D cao cấp' },
                    { name: 'Bọc vô lăng da Napa êm ái' },
                    { name: 'Nước hoa & Tinh dầu kẹp cửa gió' }
                ]
            },
            {
                heading: 'XE MÁY & PHỤ KIỆN PHƯỢT',
                items: [
                    { name: 'Mũ bảo hiểm 3/4 & Fullface chuẩn DOT' },
                    { name: 'Dầu nhớt xe tay ga và xe số cao cấp' },
                    { name: 'Găng tay đi phượt chống nước' },
                    { name: 'Bộ dụng cụ sửa xe lưu động' },
                    { name: 'Khóa chống trộm xe máy báo động' },
                    { name: 'Đèn LED trợ sáng bi cầu mini' }
                ]
            }
        ],
        deal: {
            badge: 'PHỤ KIỆN XẾ HỘP',
            title: 'Camera hành trình 70mai 4K',
            desc: 'Ghi hình ban đêm sắc nét. Tặng thẻ nhớ tốc độ cao 64GB.',
            btnText: 'Lắp đặt ngay',
            image: 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=400&q=80'
        }
    },
    pets: {
        title: 'Thú cưng',
        subtitle: 'Dinh dưỡng trọn vẹn và đồ dùng êm ái yêu thương Boss cưng',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>`,
        topCards: [
            { title: 'Hạt cho mèo', image: 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thức ăn cho chó', image: 'https://images.unsplash.com/photo-1548767797-d8c844163c4c?auto=format&fit=crop&w=300&q=80' },
            { title: 'Nệm ngủ thú cưng', image: 'https://images.unsplash.com/photo-1541599540903-216a46ca1dc0?auto=format&fit=crop&w=300&q=80' },
            { title: 'Đồ chơi cào móng', image: 'https://images.unsplash.com/photo-1545249390-6bdfa286032f?auto=format&fit=crop&w=300&q=80' },
            { title: 'Balo phi hành gia', image: 'https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'THƯƠNG HIỆU PET CARE',
                items: [
                    { name: 'Royal Canin France' },
                    { name: 'Whiskas Delicious' },
                    { name: 'Pedigree Pet' },
                    { name: 'Ciao Churu Japan' },
                    { name: 'Me-O Cat Food' },
                    { name: "Cat's Best Organic" }
                ]
            },
            {
                heading: 'DINH DƯỠNG THÚ CƯNG',
                items: [
                    { name: 'Hạt khô cho mèo con & Mèo lớn' },
                    { name: 'Hạt khô dinh dưỡng cho cún cưng' },
                    { name: 'Pate & Súp thưởng dinh dưỡng Ciao' },
                    { name: 'Gel dinh dưỡng & Vitamin tăng đề kháng' },
                    { name: 'Cỏ mèo tươi & Bánh thưởng sạch răng' },
                    { name: 'Sữa bột chuyên dụng cho thú cưng' }
                ]
            },
            {
                heading: 'ĐỒ DÙNG & VỆ SINH',
                items: [
                    { name: 'Cát vệ sinh đậu nành xả bồn cầu' },
                    { name: 'Bát ăn & Máy cấp nước tự động' },
                    { name: 'Balo phi hành gia thoáng khí' },
                    { name: 'Nệm ngủ bông êm ái mùa đông' },
                    { name: 'Sữa tắm mượt lông khử khuẩn' },
                    { name: 'Trụ cào móng & Cây leo Cat Tree' }
                ]
            }
        ],
        deal: {
            badge: 'FESTIVAL BOSS & SEN',
            title: 'Hạt & Pate Royal Canin',
            desc: 'Dinh dưỡng chuẩn chuyên gia. Mua 2 tặng kèm súp thưởng.',
            btnText: 'Mua cho Boss',
            image: 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?auto=format&fit=crop&w=400&q=80'
        }
    },
    global: {
        title: 'Hàng quốc tế',
        subtitle: 'Mua sắm xuyên biên giới, hàng hiệu chính ngạch giao tận cửa',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        topCards: [
            { title: 'Mỹ phẩm Hàn Quốc', image: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=300&q=80' },
            { title: 'Gia dụng Nhật Bản', image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thực phẩm chức năng', image: 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=300&q=80' },
            { title: 'Bánh kẹo xách tay', image: 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thời trang Ulzzang', image: 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'NGUỒN HÀNG NỔI BẬT',
                items: [
                    { name: 'Hàng nội địa Nhật Bản' },
                    { name: 'Mỹ phẩm chính hãng Hàn Quốc' },
                    { name: 'Thực phẩm chức năng Mỹ & Úc' },
                    { name: 'Thời trang thiết kế Quảng Châu' },
                    { name: 'Đồ điện tử mini Singapore' },
                    { name: 'Bánh kẹo Đài Loan & Thái Lan' }
                ]
            },
            {
                heading: 'MẶT HÀNG BÁN CHẠY',
                items: [
                    { name: 'Viên uống Collagen & Vitamin D3' },
                    { name: 'Son & Kem nền chuẩn Hàn' },
                    { name: 'Socola Bỉ & Bánh quy nhập khẩu' },
                    { name: 'Đồ gia dụng nội địa Nhật bền bỉ' },
                    { name: 'Quần áo & Váy hot trend Ulzzang' },
                    { name: 'Mô hình Anime & Blindbox chính hãng' }
                ]
            },
            {
                heading: 'CHÍNH SÁCH QUỐC TẾ',
                items: [
                    { name: 'Vận chuyển nhanh từ 5 - 7 ngày' },
                    { name: 'Miễn phí thủ tục thông quan' },
                    { name: 'Cam kết 100% hàng chuẩn ngoại' },
                    { name: 'Bảo hiểm đền bù thất lạc hàng' },
                    { name: 'Hỗ trợ kiểm tra hành trình đơn' },
                    { name: 'Đổi trả nếu hàng bị hư vỡ' }
                ]
            }
        ],
        deal: {
            badge: 'SIÊU HỘI XUYÊN BIÊN GIỚI',
            title: 'Hàng xách tay Mỹ & Nhật Bản',
            desc: 'Miễn phí vận chuyển quốc tế cho đơn hàng từ 250K.',
            btnText: 'Săn deal ngay',
            image: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=400&q=80'
        }
    },
    services: {
        title: 'Dịch vụ & Thẻ cào',
        subtitle: 'Nạp tiền điện thoại, thẻ game và thanh toán hóa đơn siêu tốc 24/7',
        iconSvg: `<svg class="w-5 h-5 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>`,
        topCards: [
            { title: 'Nạp thẻ điện thoại', image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=300&q=80' },
            { title: 'Thẻ Game Online', image: 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?auto=format&fit=crop&w=300&q=80' },
            { title: 'Gói cước 4G/5G', image: 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=300&q=80' },
            { title: 'Vé máy bay', image: 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=300&q=80' },
            { title: 'Voucher ăn uống', image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=300&q=80' }
        ],
        columns: [
            {
                heading: 'NẠP ĐIỆN THOẠI & DATA',
                items: [
                    { name: 'Nạp tiền Viettel chiết khấu 5%' },
                    { name: 'Nạp tiền Mobifone chiết khấu 5%' },
                    { name: 'Nạp tiền Vinaphone chiết khấu 5%' },
                    { name: 'Gói Data 4G theo ngày ST15K' },
                    { name: 'Gói Data 4G tháng không giới hạn' },
                    { name: 'Nạp thẻ Vietnamobile & Wintel' }
                ]
            },
            {
                heading: 'THẺ GAME CHIẾT KHẤU CAO',
                items: [
                    { name: 'Thẻ Garena (Liên Quân, Free Fire)' },
                    { name: 'Thẻ Zing Xu (VNG Games)' },
                    { name: 'Thẻ Vcoin & VTC Pay' },
                    { name: 'Thẻ Gate FPT' },
                    { name: 'Thẻ Steam Wallet USD / VND' },
                    { name: 'Mã thẻ nạp App Store & Google Play' }
                ]
            },
            {
                heading: 'TIỆN ÍCH HÓA ĐƠN & VÉ',
                items: [
                    { name: 'Thanh toán tiền Điện toàn quốc' },
                    { name: 'Thanh toán tiền Nước sinh hoạt' },
                    { name: 'Nạp cước Internet & Truyền hình' },
                    { name: 'Đặt vé máy bay & Phòng khách sạn' },
                    { name: 'Vé xem phim CGV, Lotte, Beta' },
                    { name: 'Voucher ẩm thực lẩu nướng giảm 50%' }
                ]
            }
        ],
        deal: {
            badge: 'CHIẾT KHẤU ĐẾN 5.5%',
            title: 'Nạp tiền điện thoại & Thẻ Game',
            desc: 'Nhận mã thẻ liền tay trong 3 giây, bảo mật tuyệt đối 100%.',
            btnText: 'Nạp ngay',
            image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=400&q=80'
        }
    }
};

// Aliases
categoryFlyoutData.pet = categoryFlyoutData.pets;
categoryFlyoutData.service = categoryFlyoutData.services;


export default categoryFlyoutData;
export { categoryFlyoutData };

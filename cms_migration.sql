-- CMS Migration Script
-- Run this ONCE against your existing al_shifah_db database

USE al_shifah_db;

-- 1. Extend page_content table
ALTER TABLE page_content 
    MODIFY COLUMN title VARCHAR(500),
    MODIFY COLUMN image_url VARCHAR(500),
    ADD COLUMN IF NOT EXISTS hero_type ENUM('image','slideshow','video') DEFAULT 'image',
    ADD COLUMN IF NOT EXISTS video_url VARCHAR(500),
    ADD COLUMN IF NOT EXISTS badge_text VARCHAR(255) DEFAULT 'Official Al-Shifah Charity Foundation',
    ADD COLUMN IF NOT EXISTS overlay_opacity VARCHAR(10) DEFAULT '0.6',
    ADD COLUMN IF NOT EXISTS btn_primary_text VARCHAR(100) DEFAULT 'Donate Now',
    ADD COLUMN IF NOT EXISTS btn_primary_url VARCHAR(255) DEFAULT 'donate.php',
    ADD COLUMN IF NOT EXISTS btn_primary_color VARCHAR(20) DEFAULT 'emerald',
    ADD COLUMN IF NOT EXISTS btn_secondary_text VARCHAR(100) DEFAULT 'Our Campaigns',
    ADD COLUMN IF NOT EXISTS btn_secondary_url VARCHAR(255) DEFAULT 'campaigns.php';

-- Update existing hero rows with defaults
UPDATE page_content SET 
    hero_type='image',
    badge_text='Official Al-Shifah Charity Foundation',
    overlay_opacity='0.6',
    btn_primary_text='Donate Now',
    btn_primary_url='donate.php',
    btn_secondary_text='Our Campaigns',
    btn_secondary_url='campaigns.php'
WHERE section_name='hero';

-- Add contact hero page
INSERT IGNORE INTO page_content (page_name, section_name, title, subtitle, image_url, hero_type, badge_text, btn_primary_text, btn_primary_url, btn_secondary_text, btn_secondary_url) VALUES 
('contact', 'hero', 'Get In Touch With Us', 'Reach out with your questions, partnership proposals, or to learn more about our humanitarian programs.', 'https://images.unsplash.com/photo-1588681664899-f142ff2dc9b1?auto=format&fit=crop&q=80&w=1600', 'image', 'Contact Al-Shifah', 'Join Us', 'login.php', 'View Campaigns', 'campaigns.php');

-- 2. Add team member columns
ALTER TABLE team_members
    ADD COLUMN IF NOT EXISTS bio TEXT,
    ADD COLUMN IF NOT EXISTS photo_url VARCHAR(500);

-- 3. Create hero_slides table
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) NOT NULL DEFAULT 'home',
    image_url VARCHAR(500) NOT NULL,
    caption VARCHAR(500),
    link_url VARCHAR(255),
    order_rank INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO hero_slides (page_name, image_url, caption, order_rank) VALUES
('home', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=1600', 'Changing lives in Northern Nigeria.', 1),
('home', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=1600', 'Education programs for orphans and youth.', 2),
('home', 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=1600', 'WASH & health infrastructure for communities.', 3);

-- 4. Create homepage_sections table
CREATE TABLE IF NOT EXISTS homepage_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) UNIQUE NOT NULL,
    label VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    is_visible BOOLEAN DEFAULT TRUE,
    order_rank INT DEFAULT 0
);

INSERT IGNORE INTO homepage_sections (section_key, label, description, is_visible, order_rank) VALUES
('hero',      'Hero / Banner',        'The top hero area of the homepage',             1, 1),
('tracker',   'Impact Tracker',       'Fundraising progress bar & totals',             1, 2),
('stats',     'Impact Statistics',    'Key numbers: kids, families, meals reached',    1, 3),
('mission',   'Mission Summary',      'Why We Exist — the two-column text + image',    1, 4),
('campaigns', 'Active Campaigns',     'Featured campaign cards',                       1, 5),
('gallery',   'Photo Gallery',        'Visual gallery grid from field activities',     1, 6),
('blog',      'Latest Blog Posts',    'Latest news & insights cards',                  1, 7);

-- 5. Create content_blocks table
CREATE TABLE IF NOT EXISTS content_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) NOT NULL,
    block_key VARCHAR(100) NOT NULL,
    block_value TEXT,
    UNIQUE KEY uq_block (section_key, block_key)
);

INSERT IGNORE INTO content_blocks (section_key, block_key, block_value) VALUES
('tracker',   'heading',           'Collective Progress'),
('tracker',   'subheading',        'Tracking every Naira donated toward our community essential goals.'),
('tracker',   'badge_label',       'Live Impact Tracker'),
('mission',   'badge',             'Why We Exist'),
('mission',   'heading',           'Establishing services within the community to make a difference.'),
('mission',   'paragraph',         'Our mission is to render charitable and humanitarian services geared toward supporting the needy and less privileged members of the community through the provision of basic needs.'),
('mission',   'cta_text',          'Learn about our Constitution'),
('mission',   'cta_url',           'about.php'),
('mission',   'image_url',         'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=800'),
('mission',   'quote',             '"We firmly resolve to provide for ourselves a constitution and to be governed by service."'),
('campaigns', 'heading',           'Active Support Programs'),
('campaigns', 'subheading',        'Targeted initiatives providing immediate and long-term relief.'),
('campaigns', 'cta_text',          'View All'),
('gallery',   'badge',             'Our Visual Impact'),
('gallery',   'heading',           'Field Update Gallery'),
('gallery',   'subheading',        'A window into our work across Nigeria, establishing services and providing relief materials.'),
('blog',      'heading',           'Latest Insights'),
('blog',      'subheading',        'In-depth stories about our programs and constitutional achievements.'),
('blog',      'cta_text',          'Read All Updates'),
('about',     'intro_badge',       'Official Constitution: Article 1'),
('about',     'intro_heading',     'A Not-for-Profit & Non-Political Organisation.'),
('about',     'intro_quote',       '"We, the members of AL-SHIFAH CHARITY FOUNDATION... firmly and solemnly resolve to provide for ourselves a constitution and to be governed by the provisions therein contained."'),
('about',     'intro_paragraph',   'Based in Damaturu, Nigeria, Al-Shifah Charity Foundation is committed to humanitarian excellence.'),
('about',     'hq_label',          'Headquarters'),
('about',     'governance_heading','Governing Body'),
('about',     'governance_body',   'As per Article 7, the general administration is governed by the Incorporated Trustees.'),
('about',     'bullet_1',          'Audited annually by independent licenced Auditors.'),
('about',     'bullet_2',          '2/3 majority votes required for key trustee decisions.'),
('about',     'bullet_3',          'Zero profit distribution to members (Article 13).'),
('contact',   'heading',           'Get In Touch'),
('contact',   'subheading',        'Reach out with your questions, partnership proposals, or to learn more.'),
('contact',   'form_heading',      'Send Us a Message'),
('footer',    'tagline',           'Committed to humanitarian excellence and community service.');

-- 7. Admin Roles
ALTER TABLE admins ADD COLUMN IF NOT EXISTS role ENUM('super_admin', 'admin') DEFAULT 'admin';
UPDATE admins SET role = 'super_admin' WHERE username = 'admin';

-- 6. Create contact_settings table
CREATE TABLE IF NOT EXISTS contact_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT
);

INSERT IGNORE INTO contact_settings (setting_key, setting_value) VALUES
('email',        'al.shifahcharityfoundation@gmail.com'),
('phone_1',      '+234 913 856 3760'),
('phone_2',      '+234 706 536 4835'),
('address',      'No. 07, Khadi Bulama Street, Commissioners Quarters, Damaturu, Yobe State, Nigeria'),
('map_embed',    'https://maps.google.com/maps?q=Damaturu,+Yobe+State,+Nigeria&output=embed&z=13'),
('office_hours', 'Mon - Fri: 9:00 AM – 5:00 PM');
